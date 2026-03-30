<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\SiteModel;

class WorkOrdersController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        $db = \Config\Database::connect();

        $workOrders = $db->query("
            SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date,
                   s.name AS site_name, c.name AS customer_name,
                   u.full_name AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id = wo.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = wo.assigned_to
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE wo.company_id = ? AND wo.deleted_at IS NULL
            ORDER BY wo.created_at DESC
        ", [$companyId])->getResultArray();

        return view('admin/work_orders/index', ['workOrders' => $workOrders]);
    }

     public function create()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/sites');
        }

            $companyId = $this->session->get('company_id');
            $workOrderModel = new WorkOrderModel();

            $data = [
            'company_id'  => $companyId,
            'site_id'     => $this->request->getPost('site_id'),
            'title'       => $this->request->getPost('title'),
            'equipment_id'=> $this->request->getPost('equipment_id') ?: null,
            'status'      => $this->request->getPost('status')   ?: 'open',
            'priority'    => $this->request->getPost('priority') ?: 'medium',
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date')   ?: null,
            'description' => $this->request->getPost('description') ?? '',
            'group_id'    => $this->request->getPost('group_id') ?: null,
        ];

        $inserted = $workOrderModel->insert($data);

        // Determine if client expects JSON (AJAX or Accept header)
        $wantsJson = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;

        if ($wantsJson) {
            if ($inserted) {
                $woId = $workOrderModel->getInsertID();
                // Gather basic equipment details for convenience
                $assetTag = '';
                $serialNo = '';
                if (!empty($data['equipment_id'])) {
                    $eqModel = new \App\Models\EquipmentModel();
                    $eqRow   = $eqModel->find($data['equipment_id']);
                    if ($eqRow) {
                        $assetTag = $eqRow['asset_tag'] ?? '';
                        $serialNo = $eqRow['serial_number'] ?? '';
                    }
                }
                return $this->response->setJSON([
                    'success'            => true,
                    'message'            => 'Work order created successfully',
                    'work_order_id'      => $woId,
                    'title'              => (string) $data['title'],
                    'priority'           => (string) $data['priority'],
                    'status'             => (string) $data['status'],
                    'assigned_to_name'   => !empty($data['assigned_to']) ? (string) $data['assigned_to'] : 'N/A',
                    'start_date'         => (string) $data['start_date'],
                    'end_date'           => (string) $data['end_date'],
                    'description'        => (string) $data['description'],
                    'asset_tag'          => $assetTag,
                    'serial_number'      => $serialNo,
                    'group_id'           => (string) ($data['group_id'] ?? ''),
                    'csrf_hash'          => csrf_hash(),
                ]);
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create work order',
            ]);
        }

        // Non-AJAX: redirect to site page
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }

    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $companyId = (int) $this->session->get('company_id');
            $workOrderModel = new WorkOrderModel();
            $data = [
                'company_id'  => $companyId,
                'title'       => $this->request->getPost('title'),
                'equipment_id'=> $this->request->getPost('equipment_id') ?: null,
                'status'      => $this->request->getPost('status'),
                'priority'    => $this->request->getPost('priority'),
                'assigned_to' => $this->request->getPost('assigned_to'),
                'start_date'  => $this->request->getPost('start_date'),
                'end_date'    => $this->request->getPost('end_date'),
                'description' => $this->request->getPost('description'),
            ];
            $updated = $workOrderModel->update($id, $data);

            // Determine if client expects JSON
            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
                || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
            if ($wantsJson) {
                if ($updated !== false) {
                    // gather equipment info
                    $assetTag = '';
                    $serialNo = '';
                    if (!empty($data['equipment_id'])) {
                        $eqModel = new \App\Models\EquipmentModel();
                        $eqRow   = $eqModel->find($data['equipment_id']);
                        if ($eqRow) {
                            $assetTag = $eqRow['asset_tag'] ?? '';
                            $serialNo = $eqRow['serial_number'] ?? '';
                        }
                    }
                    return $this->response->setJSON([
                        'success'          => true,
                        'message'          => 'Work order updated successfully',
                        'work_order_id'    => (int) $id,
                        'title'            => (string) $data['title'],
                        'priority'         => (string) $data['priority'],
                        'status'           => (string) $data['status'],
                        'assigned_to_name' => !empty($data['assigned_to']) ? (string) $data['assigned_to'] : 'N/A',
                        'start_date'       => (string) $data['start_date'],
                        'end_date'         => (string) $data['end_date'],
                        'description'      => (string) $data['description'],
                        'asset_tag'        => $assetTag,
                        'serial_number'    => $serialNo,
                        'csrf_hash'        => csrf_hash(),
                    ]);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update work order',
                ]);
            }

            // Non-AJAX: redirect to site page
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $workOrderModel = new WorkOrderModel();
        $deleted = $workOrderModel->delete($id);
        // Determine if client expects JSON
        $wantsJson = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
        if ($wantsJson) {
            return $this->response->setJSON([
                'success' => (bool) $deleted,
                'message' => $deleted ? 'Work order deleted' : 'Failed to delete work order',
                'work_order_id' => (int) $id,
            ]);
        }
        return redirect()->back();
    }

}