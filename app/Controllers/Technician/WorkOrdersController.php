<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;

class WorkOrdersController extends BaseController
{
    public function index()
    {
        $model     = new WorkOrderModel();
        $companyId = $this->session->get('company_id');
        $technicianId = $this->session->get('technician_id');
        $data['workOrders'] = $model
            ->where('company_id', $companyId)
            ->where('assigned_to', $technicianId)
            ->findAll();
        return view('technician/work_orders/index', $data);
    }

    public function show(int $id)
    {
        $model     = new WorkOrderModel();
        $companyId = $this->session->get('company_id');
        $technicianId = $this->session->get('technician_id');
        $workOrder = $model
            ->where('company_id', $companyId)
            ->where('assigned_to', $technicianId)
            ->where('id', $id)
            ->first();
        if (! $workOrder) {
            return redirect()->to('/technician/work-orders');
        }
        $data['workOrder'] = $workOrder;
        return view('technician/work_orders/show', $data);
    }
}