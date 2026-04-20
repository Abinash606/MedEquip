<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId  = (int) $this->session->get('company_id');
        $customerId = (int) ($this->session->get('customer_id') ?? 0);
        $db         = \Config\Database::connect();

        // All sites for this customer account only
        $siteSql = "SELECT id, name FROM sites WHERE company_id = ? AND deleted_at IS NULL";
        $params  = [$companyId];

        if ($customerId > 0) {
            $siteSql .= " AND customer_id = ?";
            $params[] = $customerId;
        }

        $siteSql .= " ORDER BY name ASC";

        $sites   = $db->query($siteSql, $params)->getResultArray();
        $siteIds = array_map('intval', array_column($sites, 'id'));

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

        $inList = implode(',', $siteIds);

        // Grouped inspection summary per site so dashboard matches customer inspection pages.
        // Fix:
        // - safely handle zero dates like 0000-00-00 without MySQL strict-mode crash
        // - use grouped inspection sessions
        $inspectionSummaryRows = $db->query("
            SELECT
                z.site_id,
                COUNT(*) AS total_groups,
                SUM(CASE WHEN z.group_status = 'Pass' THEN 1 ELSE 0 END) AS pass_groups,
                SUM(
                    CASE
                        WHEN z.group_status = 'In Progress'
                         AND z.next_due_date IS NOT NULL
                         AND z.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                        THEN 1 ELSE 0
                    END
                ) AS due_soon_groups
            FROM (
                SELECT
                    i.site_id,
                    COALESCE(NULLIF(i.group_id, ''), CONCAT('ROW-', i.id)) AS inspection_group,
                    CASE
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'fail' THEN 1 ELSE 0 END) > 0 THEN 'Fail'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'repair' THEN 1 ELSE 0 END) > 0 THEN 'Repair'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'pass' THEN 1 ELSE 0 END) > 0 THEN 'Pass'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) IN ('closed/complete','closed_complete','closed','complete','completed') THEN 1 ELSE 0 END) > 0 THEN 'Closed/Complete'
                        ELSE 'In Progress'
                    END AS group_status,
                    MIN(
                        CASE
                            WHEN CONCAT(i.next_due_date) IN ('', '0000-00-00', '0000-00-00 00:00:00')
                                THEN NULL
                            ELSE DATE(CONCAT(i.next_due_date))
                        END
                    ) AS next_due_date
                FROM inspections i
                WHERE i.site_id IN ($inList)
                  AND i.deleted_at IS NULL
                GROUP BY i.site_id, COALESCE(NULLIF(i.group_id, ''), CONCAT('ROW-', i.id))
            ) z
            GROUP BY z.site_id
        ")->getResultArray();

        $inspectionSummaryMap = [];
        foreach ($inspectionSummaryRows as $row) {
            $inspectionSummaryMap[(int) $row['site_id']] = [
                'total_groups' => (int) ($row['total_groups'] ?? 0),
                'pass_groups'  => (int) ($row['pass_groups'] ?? 0),
                'due_soon'     => (int) ($row['due_soon_groups'] ?? 0),
            ];
        }

        // Active work orders per site. Treat only completed/closed/cancelled as non-active.
        $activeWorkOrderSql = "
            SELECT
                site_id,
                COUNT(*) AS active_wo
            FROM work_orders
            WHERE site_id IN ($inList)
              AND deleted_at IS NULL
              AND LOWER(COALESCE(status, '')) NOT IN ('completed','complete','closed','cancelled','canceled')
            GROUP BY site_id
        ";
        $woRows = $db->query($activeWorkOrderSql)->getResultArray();

        $woMap = [];
        foreach ($woRows as $row) {
            $woMap[(int) $row['site_id']] = (int) ($row['active_wo'] ?? 0);
        }

        // Active equipment per site.
        $assetSql = "
            SELECT site_id, COUNT(*) AS asset_count
            FROM site_equipment
            WHERE site_id IN ($inList)
              AND deleted_at IS NULL
            GROUP BY site_id
        ";
        $assetRows = $db->query($assetSql)->getResultArray();

        $assetMap = [];
        foreach ($assetRows as $row) {
            $assetMap[(int) $row['site_id']] = (int) ($row['asset_count'] ?? 0);
        }

        $siteStats = [];
        foreach ($sites as $site) {
            $sid         = (int) $site['id'];
            $inspSummary = $inspectionSummaryMap[$sid] ?? [
                'total_groups' => 0,
                'pass_groups'  => 0,
                'due_soon'     => 0,
            ];

            $inspectionCount = (int) $inspSummary['total_groups'];
            $passCount       = (int) $inspSummary['pass_groups'];
            $dueSoonCount    = (int) $inspSummary['due_soon'];

            $siteStats[] = [
                'id'               => $sid,
                'name'             => $site['name'],
                'asset_count'      => (int) ($assetMap[$sid] ?? 0),
                'inspection_count' => $inspectionCount,
                'compliance_pct'   => $inspectionCount > 0 ? (int) round(($passCount / $inspectionCount) * 100) : 0,
                'open_wo'          => (int) ($woMap[$sid] ?? 0),
                'due_soon'         => $dueSoonCount,
            ];
        }

        $totalAssets = array_sum(array_column($siteStats, 'asset_count'));
        $totalOpenWO = array_sum(array_column($siteStats, 'open_wo'));
        $totalInsp   = array_sum(array_column($siteStats, 'inspection_count'));

        $totalPass = 0;
        foreach ($siteStats as $stat) {
            $sid = (int) $stat['id'];
            $totalPass += (int) ($inspectionSummaryMap[$sid]['pass_groups'] ?? 0);
        }

        $overallCompliance = $totalInsp > 0 ? (int) round(($totalPass / $totalInsp) * 100) : 100;
        $dueSoon           = array_sum(array_column($siteStats, 'due_soon'));

        // Recent completed inspections at grouped inspection-session level.
        $recentInspections = $db->query("
            SELECT
                g.latest_id AS id,
                g.group_key AS group_id,
                g.group_status AS status,
                g.completed_at,
                g.site_id,
                COALESCE(i.asset_tag, e.asset_tag) AS asset_tag,
                COALESCE(i.model, e.model) AS model,
                COALESCE(i.make, e.make) AS make,
                s.name AS site_name,
                u.full_name AS tech_name
            FROM (
                SELECT
                    MAX(i.id) AS latest_id,
                    i.site_id,
                    COALESCE(NULLIF(i.group_id, ''), CONCAT('ROW-', i.id)) AS group_key,
                    CASE
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'fail' THEN 1 ELSE 0 END) > 0 THEN 'Fail'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'repair' THEN 1 ELSE 0 END) > 0 THEN 'Repair'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) = 'pass' THEN 1 ELSE 0 END) > 0 THEN 'Pass'
                        WHEN SUM(CASE WHEN LOWER(COALESCE(i.status, '')) IN ('closed/complete','closed_complete','closed','complete','completed') THEN 1 ELSE 0 END) > 0 THEN 'Closed/Complete'
                        ELSE 'In Progress'
                    END AS group_status,
                    MAX(i.completed_at) AS completed_at
                FROM inspections i
                WHERE i.site_id IN ($inList)
                  AND i.deleted_at IS NULL
                GROUP BY i.site_id, COALESCE(NULLIF(i.group_id, ''), CONCAT('ROW-', i.id))
            ) g
            INNER JOIN inspections i ON i.id = g.latest_id
            LEFT JOIN site_equipment e ON e.id = i.equipment_id AND e.deleted_at IS NULL
            LEFT JOIN sites s ON s.id = g.site_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u ON u.id = t.user_id
            WHERE g.group_status IN ('Pass','Fail','Repair','Closed/Complete')
            ORDER BY COALESCE(g.completed_at, i.updated_at, i.created_at) DESC
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