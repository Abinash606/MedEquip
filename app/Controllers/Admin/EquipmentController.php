<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;

class EquipmentController extends BaseController
{
    public function index()
    {
        $model = new EquipmentModel();
        $companyId = $this->session->get('company_id');
        $data['equipment'] = $model->where('company_id', $companyId)->findAll();
        return view('admin/equipment/index', $data);
    }
}