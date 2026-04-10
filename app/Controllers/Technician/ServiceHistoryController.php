<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\EquipmentModel;
use App\Models\SiteModel;
use App\Models\UserModel;
use Config\Database;

class ServiceHistoryController extends BaseController
{
    public function index()
    {
        $db        = Database::connect();
        $companyId = $this->session->get('company_id');
        $userId    = $this->session->get('user_id');

        $equipmentModel = new EquipmentModel();
        $siteModel      = new SiteModel();
        $userModel      = new UserModel();
        $workOrderModel = new WorkOrderModel();

        // Current user
        $user = $userModel->find($userId);
        $data['current_user'] = [
            'id'   => $userId,
            'name' => $user['full_name'] ?? ($user['name'] ?? 'Unknown User'),
        ];

        // Technician state
        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        $states = [];
        if (!empty($technician['state'])) {
            $states = array_filter(array_map('trim', explode(',', $technician['state'])));
        }

        // Sites according to technician state
        if (!empty($states)) {
            $data['sites'] = $siteModel
                ->where('company_id', $companyId)
                ->whereIn('state', $states)
                ->findAll();
        } else {
            $data['sites'] = [];
        }

        $siteIds = array_column($data['sites'], 'id');

        // Equipment according to allowed sites
        if (!empty($siteIds)) {
            $data['equipment'] = $equipmentModel
                ->where('company_id', $companyId)
                ->whereIn('site_id', $siteIds)
                ->findAll();
        } else {
            $data['equipment'] = [];
        }

        // Open work orders only
        if (!empty($siteIds)) {
            $data['open_work_orders'] = $workOrderModel
            ->select('
                work_orders.*,
                sites.name as site_name,
                COALESCE(se.asset_tag, equipment.asset_tag) as asset_tag,
                COALESCE(se.make, equipment.make) as make,
                COALESCE(se.model, equipment.model) as model
            ')
            ->join('sites', 'sites.id = work_orders.site_id', 'left')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->join(
                'site_equipment se',
                'se.master_equipment_id = work_orders.equipment_id
                AND se.site_id = work_orders.site_id
                AND se.company_id = work_orders.company_id
                AND se.deleted_at IS NULL',
                'left'
            )
            ->where('work_orders.company_id', $companyId)
            ->whereIn('work_orders.site_id', $siteIds)
            ->where('work_orders.status', 'open')
            ->orderBy('work_orders.created_at', 'DESC')
            ->findAll();

        } else {
            $data['open_work_orders'] = [];
        }

        // Completed service history only
        if (!empty($siteIds)) {
           $data['recent_history'] = $workOrderModel
            ->select('
                work_orders.*,
                sites.name as site_name,
                COALESCE(se.make, equipment.make) as make,
                COALESCE(se.model, equipment.model) as model,
                COALESCE(se.asset_tag, equipment.asset_tag) as asset_tag
            ')
            ->join('sites', 'sites.id = work_orders.site_id', 'left')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->join(
                'site_equipment se',
                'se.master_equipment_id = work_orders.equipment_id
                AND se.site_id = work_orders.site_id
                AND se.company_id = work_orders.company_id
                AND se.deleted_at IS NULL',
                'left'
            )
            ->where('work_orders.company_id', $companyId)
            ->whereIn('work_orders.site_id', $siteIds)
            ->where('work_orders.status', 'completed')
            ->orderBy('work_orders.completed_at', 'DESC')
            ->orderBy('work_orders.updated_at', 'DESC')
            ->limit(10)
            ->findAll();

        } else {
            $data['recent_history'] = [];
        }

        return view('technician/service/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/technician/service-history');
        }

        $db        = Database::connect();
        $companyId = $this->session->get('company_id');
        $userId    = $this->session->get('user_id');

        $workOrderModel = new WorkOrderModel();

        // Technician state
        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        $states = [];
        if (!empty($technician['state'])) {
            $states = array_filter(array_map('trim', explode(',', $technician['state'])));
        }

        $siteId = $this->request->getPost('site_id');

        // Validate selected site belongs to technician state
        $siteQuery = $db->table('sites')
            ->where('id', $siteId)
            ->where('company_id', $companyId);

        if (!empty($states)) {
            $siteQuery->whereIn('state', $states);
        }

        $validSite = $siteQuery->get()->getRowArray();

        if (!$validSite) {
            return redirect()->back()->withInput()->with('error', 'Invalid site selected for your assigned state.');
        }

        $status = $this->request->getPost('status');

        // Allow only open or completed
        if (!in_array($status, ['open', 'completed'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid status selected.');
        }

        $data = [
            'company_id'   => $companyId,
            'site_id'      => $siteId,
            'title'        => trim((string) $this->request->getPost('title')),
            'sn'           => trim((string) $this->request->getPost('sn')),
            'equipment_id' => $this->request->getPost('equipment_id') ?: null,
            'status'       => $status,
            'priority'     => $this->request->getPost('priority'),
            'assigned_to'  => $userId,
            'created_by'   => $userId,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'description'  => trim((string) $this->request->getPost('description')),
        ];

        if (
            empty($data['site_id']) ||
            empty($data['title']) ||
            empty($data['sn']) ||
            empty($data['status']) ||
            empty($data['priority'])
        ) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields.');
        }

        if ($workOrderModel->insert($data)) {
            return redirect()->to('/technician/service-history')
                ->with('success', 'Work Order created successfully.');
        }

        $errors = $workOrderModel->errors();
        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to create work order.';

        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}
