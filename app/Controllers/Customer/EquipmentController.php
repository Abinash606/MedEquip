<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;

class EquipmentController extends BaseController
{
    public function index(int $siteId)
    {
        $model     = new EquipmentModel();
        $companyId = $this->session->get('company_id');
        $data['equipment'] = $model
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->findAll();
        return view('customer/equipment/index', $data);
    }
}