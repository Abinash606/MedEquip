<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\InspectionModel;

class InspectionsController extends BaseController
{
    public function index(int $siteId)
    {
        $model     = new InspectionModel();
        $companyId = $this->session->get('company_id');
        $data['inspections'] = $model
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->findAll();
        return view('customer/inspections/index', $data);
    }
}