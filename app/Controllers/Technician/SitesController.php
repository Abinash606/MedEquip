<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class SitesController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        // Logged-in user info
        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        // 1️⃣ Technician state(s) fetch (NOT soft deleted)
        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRow();

        if (!$technician || empty($technician->state)) {
            return view('technician/site/index', [
                'sites' => []
            ]);
        }

        // 2️⃣ States explode
        $states = array_map('trim', explode(',', $technician->state));

        // 3️⃣ Customer IDs fetch (NOT soft deleted)
        $customerIds = $db->table('customers')
            ->select('id')
            ->where('company_id', $companyId)
            ->whereIn('billing_state', $states)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getResultArray();

        if (empty($customerIds)) {
            return view('technician/site/index', [
                'sites' => []
            ]);
        }

        $customerIds = array_column($customerIds, 'id');

        // 4️⃣ Sites fetch using customer FK (NOT soft deleted)
        $sites = $db->table('sites')
            ->select('
                sites.id,
                sites.name           AS site_name,
                sites.address        AS site_address,
                sites.contact_name   AS site_contact_name,
                sites.phone          AS site_phone,
                sites.email          AS site_email,
                customers.name       AS customer_name
            ')
            ->join(
                'customers',
                'customers.id = sites.customer_id AND customers.deleted_at IS NULL',
                'left'
            )
            ->where('sites.company_id', $companyId)
            ->whereIn('sites.customer_id', $customerIds)
            ->where('sites.deleted_at IS NULL', null, false)
            ->get()
            ->getResultArray();

        return view('technician/site/index', [
            'sites' => $sites
        ]);
    }
}
