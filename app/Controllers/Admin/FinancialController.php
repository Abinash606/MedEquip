<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Dashboard controller for the Super Admin (company owner).
 */
class FinancialController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        // Load models to gather statistics
        $customerModel   = new \App\Models\CustomerModel();
        $siteModel       = new \App\Models\SiteModel();
        $equipmentModel  = new \App\Models\EquipmentModel();
        $inspectionModel = new \App\Models\InspectionModel();
        $workModel       = new \App\Models\WorkOrderModel();

        $data = [
            'customersCount'   => $customerModel->where('company_id', $companyId)->countAllResults(),
            'sitesCount'       => $siteModel->where('company_id', $companyId)->countAllResults(),
            'equipmentCount'   => $equipmentModel->where('company_id', $companyId)->countAllResults(),
            'inspectionsCount' => $inspectionModel->where('company_id', $companyId)->countAllResults(),
            'workOrdersCount'  => $workModel->where('company_id', $companyId)->countAllResults(),
        ];
        return view('admin/financials/index', $data);
    }
}