<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class ReportsController extends BaseController
{
    public function index()
    {
       
        return view('technician/report/index');
    }

   
}