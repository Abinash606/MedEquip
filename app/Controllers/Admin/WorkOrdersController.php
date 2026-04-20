<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use Config\Database;

class WorkOrdersController extends BaseController
{
    protected WorkOrderModel $workOrders;

    public function __construct()
    {
        $this->workOrders = new WorkOrderModel();
    }

    private function wantsJson(): bool
    {
        return $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
    }

    private function normalizeStatus(?string $status): string
    {
        $key = strtolower(trim((string) $status));
        $key = str_replace(['-', ' '], '_', $key);

        return match ($key) {
            'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            'in_progress', 'inprogress' => 'in_progress',
            default => 'open',
        };
    }

    private function normalizePriority(?string $priority): string
    {
        $key = strtolower(trim((string) $priority));

        return match ($key) {
            'low' => 'low',
            'high', 'critical' => 'high',
            default => 'normal',
        };
    }

    private function resolveEquipmentReference(int $companyId, int $siteId, $postedEquipmentId): ?array
    {
        $postedEquipmentId = (int) $postedEquipmentId;
        if ($postedEquipmentId <= 0) {
            return null;
        }

        $db = Database::connect();

        $siteEq = $db->table('site_equipment')
            ->select('id, master_equipment_id, asset_tag, serial_number, make, model, device_type')
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('id', $postedEquipmentId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($siteEq) {
            // Fall back to site_equipment.id when no master link exists
            // so work orders can still be created for site-only equipment.
            return [
                'site_equipment_id' => (int) $siteEq['id'],
                'equipment_id'      => !empty($siteEq['master_equipment_id'])
                    ? (int) $siteEq['master_equipment_id']
                    : (int) $siteEq['id'],
                'asset_tag'         => $siteEq['asset_tag'] ?? '',
                'serial_number'     => $siteEq['serial_number'] ?? '',
                'make'              => $siteEq['make'] ?? '',
                'model'             => $siteEq['model'] ?? '',
                'device_type'       => $siteEq['device_type'] ?? '',
            ];
        }

        $master = $db->table('equipment')
            ->select('id, asset_tag, serial_number, make, model, device_type')
            ->where('company_id', $companyId)
            ->where('id', $postedEquipmentId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($master) {
            $siteCopy = $db->table('site_equipment')
                ->select('id')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('master_equipment_id', (int) $master['id'])
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            return [
                'site_equipment_id' => !empty($siteCopy['id']) ? (int) $siteCopy['id'] : null,
                'equipment_id'      => (int) $master['id'],
                'asset_tag'         => $master['asset_tag'] ?? '',
                'serial_number'     => $master['serial_number'] ?? '',
                'make'              => $master['make'] ?? '',
                'model'             => $master['model'] ?? '',
                'device_type'       => $master['device_type'] ?? '',
            ];
        }

        return null;
    }

    private function fetchWorkOrderRow(int $companyId, int $id): ?array
    {
        $db = Database::connect();

        $row = $db->table('work_orders')
            ->select("
                work_orders.*,
                tech_user.full_name AS assigned_to_name
            ")
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users AS tech_user', 'tech_user.id = technicians.user_id', 'left')
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.id', $id)
            ->where('work_orders.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $siteId = (int) ($row['site_id'] ?? 0);
        $eqRef  = null;

        if (!empty($row['equipment_id'])) {
            $eqRef = $this->resolveEquipmentReference($companyId, $siteId, (int) $row['equipment_id']);
        }

        $row['site_equipment_id'] = $eqRef['site_equipment_id'] ?? null;
        $row['asset_tag']         = $eqRef['asset_tag'] ?? '';
        $row['serial_number']     = $eqRef['serial_number'] ?? '';
        $row['make']              = $eqRef['make'] ?? '';
        $row['model']             = $eqRef['model'] ?? '';
        $row['device_type']       = $eqRef['device_type'] ?? '';

        return $row;
    }

    public function index()
    {
        $companyId = (int) $this->session->get('company_id');
        $db = Database::connect();

        $workOrders = $db->query("
            SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date,
                   s.name AS site_name, c.name AS customer_name, u.full_name AS tech_name
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

    public function show($id)
    {
        $companyId = (int) $this->session->get('company_id');
        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        return $this->response->setJSON([
            'success' => (bool) $row,
            'data'    => $row,
            'message' => $row ? '' : 'Work order not found.',
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/sites');
        }

        $companyId = (int) $this->session->get('company_id');
        $siteId    = (int) $this->request->getPost('site_id');
        $title     = trim((string) $this->request->getPost('title'));
        $groupId   = trim((string) $this->request->getPost('group_id'));

        if ($siteId <= 0 || $title === '') {
            $msg = 'Site and title are required.';
            return $this->wantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->with('error', $msg);
        }

        $eqRef = $this->resolveEquipmentReference($companyId, $siteId, $this->request->getPost('equipment_id'));
        if (!$eqRef) {
            $msg = 'Equipment not found for this site.';
            return $this->wantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->withInput()->with('error', $msg);
        }

        $data = [
            'company_id'   => $companyId,
            'site_id'      => $siteId,
            'equipment_id' => (int) $eqRef['equipment_id'],
            'title'        => $title,
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->normalizeStatus($this->request->getPost('status')),
            'priority'     => $this->normalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'group_id'     => $groupId !== '' ? $groupId : null,
        ];

        $existing = null;
        if ($groupId !== '') {
            $existing = $this->workOrders
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('equipment_id', (int) $eqRef['equipment_id'])
                ->where('group_id', $groupId)
                ->where('deleted_at', null)
                ->first();
        }

        if ($existing) {
            $this->workOrders->update((int) $existing['id'], array_merge($data, ['updated_at' => date('Y-m-d H:i:s')]));
            $id = (int) $existing['id'];
        } else {
            $this->workOrders->insert($data);
            $id = (int) $this->workOrders->getInsertID();
        }

        $row = $this->fetchWorkOrderRow($companyId, $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => $existing ? 'Work order updated successfully' : 'Work order created successfully',
                'work_order_id'    => $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/sites/' . $siteId)->with('success', $existing ? 'Work order updated.' : 'Work order created.');
    }

    public function update($id)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }

        $companyId = (int) $this->session->get('company_id');
        $existingRow = $this->fetchWorkOrderRow($companyId, (int) $id);

        if (!$existingRow) {
            $msg = 'Work order not found.';
            return $this->wantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->with('error', $msg);
        }

        $siteId = (int) ($this->request->getPost('site_id') ?: $existingRow['site_id']);
        $postedEquipmentId = $this->request->getPost('equipment_id');

        if ((int) $postedEquipmentId > 0) {
            $eqRef = $this->resolveEquipmentReference($companyId, $siteId, $postedEquipmentId);
            if (!$eqRef) {
                $msg = 'Equipment not found for this site.';
                return $this->wantsJson()
                    ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                    : redirect()->back()->withInput()->with('error', $msg);
            }
            $equipmentId = (int) $eqRef['equipment_id'];
        } else {
            $equipmentId = (int) $existingRow['equipment_id'];
        }

        $data = [
            'title'        => trim((string) $this->request->getPost('title')),
            'equipment_id' => $equipmentId,
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->normalizeStatus($this->request->getPost('status')),
            'priority'     => $this->normalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $groupId = trim((string) $this->request->getPost('group_id'));
        if ($groupId !== '') {
            $data['group_id'] = $groupId;
        }

        $this->workOrders->update((int) $id, $data);
        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => 'Work order updated successfully',
                'work_order_id'    => (int) $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/sites/' . $siteId)->with('success', 'Work order updated.');
    }

    public function delete($id)
    {
        $companyId = (int) $this->session->get('company_id');

        $exists = $this->workOrders
            ->where('company_id', $companyId)
            ->where('id', (int) $id)
            ->where('deleted_at', null)
            ->first();

        if (!$exists) {
            if ($this->wantsJson()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Work order not found.']);
            }
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $this->workOrders->delete((int) $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'       => true,
                'message'       => 'Work order deleted successfully',
                'work_order_id' => (int) $id,
                'csrf_hash'     => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', 'Work order deleted.');
    }
}