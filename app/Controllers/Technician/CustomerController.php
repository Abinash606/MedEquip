<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class CustomerController extends BaseController
{
    public function index()
    {
       
        return view('technician/customer/index');
    }

   
}