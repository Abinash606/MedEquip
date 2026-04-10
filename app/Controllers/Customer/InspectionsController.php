<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\SiteModel;
use App\Models\EquipmentModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $db         = \Config\Database::connect();

        // Resolve the site IDs for this specific customer
        $siteSql    = "SELECT id FROM sites WHERE company_id = ? AND deleted_at IS NULL";
        $siteParams = [$companyId];
        if ($customerId) {
            $siteSql    .= " AND customer_id = ?";
            $siteParams[] = $customerId;
        }
        $siteRows = $db->query($siteSql, $siteParams)->getResultArray();
        $siteIds  = array_column($siteRows, 'id');

        if (empty($siteIds)) {
            return view('customer/inspections/index', [
                'upcomingInspections' => [],
                'inspectionHistory'   => [],
                'sites'               => [],
            ]);
        }

        $inList = implode(',', array_map('intval', $siteIds));

        // Upcoming = open/in-progress inspections for this customer's sites
        $upcoming = $db->query("
            SELECT i.id, i.group_id, i.status, i.site_id, i.scheduled_at, i.completed_at,
                   i.next_due_date, i.inspection_type,
                   e.make, e.model, e.device_type, e.asset_tag, e.serial_number,
                   s.name AS site_name
            FROM inspections i
            LEFT JOIN site_equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s     ON s.id = i.site_id
            WHERE i.site_id IN ($inList)
              AND i.status NOT IN ('Pass','Fail','Repair','pass','fail','repair','completed','Closed/Complete')
            GROUP BY i.group_id
            ORDER BY i.scheduled_at ASC
        ")->getResultArray();

        // History = completed inspections
        $history = $db->query("
            SELECT i.id, i.group_id, i.status, i.site_id, i.scheduled_at, i.completed_at,
                   i.next_due_date, i.inspection_type,
                   e.make, e.model, e.device_type, e.asset_tag, e.serial_number,
                   s.name AS site_name,
                   u.full_name AS technician_name
            FROM inspections i
            LEFT JOIN site_equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE i.site_id IN ($inList)
              AND i.status IN ('Pass','Fail','Repair','pass','fail','repair','completed','Closed/Complete')
            GROUP BY i.group_id
            ORDER BY i.completed_at DESC
            LIMIT 200
        ")->getResultArray();

        // Site list for filtering dropdown
        $sites = $db->query(
            "SELECT id, name FROM sites WHERE id IN ($inList) ORDER BY name"
        )->getResultArray();

        // Summary counts
        $totalInspections = count($upcoming) + count($history);
        $completedCount   = count($history);
        $openCount        = count($upcoming);
        $equipmentCount   = (int)($db->query(
            "SELECT COUNT(DISTINCT equipment_id) AS cnt FROM inspections WHERE site_id IN ($inList)"
        )->getRow()->cnt ?? 0);

        return view('customer/inspections/index', [
            'upcomingInspections' => $upcoming,
            'inspectionHistory'   => $history,
            'sites'               => $sites,
            'inspectionsCount'    => $totalInspections,
            'openCount'           => $openCount,
            'completedCount'      => $completedCount,
            'sitesCount'          => count($sites),
            'equipmentCount'      => $equipmentCount,
        ]);
    }

    /**
     * Proxy to the admin inspection PDF/preview — customers can view reports for their sites.
     */
    public function reportPdf($groupId)
    {
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $db         = \Config\Database::connect();

        // Security: verify this group_id belongs to this customer's sites
        $check = $db->query("
            SELECT i.id FROM inspections i
            INNER JOIN sites s ON s.id = i.site_id
            WHERE i.group_id = ? AND s.company_id = ?
            " . ($customerId ? " AND s.customer_id = $customerId" : "") . "
            LIMIT 1
        ", [$groupId, $companyId])->getRow();

        if (!$check) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        // Use InspectionModel directly — avoids instantiating admin controller
        // which has a `use Dompdf\Dompdf` that throws if Dompdf isn't installed
        $inspectionModel = new \App\Models\InspectionModel();
        $rows   = $inspectionModel->getReportRowsByGroup((int)$companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        // Delegate to technician controller's buildReportHtml which is self-contained
        $techController = new \App\Controllers\Technician\InspectionController();
        $html = $techController->buildReportHtmlPublic($latest, $rows ?? [], $groupId);

        // Try Dompdf, fall back to HTML
        try {
            if (!class_exists('\Dompdf\Dompdf')) throw new \Exception('Dompdf not installed');
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="inspection-report-' . $groupId . '.pdf"')
                ->setBody($dompdf->output());
        } catch (\Throwable $e) {
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=utf-8')
                ->setBody($html);
        }
    }
}
