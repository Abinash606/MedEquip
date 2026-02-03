<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\InspectionModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        
        return view('customer/inspections/index');
    }
}