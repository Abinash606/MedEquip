<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\EquipmentModel;
use App\Models\SiteModel;
use App\Models\UserModel;

class ServiceHistoryController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        $userId = $this->session->get('user_id');

        // Get equipment for the dropdown
        $equipmentModel = new EquipmentModel();
        $data['equipment'] = $equipmentModel->where('company_id', $companyId)->findAll();

        // Get sites for the dropdown
        $siteModel = new SiteModel();
        $data['sites'] = $siteModel->where('company_id', $companyId)->findAll();

        // Get user info
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        $data['current_user'] = [
            'id' => $userId,
            'name' => $user['full_name'] ?? 'Unknown User'
        ];

        // Get Open Work Orders (assigned to this technician)
        $workOrderModel = new WorkOrderModel();
        $data['open_work_orders'] = $workOrderModel
            ->select('work_orders.*, sites.name as site_name, equipment.make, equipment.model')
            ->join('sites', 'sites.id = work_orders.site_id', 'left')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.assigned_to', $userId)
            ->whereIn('work_orders.status', ['open', 'in progress'])
            ->orderBy('work_orders.created_at', 'DESC')
            ->findAll();

        // Get Recent Service History (completed work orders)
        $data['recent_history'] = $workOrderModel
            ->select('work_orders.*, sites.name as site_name, equipment.make, equipment.model')
            ->join('sites', 'sites.id = work_orders.site_id', 'left')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.assigned_to', $userId)
            ->where('work_orders.status', 'completed')
            ->orderBy('work_orders.completed_at', 'DESC')
            ->limit(10)
            ->findAll();

        return view('technician/service/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $companyId = $this->session->get('company_id');
            $userId = $this->session->get('user_id');

            $workOrderModel = new WorkOrderModel();

            $data = [
                'company_id' => $companyId,
                'title' => $this->request->getPost('title'),
                'equipment_id' => $this->request->getPost('equipment_id') ?: null,
                'status' => $this->request->getPost('status'),
                'priority' => $this->request->getPost('priority'),
                'assigned_to' => $userId,
                'start_date' => $this->request->getPost('start_date') ?: null,
                'end_date' => $this->request->getPost('end_date') ?: null,
                'description' => $this->request->getPost('description'),
                'site_id' => $this->request->getPost('site_id'),
                'created_by' => $userId,
            ];

            // Validate required fields
            if (empty($data['title']) || empty($data['status']) || empty($data['priority']) || empty($data['site_id'])) {
                return redirect()->back()->with('error', 'Please fill all required fields including Site');
            }

            if ($workOrderModel->insert($data)) {
                return redirect()->to('/technician/service-history')->with('success', 'Work Order created successfully');
            } else {
                $errors = $workOrderModel->errors();
                return redirect()->back()->with('error', 'Failed to create work order: ' . implode(', ', $errors));
            }
        }

        return redirect()->to('/technician/service-history');
    }
}