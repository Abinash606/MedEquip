<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId = (int) session('company_id');
        $userId    = (int) session('user_id');
        $db        = \Config\Database::connect();

        // Resolve technician ID from user_id
        $tech = $db->query(
            "SELECT id FROM technicians WHERE user_id = ? AND company_id = ? AND deleted_at IS NULL LIMIT 1",
            [$userId, $companyId]
        )->getRow();
        $techId = $tech ? (int)$tech->id : 0;

        // ── Action Required: equipment that needs inspection (fail/repair status) ──
        $actionRequired = $db->query(
            "SELECT COUNT(*) AS cnt FROM site_equipment WHERE company_id = ? AND deleted_at IS NULL AND status IN ('need_attention','out_of_service')",
            [$companyId]
        )->getRow()->cnt ?? 0;

        // ── Open Requests: open work orders assigned to this technician ──
        $openRequests = 0;
        if ($techId) {
            $openRequests = $db->query(
                "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND assigned_to = ? AND status NOT IN ('closed','completed') AND deleted_at IS NULL",
                [$companyId, $techId]
            )->getRow()->cnt ?? 0;
        }
        if (!$techId) {
            $openRequests = 0;
        }

        // ── Compliance: pass rate from this technician's inspections ──
        $totalInsp = 0;
        $passInsp  = 0;
        if ($techId) {
            $totalInsp = $db->query(
                "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND technician_id = ?",
                [$companyId, $techId]
            )->getRow()->cnt ?? 0;
            $passInsp = $db->query(
                "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND technician_id = ? AND status IN ('Pass','pass')",
                [$companyId, $techId]
            )->getRow()->cnt ?? 0;
        }
        // Fallback to company-wide
        if (!$techId || $totalInsp === 0) {
            $totalInsp = $db->query(
                "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ?", [$companyId]
            )->getRow()->cnt ?? 0;
            $passInsp = $db->query(
                "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','pass')", [$companyId]
            )->getRow()->cnt ?? 0;
        }
        $compliancePct = $totalInsp > 0 ? round(($passInsp / $totalInsp) * 100) : 100;
        $complianceLabel = $compliancePct >= 90 ? 'Excellent' : ($compliancePct >= 75 ? 'Good' : ($compliancePct >= 50 ? 'Fair' : 'Needs Attention'));

        // ── Recent Inspections ──────────────────────────────────────────────
        $sql = "
            SELECT
                i.id, i.group_id, i.status, i.completed_at, i.inspection_type,
                COALESCE(i.asset_tag, e.asset_tag) AS asset_tag,
                COALESCE(i.make, e.make) AS make,
                COALESCE(i.model, e.model) AS model,
                s.name AS site_name,
                c.name AS customer_name
            FROM inspections i
            LEFT JOIN site_equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s     ON s.id = i.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            WHERE i.company_id = ?
              AND i.status IN ('Pass','Fail','Repair','pass','fail','repair')
        ";
        $params = [$companyId];
        if ($techId) {
            $sql .= " AND i.technician_id = ?";
            $params[] = $techId;
        }
        $sql .= " ORDER BY i.id DESC LIMIT 15";
        $recentInspections = $db->query($sql, $params)->getResultArray();

        return view('technician/dashboard', [
            'actionRequired'  => $actionRequired,
            'openRequests'    => $openRequests,
            'compliancePct'   => $compliancePct,
            'complianceLabel' => $complianceLabel,
            'recentInspections' => $recentInspections,
        ]);
    }

    /**
     * GET technician/search?q=...
     * Global search for the technician portal:
     * searches sites, equipment, and inspections scoped to this company.
     */
    public function search()
    {
        $companyId = (int) session('company_id');
        $q         = trim((string) $this->request->getGet('q'));

        if (strlen($q) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $db      = \Config\Database::connect();
        $results = [];
        $seen    = [];
        $push = function(array $row) use (&$results, &$seen) {
            $key = ($row['type'] ?? '') . '|' . ($row['label'] ?? '') . '|' . ($row['url'] ?? '');
            if (isset($seen[$key])) return;
            $seen[$key] = true;
            $results[] = $row;
        };

        $sites = $db->query("SELECT s.id, s.name, c.name AS customer_name FROM sites s LEFT JOIN customers c ON c.id = s.customer_id WHERE s.company_id = ? AND s.deleted_at IS NULL AND (s.name LIKE ? OR c.name LIKE ?) LIMIT 5", [$companyId, "%$q%", "%$q%"])->getResultArray();
        foreach ($sites as $r) {
            $push([
                'label'    => $r['name'],
                'subtitle' => 'Site — ' . ($r['customer_name'] ?? ''),
                'type'     => 'Site',
                'icon'     => 'fa-sitemap',
                'url'      => site_url('technician/sites/view/' . $r['id']),
            ]);
        }

        $equip = $db->query("SELECT e.id, e.asset_tag, e.site_id, CONCAT(COALESCE(e.make,''), ' ', COALESCE(e.model,'')) AS model_str, e.serial_number, e.device_type, s.name AS site_name FROM site_equipment e LEFT JOIN sites s ON s.id = e.site_id WHERE e.company_id = ? AND e.deleted_at IS NULL AND (e.asset_tag LIKE ? OR e.model LIKE ? OR e.make LIKE ? OR e.serial_number LIKE ?) LIMIT 8", [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"])->getResultArray();
        foreach ($equip as $r) {
            $push([
                'label'    => $r['asset_tag'] ?: trim($r['model_str']),
                'subtitle' => 'Equipment' . (!empty($r['device_type']) ? ' — ' . $r['device_type'] : '') . (!empty($r['site_name']) ? ' @ ' . $r['site_name'] : '') . (!empty($r['serial_number']) ? ' | S/N: ' . $r['serial_number'] : ''),
                'type'     => 'Equipment',
                'icon'     => 'fa-boxes-stacked',
                'url'      => !empty($r['site_id']) ? site_url('technician/sites/view/' . $r['site_id']) : site_url('technician/sites'),
                'nav'      => !empty($r['site_id']) ? [
                    'open_tab'     => 'equipment',
                    'asset_tag'    => $r['asset_tag'] ?? '',
                    'equipment_id' => $r['id'] ?? '',
                ] : null,
            ]);
        }

        $inspectionAssets = $db->query("SELECT i.group_id, i.site_id, COALESCE(i.asset_tag, e.asset_tag) AS asset_tag, COALESCE(i.make, e.make) AS make, COALESCE(i.model, e.model) AS model, COALESCE(i.serial_number, e.serial_number) AS serial_number, COALESCE(i.device_type, e.device_type) AS device_type, COALESCE(NULLIF(i.title,''), NULLIF(i.inspection_type,''), 'Inspection') AS inspection_title, s.name AS site_name FROM inspections i LEFT JOIN site_equipment e ON e.id = i.equipment_id LEFT JOIN sites s ON s.id = i.site_id WHERE i.company_id = ? AND i.deleted_at IS NULL AND (COALESCE(i.asset_tag, e.asset_tag) LIKE ? OR COALESCE(i.model, e.model) LIKE ? OR COALESCE(i.make, e.make) LIKE ? OR COALESCE(i.serial_number, e.serial_number) LIKE ?) ORDER BY i.id DESC LIMIT 8", [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"])->getResultArray();
        foreach ($inspectionAssets as $r) {
            $label = $r['asset_tag'] ?: trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? ''));
            $push([
                'label'    => $label,
                'subtitle' => 'Inspection Device' . (!empty($r['site_name']) ? ' @ ' . $r['site_name'] : '') . (!empty($r['group_id']) ? ' | ' . $r['group_id'] : '') . (!empty($r['serial_number']) ? ' | S/N: ' . $r['serial_number'] : ''),
                'type'     => 'Inspection Device',
                'icon'     => 'fa-magnifying-glass',
                'url'      => !empty($r['site_id']) ? site_url('technician/sites/view/' . $r['site_id']) : site_url('technician/inspections'),
                'nav'      => !empty($r['site_id']) && !empty($r['group_id']) ? [
                    'open_tab'         => 'inspections',
                    'group_id'         => $r['group_id'],
                    'inspection_title' => $r['inspection_title'] ?? 'Inspection',
                ] : null,
            ]);
        }

        $insps = $db->query("SELECT DISTINCT i.group_id, i.site_id, i.title, s.name AS site_name, c.name AS customer_name, i.inspection_type, i.status FROM inspections i LEFT JOIN sites s ON s.id = i.site_id LEFT JOIN customers c ON c.id = s.customer_id WHERE i.company_id = ? AND i.deleted_at IS NULL AND (i.group_id LIKE ? OR s.name LIKE ? OR c.name LIKE ? OR COALESCE(i.asset_tag, '') LIKE ?) LIMIT 8", [$companyId, "%$q%", "%$q%", "%$q%", "%$q%"])->getResultArray();
        foreach ($insps as $r) {
            $inspectionTitle = !empty($r['title']) ? $r['title'] : (!empty($r['inspection_type']) ? $r['inspection_type'] : 'Inspection');
            $push([
                'label'    => $r['group_id'] ?? '—',
                'subtitle' => 'Inspection — ' . ($r['customer_name'] ?? '') . ' / ' . ($r['site_name'] ?? ''),
                'type'     => 'Inspection',
                'icon'     => 'fa-clipboard-list',
                'url'      => !empty($r['site_id']) ? site_url('technician/sites/view/' . $r['site_id']) : site_url('technician/inspections'),
                'nav'      => !empty($r['site_id']) && !empty($r['group_id']) ? [
                    'open_tab'         => 'inspections',
                    'group_id'         => $r['group_id'],
                    'inspection_title' => $inspectionTitle,
                ] : null,
            ]);
        }

        return $this->response->setJSON(['results' => array_slice($results, 0, 12)]);
    }
}
