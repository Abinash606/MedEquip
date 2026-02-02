<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;

class WorkOrdersController extends BaseController
{
    public function index(int $siteId)
    {
        $model     = new WorkOrderModel();
        $companyId = $this->session->get('company_id');
        $data['workOrders'] = $model
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->findAll();
        return view('customer/work_orders/index', $data);
    }
}