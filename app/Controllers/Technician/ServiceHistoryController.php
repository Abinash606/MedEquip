<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class ServiceHistoryController extends BaseController
{
    public function index()
    {
       
        return view('technician/service/index');
    }

   
}