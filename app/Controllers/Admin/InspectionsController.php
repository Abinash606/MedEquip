<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InspectionModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        $model = new InspectionModel();
        $companyId = $this->session->get('company_id');
        $data['inspections'] = $model->where('company_id', $companyId)->findAll();
        return view('admin/inspections/index', $data);
    }
}