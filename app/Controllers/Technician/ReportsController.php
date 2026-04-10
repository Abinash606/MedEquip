<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class ReportsController extends BaseController
{
    public function index()
    {
        $companyId    = (int) session('company_id');
        $db           = \Config\Database::connect();

        // Resolve technician ID
        $techRow = $db->query(
            "SELECT id FROM technicians WHERE user_id = ? AND company_id = ? AND deleted_at IS NULL LIMIT 1",
            [(int) session('user_id'), $companyId]
        )->getRow();
        $technicianId = $techRow ? (int) $techRow->id : null;

        // Fetch all inspection groups for this technician, grouped by group_id
        // Returns one row per inspection group (most recent record wins)
        $techFilter = $technicianId ? " AND i.technician_id = {$technicianId}" : '';

        $reports = $db->query("
            SELECT
                i.group_id,
                i.inspection_type,
                i.status,
                i.completed_at,
                i.scheduled_at,
                s.name        AS site_name,
                c.name        AS customer_name,
                COUNT(i.id)   AS device_count
            FROM inspections i
            LEFT JOIN sites s      ON s.id = i.site_id
            LEFT JOIN customers c  ON c.id = s.customer_id
            WHERE i.company_id = ?{$techFilter}
              AND i.group_id IS NOT NULL
              AND i.group_id != ''
            GROUP BY i.group_id
            ORDER BY COALESCE(i.completed_at, i.scheduled_at) DESC
            LIMIT 100
        ", [$companyId])->getResultArray();

        return view('technician/report/index', [
            'reports' => $reports,
        ]);
    }
}
