<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class CustomerController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $userId = session()->get('user_id');

        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        if (!$technician || empty($technician->state)) {
            return view('technician/customer/index', [
                'customers' => []
            ]);
        }

        $states = array_map('trim', explode(',', $technician->state));

        $customers = $db->table('customers')
            ->select('customers.*, COUNT(sites.id) as site_count, MIN(sites.id) as first_site_id')
            ->join('sites', 'sites.customer_id = customers.id AND sites.deleted_at IS NULL', 'left')
            ->whereIn('customers.billing_state', $states)
            ->where('customers.deleted_at', null)
            ->groupBy('customers.id')
            ->get()
            ->getResultArray();

        return view('technician/customer/index', [
            'customers' => $customers
        ]);
    }
}