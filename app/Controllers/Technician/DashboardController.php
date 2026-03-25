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
            "SELECT COUNT(*) AS cnt FROM equipment WHERE company_id = ? AND deleted_at IS NULL AND status IN ('need_attention','out_of_service')",
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
        // Fallback: all open WOs if no specific technician
        if (!$techId) {
            $openRequests = $db->query(
                "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status NOT IN ('closed','completed') AND deleted_at IS NULL",
                [$companyId]
            )->getRow()->cnt ?? 0;
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
                e.asset_tag, e.make, e.model,
                s.name AS site_name,
                c.name AS customer_name
            FROM inspections i
            LEFT JOIN equipment e ON e.id = i.equipment_id
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
}
