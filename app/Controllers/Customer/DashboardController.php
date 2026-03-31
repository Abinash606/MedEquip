<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $db         = \Config\Database::connect();

        // All sites for this customer
        $siteSql = "SELECT id, name FROM sites WHERE company_id = ? AND deleted_at IS NULL";
        $params  = [$companyId];
        if ($customerId) { $siteSql .= " AND customer_id = ?"; $params[] = $customerId; }
        $sites   = $db->query($siteSql, $params)->getResultArray();
        $siteIds = array_column($sites, 'id');

        if (empty($siteIds)) {
            return view('customer/dashboard', [
                'sites'             => [],
                'siteStats'         => [],
                'totalAssets'       => 0,
                'totalInspections'  => 0,
                'totalOpenWO'       => 0,
                'overallCompliance' => 100,
                'dueSoon'           => 0,
                'recentInspections' => [],
            ]);
        }

        $inList = implode(',', array_map('intval', $siteIds));

        // Per-site stats
        $siteStats = [];
        foreach ($sites as $site) {
            $sid = (int)$site['id'];
            $r   = $db->query("SELECT
                COUNT(*) AS asset_count
                FROM equipment WHERE site_id = ? AND deleted_at IS NULL", [$sid])->getRow();

            $ti  = $db->query("SELECT COUNT(*) AS cnt FROM inspections WHERE site_id = ?", [$sid])->getRow()->cnt ?? 0;
            $pi  = $db->query("SELECT COUNT(*) AS cnt FROM inspections WHERE site_id = ? AND status IN ('Pass','pass')", [$sid])->getRow()->cnt ?? 0;
            $wo  = $db->query("SELECT COUNT(*) AS cnt FROM work_orders WHERE site_id = ? AND status = 'open' AND deleted_at IS NULL", [$sid])->getRow()->cnt ?? 0;

            $siteStats[] = [
                'id'               => $sid,
                'name'             => $site['name'],
                'asset_count'      => (int)($r->asset_count ?? 0),
                'inspection_count' => (int)$ti,
                'compliance_pct'   => $ti > 0 ? round(($pi / $ti) * 100) : 0,
                'open_wo'          => (int)$wo,
            ];
        }

        // Totals
        $totalAssets     = array_sum(array_column($siteStats, 'asset_count'));
        $totalOpenWO     = array_sum(array_column($siteStats, 'open_wo'));
        $totalInsp       = array_sum(array_column($siteStats, 'inspection_count'));

        $allPass         = (int)($db->query("SELECT COUNT(*) AS cnt FROM inspections WHERE site_id IN ($inList) AND status IN ('Pass','pass')")->getRow()->cnt ?? 0);
        $allTotal        = (int)($db->query("SELECT COUNT(*) AS cnt FROM inspections WHERE site_id IN ($inList)")->getRow()->cnt ?? 0);
        $overallCompliance = $allTotal > 0 ? round(($allPass / $allTotal) * 100) : 100;

        $dueSoon = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections
             WHERE site_id IN ($inList)
               AND next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        )->getRow()->cnt ?? 0);

        // Recent inspections (all sites, no filter — JS handles per-site filtering)
        $recentInspections = $db->query("
            SELECT
                i.id, i.group_id, i.status, i.completed_at, i.site_id,
                e.asset_tag, e.model, e.make,
                s.name  AS site_name,
                u.full_name AS tech_name
            FROM inspections i
            LEFT JOIN equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s     ON s.id = i.site_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u     ON u.id = t.user_id
            WHERE i.site_id IN ($inList)
              AND i.status IN ('Pass','Fail','Repair','pass','fail','repair')
            GROUP BY i.group_id
            ORDER BY i.id DESC
            LIMIT 50
        ")->getResultArray();

        return view('customer/dashboard', [
            'sites'              => $sites,
            'siteStats'          => $siteStats,
            'totalAssets'        => $totalAssets,
            'totalInspections'   => $totalInsp,
            'totalOpenWO'        => $totalOpenWO,
            'overallCompliance'  => $overallCompliance,
            'dueSoon'            => $dueSoon,
            'recentInspections'  => $recentInspections,
        ]);
    }
}
