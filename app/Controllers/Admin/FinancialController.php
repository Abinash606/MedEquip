<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class FinancialController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        $db = \Config\Database::connect();

        // Revenue = sum of work order estimated costs (or count * avg rate)
        // Since no billing table exists, we derive from work_orders and inspections
        // Total inspections done = revenue proxy, work orders = cost proxy

        $totalInspections = $db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ?", [$companyId]
        )->getRow()->cnt ?? 0;

        $totalWorkOrders = $db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ?", [$companyId]
        )->getRow()->cnt ?? 0;

        // Operational estimate assumptions only — not invoice or accounting data
        $serviceValuePerInspection = 150;
        $serviceLoadPerWorkOrder   = 80;
        $estimatedServiceValue = $totalInspections * $serviceValuePerInspection;
        $estimatedServiceLoad  = $totalWorkOrders  * $serviceLoadPerWorkOrder;
        $estimatedNetCapacity  = $estimatedServiceValue - $estimatedServiceLoad;
        $operationalMargin     = $estimatedServiceValue > 0 ? round(($estimatedNetCapacity / $estimatedServiceValue) * 100) : 0;

        // Per-customer breakdown
        $customerStats = $db->query("
            SELECT
                c.name AS customer_name,
                COUNT(DISTINCT i.id)  AS inspection_count,
                COUNT(DISTINCT wo.id) AS wo_count,
                COUNT(DISTINCT s.id)  AS site_count,
                COUNT(DISTINCT e.id)  AS equipment_count
            FROM customers c
            LEFT JOIN sites s       ON s.customer_id = c.id AND s.deleted_at IS NULL
            LEFT JOIN inspections i ON i.site_id = s.id
            LEFT JOIN work_orders wo ON wo.site_id = s.id
            LEFT JOIN equipment e   ON e.site_id = s.id AND e.deleted_at IS NULL
            WHERE c.company_id = ?
              AND c.deleted_at IS NULL
            GROUP BY c.id, c.name
            ORDER BY inspection_count DESC
        ", [$companyId])->getResultArray();

        // Monthly inspection trend (last 6 months)
        $monthlyTrend = $db->query("
            SELECT
                DATE_FORMAT(completed_at, '%Y-%m') AS month,
                COUNT(*) AS total,
                SUM(CASE WHEN status IN ('Pass','pass') THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN status IN ('Fail','fail') THEN 1 ELSE 0 END) AS failed
            FROM inspections
            WHERE company_id = ?
              AND completed_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ", [$companyId])->getResultArray();

        // Equipment count and compliance rate
        $equipCount = $db->query(
            "SELECT COUNT(*) AS cnt FROM equipment WHERE company_id = ? AND deleted_at IS NULL", [$companyId]
        )->getRow()->cnt ?? 0;

        $passCount  = $db->query(
            "SELECT COUNT(*) AS cnt FROM inspections WHERE company_id = ? AND status IN ('Pass','pass')", [$companyId]
        )->getRow()->cnt ?? 0;

        $complianceRate = $totalInspections > 0
            ? round(($passCount / $totalInspections) * 100) : 0;

        return view('admin/financials/index', [
            'estimatedServiceValue' => $estimatedServiceValue,
            'estimatedServiceLoad'  => $estimatedServiceLoad,
            'estimatedNetCapacity'  => $estimatedNetCapacity,
            'operationalMargin'     => $operationalMargin,
            'totalInspections'=> $totalInspections,
            'totalWorkOrders' => $totalWorkOrders,
            'equipCount'      => $equipCount,
            'complianceRate'  => $complianceRate,
            'customerStats'   => $customerStats,
            'monthlyTrend'    => $monthlyTrend,
            'serviceValuePerInspection' => $serviceValuePerInspection,
            'serviceLoadPerWorkOrder' => $serviceLoadPerWorkOrder,
        ]);
    }
}
