<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        $db = \Config\Database::connect();

        // ── KPI: Total Inspections ─────────────────────────────────
        $totalInspections = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ?",
            [$companyId]
        )->getRow()->cnt ?? 0);

        // ── KPI: Critical/High open work orders ───────────────────
        $criticalWO = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND priority IN ('critical','high') AND status != 'closed'",
            [$companyId]
        )->getRow()->cnt ?? 0);

        // ── KPI: Equipment operational % ──────────────────────────
        $totalEquipment = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM site_equipment WHERE company_id = ? AND deleted_at IS NULL",
            [$companyId]
        )->getRow()->cnt ?? 0);

        $readyEquipment = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM site_equipment WHERE company_id = ? AND deleted_at IS NULL AND status = 'ready'",
            [$companyId]
        )->getRow()->cnt ?? 0);

        $equipPct = $totalEquipment > 0 ? round(($readyEquipment / $totalEquipment) * 100) : 0;

        // ── KPI: Compliance score (pass rate) ─────────────────────
        $passCount = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','pass')",
            [$companyId]
        )->getRow()->cnt ?? 0);
        $complianceScore = $totalInspections > 0 ? round(($passCount / $totalInspections) * 100) : 100;

        // ── KPI: Pending/Open work orders ─────────────────────────
        $pendingWO = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status = 'open'",
            [$companyId]
        )->getRow()->cnt ?? 0);

        // ── Work Order Overview ────────────────────────────────────
        $woCompleted = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status IN ('closed','completed')",
            [$companyId]
        )->getRow()->cnt ?? 0);

        $woInProgress = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status = 'in_progress'",
            [$companyId]
        )->getRow()->cnt ?? 0);

        $woTotal = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ?",
            [$companyId]
        )->getRow()->cnt ?? 0);

        // ── Inspection Overview ────────────────────────────────────
        $inspCompleted = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','Fail','Repair','pass','fail','repair','completed')",
            [$companyId]
        )->getRow()->cnt ?? 0);
        $inspInProgress = max(0, $totalInspections - $inspCompleted);

        // ── Inventory Overview ─────────────────────────────────────
        $invTotal = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inventory"
        )->getRow()->cnt ?? 0);

        $invLowStock = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inventory WHERE qty > 0 AND qty <= 5"
        )->getRow()->cnt ?? 0);

        $invOutOfStock = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inventory WHERE qty = 0"
        )->getRow()->cnt ?? 0);

        // ── Live Service Feed ──────────────────────────────────────
        $liveServiceFeed = $db->query("
            SELECT
                wo.id,
                wo.title,
                wo.status,
                wo.priority,
                wo.created_at,
                wo.updated_at,
                s.name         AS site_name,
                c.name         AS customer_name,
                e.make,
                e.model,
                e.device_type,
                u.full_name    AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id = wo.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN site_equipment e ON e.id = wo.equipment_id
            LEFT JOIN technicians t ON t.id = wo.assigned_to
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE wo.company_id = ?
            ORDER BY wo.updated_at DESC, wo.id DESC
            LIMIT 10
        ", [$companyId])->getResultArray();

        // ── Technician Workload ────────────────────────────────────
        $techWorkload = $db->query("
            SELECT
                u.full_name,
                COUNT(wo.id) AS total_wo,
                SUM(CASE WHEN wo.status IN ('closed','completed') THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN wo.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                SUM(CASE WHEN wo.status = 'open' THEN 1 ELSE 0 END) AS new_wo
            FROM technicians t
            LEFT JOIN users u        ON u.id  = t.user_id
            LEFT JOIN work_orders wo ON wo.assigned_to = t.id AND wo.company_id = ?
            WHERE t.company_id = ?
            GROUP BY t.id, u.full_name
            ORDER BY total_wo DESC
            LIMIT 5
        ", [$companyId, $companyId])->getResultArray();

        $maxWO = 1;
        if (!empty($techWorkload)) {
            $totals = array_column($techWorkload, 'total_wo');
            $maxWO  = max(array_map('intval', $totals) ?: [1]);
            if ($maxWO < 1) {
                $maxWO = 1;
            }
        }

        return view('admin/dashboard', [
            'totalInspections' => $totalInspections,
            'criticalWO'       => $criticalWO,
            'equipPct'         => $equipPct,
            'complianceScore'  => $complianceScore,
            'pendingWO'        => $pendingWO,
            'woTotal'          => $woTotal,
            'woCompleted'      => $woCompleted,
            'woInProgress'     => $woInProgress,
            'inspCompleted'    => $inspCompleted,
            'inspInProgress'   => $inspInProgress,
            'invTotal'         => $invTotal,
            'invLowStock'      => $invLowStock,
            'invOutOfStock'    => $invOutOfStock,
            'liveServiceFeed'  => $liveServiceFeed,
            'techWorkload'     => $techWorkload,
            'maxWO'            => $maxWO,
        ]);
    }

    /**
     * GET admin/dashboard/search?q=...
     * Global search across customers, equipment, sites, inspections.
     */
    public function search()
    {
        $companyId = (int) $this->session->get('company_id');
        $q         = trim((string) $this->request->getGet('q'));

        if (strlen($q) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $db      = \Config\Database::connect();
        $results = [];
        $seen    = [];

        $push = function (array $row) use (&$results, &$seen) {
            $key = ($row['type'] ?? '') . '|' . ($row['label'] ?? '') . '|' . ($row['url'] ?? '');
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $results[] = $row;
        };

        // Customers
        $customers = $db->query(
            "SELECT id, name, billing_city AS subtitle
             FROM customers
             WHERE company_id = ? AND deleted_at IS NULL AND name LIKE ?
             LIMIT 5",
            [$companyId, "%$q%"]
        )->getResultArray();

        foreach ($customers as $r) {
            $push([
                'label'    => $r['name'],
                'subtitle' => $r['subtitle'] ? 'Customer — ' . $r['subtitle'] : 'Customer',
                'type'     => 'Customer',
                'icon'     => 'fa-users',
                'url'      => site_url('admin/customers/filter-sites/' . $r['id']),
            ]);
        }

        // Sites
        $sites = $db->query(
            "SELECT s.id, s.name, c.name AS customer_name
             FROM sites s
             LEFT JOIN customers c ON c.id = s.customer_id
             WHERE s.company_id = ?
               AND s.deleted_at IS NULL
               AND (s.name LIKE ? OR c.name LIKE ?)
             LIMIT 5",
            [$companyId, "%$q%", "%$q%"]
        )->getResultArray();

        foreach ($sites as $r) {
            $push([
                'label'    => $r['name'],
                'subtitle' => 'Site — ' . ($r['customer_name'] ?? ''),
                'type'     => 'Site',
                'icon'     => 'fa-sitemap',
                'url'      => site_url('admin/sites/' . $r['id']),
            ]);
        }

        // Equipment → open site details with Equipment tab
        $equip = $db->query(
            "SELECT
                e.id,
                e.asset_tag,
                e.site_id,
                CONCAT(COALESCE(e.make,''), ' ', COALESCE(e.model,'')) AS model_str,
                e.serial_number,
                e.device_type,
                s.name AS site_name
             FROM site_equipment e
             LEFT JOIN sites s ON s.id = e.site_id
             WHERE e.company_id = ?
               AND e.deleted_at IS NULL
               AND (
                    e.asset_tag LIKE ?
                 OR e.model LIKE ?
                 OR e.make LIKE ?
                 OR e.serial_number LIKE ?
               )
             LIMIT 8",
            [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"]
        )->getResultArray();

        foreach ($equip as $r) {
            $label = $r['asset_tag'] ?: trim($r['model_str']);

            $url = !empty($r['site_id'])
                ? site_url('admin/sites/' . $r['site_id'])
                : site_url('admin/equipment');

            $push([
                'label'    => $label,
                'subtitle' => 'Equipment'
                    . (!empty($r['device_type']) ? ' — ' . $r['device_type'] : '')
                    . (!empty($r['site_name']) ? ' @ ' . $r['site_name'] : '')
                    . (!empty($r['serial_number']) ? ' | S/N: ' . $r['serial_number'] : ''),
                'type'     => 'Equipment',
                'icon'     => 'fa-boxes-stacked',
                'url'      => $url,
                'nav'      => !empty($r['site_id']) ? [
                    'open_tab'     => 'equipment',
                    'asset_tag'    => $r['asset_tag'] ?? '',
                    'equipment_id' => $r['id'] ?? '',
                ] : null,
            ]);
        }

        // Inspection-only / inspection-saved devices → open site details with Inspections tab
        $inspectionAssets = $db->query(
            "SELECT
                i.group_id,
                i.site_id,
                COALESCE(i.asset_tag, e.asset_tag) AS asset_tag,
                COALESCE(i.make, e.make) AS make,
                COALESCE(i.model, e.model) AS model,
                COALESCE(i.serial_number, e.serial_number) AS serial_number,
                COALESCE(i.device_type, e.device_type) AS device_type,
                COALESCE(NULLIF(i.title, ''), NULLIF(i.inspection_type, ''), 'Inspection') AS inspection_title,
                s.name AS site_name
             FROM inspections i
             LEFT JOIN site_equipment e ON e.id = i.equipment_id
             LEFT JOIN sites s ON s.id = i.site_id
             WHERE i.company_id = ?
               AND i.deleted_at IS NULL
               AND (
                    COALESCE(i.asset_tag, e.asset_tag) LIKE ?
                 OR COALESCE(i.model, e.model) LIKE ?
                 OR COALESCE(i.make, e.make) LIKE ?
                 OR COALESCE(i.serial_number, e.serial_number) LIKE ?
               )
             ORDER BY i.id DESC
             LIMIT 8",
            [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"]
        )->getResultArray();

        foreach ($inspectionAssets as $r) {
            $label = $r['asset_tag'] ?: trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? ''));

            $url = !empty($r['site_id'])
                ? site_url('admin/sites/' . $r['site_id'])
                : site_url('admin/inspections');

            $push([
                'label'    => $label,
                'subtitle' => 'Inspection Device'
                    . (!empty($r['site_name']) ? ' @ ' . $r['site_name'] : '')
                    . (!empty($r['group_id']) ? ' | ' . $r['group_id'] : '')
                    . (!empty($r['serial_number']) ? ' | S/N: ' . $r['serial_number'] : ''),
                'type'     => 'Inspection Device',
                'icon'     => 'fa-magnifying-glass',
                'url'      => $url,
                'nav'      => !empty($r['site_id']) ? [
                    'open_tab'         => 'inspections',
                    'group_id'         => $r['group_id'] ?? '',
                    'inspection_title' => $r['inspection_title'] ?? 'Inspection',
                ] : null,
            ]);
        }

        // Inspection groups → open site details with Inspections tab and exact group
        $insps = $db->query(
            "SELECT DISTINCT
                i.group_id,
                i.site_id,
                i.title,
                i.inspection_type,
                s.name AS site_name,
                c.name AS customer_name
             FROM inspections i
             LEFT JOIN sites s ON s.id = i.site_id
             LEFT JOIN customers c ON c.id = s.customer_id
             WHERE i.company_id = ?
               AND i.deleted_at IS NULL
               AND (
                    i.group_id LIKE ?
                 OR s.name LIKE ?
                 OR c.name LIKE ?
                 OR COALESCE(i.asset_tag, '') LIKE ?
               )
             LIMIT 8",
            [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"]
        )->getResultArray();

        foreach ($insps as $r) {
            $inspectionTitle = !empty($r['title'])
                ? $r['title']
                : (!empty($r['inspection_type']) ? $r['inspection_type'] : 'Inspection');

            $url = !empty($r['site_id'])
                ? site_url('admin/sites/' . $r['site_id'])
                : site_url('admin/inspection-reports');

            $push([
                'label'    => $r['group_id'] ?? '—',
                'subtitle' => 'Inspection — ' . ($r['customer_name'] ?? '') . ' / ' . ($r['site_name'] ?? ''),
                'type'     => 'Inspection',
                'icon'     => 'fa-clipboard-list',
                'url'      => $url,
                'nav'      => !empty($r['site_id']) ? [
                    'open_tab'         => 'inspections',
                    'group_id'         => $r['group_id'] ?? '',
                    'inspection_title' => $inspectionTitle,
                ] : null,
            ]);
        }

        return $this->response->setJSON(['results' => array_slice($results, 0, 12)]);
    }
}