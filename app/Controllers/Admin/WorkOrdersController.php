<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;

class WorkOrdersController extends BaseController
{
    public function index()
    {
        $model = new WorkOrderModel();
        $companyId = $this->session->get('company_id');
        $data['workOrders'] = $model->where('company_id', $companyId)->findAll();
        return view('admin/work_orders/index', $data);
    }
}