<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class DocumentsController extends BaseController
{
    public function index()
    {
        $companyId  = (int) $this->session->get('company_id');
        $customerId = (int) $this->session->get('customer_id');
        $db         = \Config\Database::connect();

        // Get all site IDs for this customer
        $siteSql    = "SELECT id FROM sites WHERE company_id = ? AND deleted_at IS NULL";
        $siteParams = [$companyId];
        if ($customerId) {
            $siteSql    .= " AND customer_id = ?";
            $siteParams[] = $customerId;
        }
        $siteRows = $db->query($siteSql, $siteParams)->getResultArray();
        $siteIds  = array_column($siteRows, 'id');

        if (empty($siteIds)) {
            return view('customer/documents/index', ['reports' => []]);
        }

        $inList = implode(',', array_map('intval', $siteIds));

        // Fetch all inspection groups for this customer's sites
        $reports = $db->query("
            SELECT
                i.group_id,
                i.inspection_type,
                i.status,
                i.completed_at,
                i.scheduled_at,
                s.name       AS site_name,
                c.name       AS customer_name,
                COUNT(i.id)  AS device_count
            FROM inspections i
            LEFT JOIN sites s     ON s.id = i.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            WHERE i.site_id IN ($inList)
              AND i.group_id IS NOT NULL
              AND i.group_id != ''
            GROUP BY i.group_id
            ORDER BY COALESCE(i.completed_at, i.scheduled_at) DESC
            LIMIT 100
        ")->getResultArray();

        return view('customer/documents/index', ['reports' => $reports]);
    }
}
