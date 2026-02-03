<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class InspectionController extends BaseController
{
    public function index()
    {
       
        return view('technician/inspection/index');
    }

   
}