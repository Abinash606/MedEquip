<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class CustomerController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        if (!$technician || empty($technician->state)) {
            return view('technician/customer/index', [
                'customers'    => [],
                'allCustomers' => [],
            ]);
        }

        $states = array_map('trim', explode(',', $technician->state));

        $customers = $db->table('customers')
            ->select('customers.*, COUNT(sites.id) as site_count, MIN(sites.id) as first_site_id')
            ->join('sites', 'sites.customer_id = customers.id AND sites.deleted_at IS NULL', 'left')
            ->whereIn('customers.billing_state', $states)
            ->where('customers.deleted_at', null)
            ->where('customers.company_id', $companyId)
            ->groupBy('customers.id')
            ->get()
            ->getResultArray();

        // ── For Add Site dropdown ────────────────────────────────
        $allCustomers = $db->table('customers')
            ->select('id, name')
            ->whereIn('billing_state', $states)
            ->where('deleted_at', null)
            ->where('company_id', $companyId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('technician/customer/index', [
            'customers'    => $customers,
            'allCustomers' => $allCustomers,
        ]);
    }

    // ── Store customer filter in session, redirect cleanly ───────
    public function filterSites($customerId)
    {
        session()->set('site_customer_filter', $customerId);
        return redirect()->to('technician/sites');
    }
}
