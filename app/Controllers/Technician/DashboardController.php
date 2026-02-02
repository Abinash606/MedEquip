<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [];
        return view('technician/dashboard', $data);
    }
}