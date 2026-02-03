<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class SitesController extends BaseController
{
    public function index()
    {
       
        return view('technician/site/index');
    }

   
}