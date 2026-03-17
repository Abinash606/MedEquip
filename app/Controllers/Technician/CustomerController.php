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
            ->whereIn('billing_state', $states)
            ->where('deleted_at', null)
            ->get()
            ->getResult();

        return view('technician/customer/index', [
            'customers' => $customers
        ]);
    }
}