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
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ?", [$companyId]
        )->getRow()->cnt ?? 0);

        // ── KPI: Critical/High open work orders ───────────────────
        $criticalWO = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND priority IN ('critical','high') AND status != 'closed'",
            [$companyId]
        )->getRow()->cnt ?? 0);

        // ── KPI: Equipment operational % ──────────────────────────
        $totalEquipment = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM equipment WHERE company_id = ? AND deleted_at IS NULL", [$companyId]
        )->getRow()->cnt ?? 0);

        $readyEquipment = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM equipment WHERE company_id = ? AND deleted_at IS NULL AND status = 'ready'", [$companyId]
        )->getRow()->cnt ?? 0);

        $equipPct = $totalEquipment > 0 ? round(($readyEquipment / $totalEquipment) * 100) : 0;

        // ── KPI: Compliance score (pass rate) ─────────────────────
        $passCount = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','pass')", [$companyId]
        )->getRow()->cnt ?? 0);
        $complianceScore = $totalInspections > 0 ? round(($passCount / $totalInspections) * 100) : 100;

        // ── KPI: Pending/Open work orders ─────────────────────────
        $pendingWO = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status = 'open'", [$companyId]
        )->getRow()->cnt ?? 0);

        // ── Work Order Overview ────────────────────────────────────
        $woCompleted = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status IN ('closed','completed')", [$companyId]
        )->getRow()->cnt ?? 0);

        $woInProgress = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status = 'in_progress'", [$companyId]
        )->getRow()->cnt ?? 0);

        $woTotal = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ?", [$companyId]
        )->getRow()->cnt ?? 0);

        // ── Inspection Overview ────────────────────────────────────
        $inspCompleted = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','Fail','Repair','pass','fail','repair','completed')",
            [$companyId]
        )->getRow()->cnt ?? 0);
        $inspInProgress = max(0, $totalInspections - $inspCompleted);

        // ── Inventory Overview ─────────────────────────────────────
        // inventory table has: id, part_number, part_description, bin, row_aisle, shelf, qty, total_value
        // NO company_id column — query all inventory rows
        $invTotal = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inventory"
        )->getRow()->cnt ?? 0);

        // Low stock: qty > 0 AND qty <= 5 (threshold since no reorder_level column)
        $invLowStock = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM inventory WHERE qty > 0 AND qty <= 5"
        )->getRow()->cnt ?? 0);

        // Out of stock: qty = 0
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
            LEFT JOIN equipment e   ON e.id = wo.equipment_id
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
            if ($maxWO < 1) $maxWO = 1;
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
}
