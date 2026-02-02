<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

/**
 * Dashboard for customer users. Shows aggregated information for the
 * customer's sites, equipment, inspections and work orders.
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $data = [];
        return view('customer/dashboard', $data);
    }
}