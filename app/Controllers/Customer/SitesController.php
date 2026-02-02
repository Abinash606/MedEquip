<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\SiteModel;

class SitesController extends BaseController
{
    public function index()
    {
        $model      = new SiteModel();
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $data['sites'] = $model->where('company_id', $companyId)
            ->where('customer_id', $customerId)
            ->findAll();
        return view('customer/sites/index', $data);
    }

    /**
     * Display the details of a site including equipment, inspections and work orders.
     */
    public function show(int $id)
    {
        $siteModel   = new SiteModel();
        $equipModel  = new \App\Models\EquipmentModel();
        $inspModel   = new \App\Models\InspectionModel();
        $workModel   = new \App\Models\WorkOrderModel();
        $companyId   = $this->session->get('company_id');
        $customerId  = $this->session->get('customer_id');
        $site = $siteModel->where('company_id', $companyId)
            ->where('customer_id', $customerId)
            ->where('id', $id)
            ->first();
        if (! $site) {
            return redirect()->to('/customer/sites');
        }
        $data['site']       = $site;
        $data['equipment']  = $equipModel->where('site_id', $id)->findAll();
        $data['inspections'] = $inspModel->where('site_id', $id)->findAll();
        $data['workOrders'] = $workModel->where('site_id', $id)->findAll();
        return view('customer/sites/show', $data);
    }
}