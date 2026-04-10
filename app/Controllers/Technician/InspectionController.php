<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\EquipmentModel;
use App\Models\SiteEquipmentModel;
use App\Models\SiteModel;
use App\Models\TechnicianModel;
use App\Libraries\OperationalWorkOrderService;
use Dompdf\Dompdf;

class InspectionController extends BaseController
{
    private function resolveTechnicianId(): ?int
    {
        $userId    = (int) session('user_id');
        $companyId = (int) session('company_id');
        if ($userId === 0) return null;
        $techModel = new TechnicianModel();
        $row = $techModel->where('user_id', $userId)->where('company_id', $companyId)->first();
        if ($row) return (int) $row['id'];
        $row = $techModel->where('user_id', $userId)->first();
        return $row ? (int) $row['id'] : null;
    }

    public function index()
    {
        $companyId    = (int) session('company_id');
        $technicianId = $this->resolveTechnicianId();
        $db           = \Config\Database::connect();

        // Summary stats (scoped to this technician)
        $techFilter = $technicianId ? " AND i.technician_id = $technicianId" : '';

        $totalInspections = (int)($db->query(
            "SELECT COUNT(DISTINCT i.group_id) AS cnt FROM inspections i WHERE i.company_id = ?" . $techFilter,
            [$companyId]
        )->getRow()->cnt ?? 0);

        $sitesCount = (int)($db->query(
            "SELECT COUNT(DISTINCT i.site_id) AS cnt FROM inspections i WHERE i.company_id = ?" . $techFilter,
            [$companyId]
        )->getRow()->cnt ?? 0);

        $customersCount = (int)($db->query(
            "SELECT COUNT(DISTINCT s.customer_id) AS cnt FROM inspections i
             LEFT JOIN sites s ON s.id = i.site_id
             WHERE i.company_id = ?" . $techFilter,
            [$companyId]
        )->getRow()->cnt ?? 0);

        $equipmentCount = (int)($db->query(
            "SELECT COUNT(DISTINCT i.equipment_id) AS cnt FROM inspections i WHERE i.company_id = ?" . $techFilter,
            [$companyId]
        )->getRow()->cnt ?? 0);

        return view('technician/inspection/index', [
            'inspectionsCount' => $totalInspections,
            'sitesCount'       => $sitesCount,
            'customersCount'   => $customersCount,
            'equipmentCount'   => $equipmentCount,
        ]);
    }

    public function getEquipment()
    {
        $companyId = (int) session('company_id');
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');
        if ($assetTag === '' || $siteId === 0) return $this->response->setJSON(['found' => false]);
        $equipmentModel = new SiteEquipmentModel();
        $eq = $equipmentModel->findByAssetTag($companyId, $siteId, $assetTag);
        if (!$eq) return $this->response->setJSON(['found' => false]);
        return $this->response->setJSON([
            'found' => true,
            'id' => (int) $eq['id'],
            'asset_tag' => $eq['asset_tag'],
            'make' => $eq['make'] ?? '',
            'model' => $eq['model'] ?? '',
            'device_type' => $eq['device_type'] ?? '',
            'serial_number' => $eq['serial_number'] ?? '',
            'department' => $eq['department'] ?? '',
            'location' => $eq['location'] ?? '',
            'est' => $eq['est'] ?? '0',
            'cal' => $eq['cal'] ?? '0',
        ]);
    }

    public function searchByModel()
    {
        $companyId = (int) session('company_id');
        $keyword   = $this->request->getGet('keyword');
        if (empty($keyword) || strlen($keyword) < 2) return $this->response->setJSON([]);
        $equipmentModel = new EquipmentModel();
        $results = $equipmentModel->where('company_id', $companyId)
            ->groupStart()->like('model', $keyword)->orLike('make', $keyword)->orLike('device_type', $keyword)->groupEnd()
            ->select('id, asset_tag, make, model, serial_number, device_type, location, department, est, cal')
            ->findAll(20);
        $seen = [];
        $unique = [];
        foreach ($results as $row) {
            $key = strtolower(($row['make'] ?? '') . '|' . ($row['model'] ?? ''));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $row;
            }
        }
        return $this->response->setJSON($unique);
    }

    public function siteInventory()
    {
        $companyId = (int) session('company_id');
        $siteId    = (int) $this->request->getGet('site_id');
        if ($siteId === 0) return $this->response->setJSON(['success' => false, 'equipment' => []]);
        $equipmentModel = new SiteEquipmentModel();
        $equipment = $equipmentModel->forSite($companyId, $siteId);
        return $this->response->setJSON(['success' => true, 'equipment' => $equipment]);
    }

    public function groupItems()
    {
        $companyId = (int) session('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));
        if ($groupId === '') return $this->response->setJSON(['success' => false, 'data' => []]);
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type,
                          e.asset_tag, e.department, e.location, u.full_name as technician_name');
        $builder->join('site_equipment e', 'e.id = i.equipment_id', 'left');
        $builder->join('technicians t', 't.id = i.technician_id', 'left');
        $builder->join('users u',       'u.id = t.user_id',       'left');
        $builder->where('i.company_id', $companyId)->where('i.group_id', $groupId)->orderBy('i.created_at', 'ASC');
        return $this->response->setJSON(['success' => true, 'data' => $builder->get()->getResultArray()]);
    }

    public function groupWorkOrders()
    {
        $companyId = (int) session('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $siteId    = (int) $this->request->getGet('site_id');
        $db = \Config\Database::connect();
        $builder = $db->table('work_orders wo');
        $builder->select('wo.*, e.asset_tag, e.serial_number, u.full_name as assigned_to_name');
        $builder->join('site_equipment e', 'e.id = wo.equipment_id', 'left');
        $builder->join('technicians t', 't.id = wo.assigned_to', 'left');
        $builder->join('users u', 'u.id = t.user_id', 'left');
        $builder->where('wo.company_id', $companyId);
        if ($groupId !== '') $builder->where('wo.group_id', $groupId);
        elseif ($siteId > 0) $builder->where('wo.site_id', $siteId);
        else return $this->response->setJSON(['success' => false, 'data' => [], 'message' => 'Missing group_id or site_id']);
        $builder->orderBy('wo.created_at', 'DESC');
        return $this->response->setJSON(['success' => true, 'data' => $builder->get()->getResultArray()]);
    }

    public function createWorkOrder()
    {
        if (!$this->request->is('post')) return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        $companyId    = (int) session('company_id');
        $userId       = (int) session('user_id');
        $technicianId = $this->resolveTechnicianId();
        if (!$technicianId) return $this->response->setJSON(['success' => false, 'message' => 'Technician not found']);
        $siteId      = (int) $this->request->getPost('site_id');
        $equipmentId = (int) $this->request->getPost('equipment_id');
        $groupId     = trim((string) $this->request->getPost('group_id'));
        $title       = trim((string) $this->request->getPost('title'));
        if ($siteId <= 0)    return $this->response->setJSON(['success' => false, 'message' => 'Site is required']);
        if ($equipmentId <= 0) return $this->response->setJSON(['success' => false, 'message' => 'Equipment is required']);
        if ($groupId === '') return $this->response->setJSON(['success' => false, 'message' => 'Inspection group ID is missing.']);
        if ($title === '')   return $this->response->setJSON(['success' => false, 'message' => 'Title is required']);
        $data = [
            'company_id' => $companyId,
            'site_id' => $siteId,
            'equipment_id' => $equipmentId,
            'group_id' => $groupId,
            'title' => $title,
            'description' => trim((string) $this->request->getPost('description')),
            'status' => strtolower(trim((string) $this->request->getPost('status'))),
            'priority' => strtolower(trim((string) $this->request->getPost('priority'))),
            'assigned_to' => $technicianId,
            'created_by' => $userId,
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $db = \Config\Database::connect();
        if ($db->table('work_orders')->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Work order created successfully', 'id' => $db->insertID(), 'group_id' => $groupId]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Insert failed']);
    }

    public function record()
    {
        if (!$this->request->is('post')) return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        $companyId     = (int) session('company_id');
        $userId        = (int) session('user_id');
        $siteId        = (int) $this->request->getPost('site_id');
        $assetTag      = trim((string) $this->request->getPost('asset_tag'));
        $result        = trim((string) $this->request->getPost('result'));
        $notes         = trim((string) $this->request->getPost('notes'));
        $dept          = trim((string) $this->request->getPost('department'));
        $room          = trim((string) $this->request->getPost('room'));
        $serial        = trim((string) $this->request->getPost('serial_number'));
        $action        = trim((string) $this->request->getPost('action_performed'));
        $pmFreq        = trim((string) $this->request->getPost('pm_frequency'));
        $est           = trim((string) $this->request->getPost('est'));
        $cal           = trim((string) $this->request->getPost('cal'));
        $assetNotFound = $this->request->getPost('asset_not_found');
        if ($siteId === 0 || $result === '') return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);

        $technicianId   = $this->resolveTechnicianId();
        $equipmentModel = new EquipmentModel();
        $inspectionModel = new InspectionModel();
        $equipmentId    = 0;

        if ($assetNotFound === '1') {
            $manufacturer = trim((string) $this->request->getPost('manufacturer'));
            $modelName    = trim((string) $this->request->getPost('model_name'));
            $description  = trim((string) $this->request->getPost('description'));
            $newAssetTag  = $assetTag !== '' ? $assetTag : ('NEW-' . strtoupper(uniqid()));
            $existing     = $equipmentModel->where('company_id', $companyId)->where('site_id', $siteId)->where('asset_tag', $newAssetTag)->first();
            if ($existing) {
                $equipmentId = (int) $existing['id'];
            } else {
                $equipmentModel->insert([
                    'company_id' => $companyId,
                    'site_id' => $siteId,
                    'asset_tag' => $newAssetTag,
                    'make' => $manufacturer,
                    'model' => $modelName,
                    'serial_number' => $serial,
                    'device_type' => $description,
                    'department' => $dept,
                    'location' => $room,
                    'status' => 'Pending'
                ]);
                $equipmentId = (int) $equipmentModel->getInsertID();
            }
            $assetTag = $newAssetTag;
        } else {
            $eq = $equipmentModel->where('company_id', $companyId)->where('site_id', $siteId)->where('asset_tag', $assetTag)->first();
            if (!$eq) return $this->response->setJSON(['success' => false, 'message' => 'Equipment not found']);
            $equipmentId = (int) $eq['id'];
        }

        $updateFields = [];
        if ($dept !== '') $updateFields['department'] = $dept;
        if ($room !== '') $updateFields['location']   = $room;
        // Serial number is NOT auto-updated from inspection — technician fills it in addDevice
        if (!empty($updateFields)) $equipmentModel->update($equipmentId, $updateFields);

        $existingGroupId = trim((string) $this->request->getPost('group_id'));
        $groupId = $existingGroupId !== '' ? $existingGroupId : ('INSP-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)));

        $nextDueDate = null;
        if ($pmFreq !== '') {
            preg_match('/^(\d+)/', $pmFreq, $m);
            $months = isset($m[1]) ? (int) $m[1] : 0;
            if ($months > 0) $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
        }

        $now     = date('Y-m-d H:i:s');
        $insData = [
            'company_id' => $companyId,
            'site_id' => $siteId,
            'equipment_id' => $equipmentId,
            'group_id' => $groupId,
            'scheduled_at' => $now,
            'completed_at' => $now,
            'status' => $result,
            'technician_id' => $technicianId,
            'findings' => $assetNotFound === '1'
                ? 'Asset not found in inventory. Manufacturer: ' . $this->request->getPost('manufacturer')
                . '; Model: ' . $this->request->getPost('model_name') . '; Serial #: ' . $serial : '',
            'notes' => $notes,
            'inspection_type' => $action,
            'est' => $est,
            'cal' => $cal,
            'pm_frequency' => $pmFreq,
            'next_due_date' => $nextDueDate,
            'device_complete' => 'Yes',
            'created_by' => $userId > 0 ? $userId : null,
        ];

        $existingInspection = $inspectionModel->where('equipment_id', $equipmentId)->where('group_id', $groupId)->where('site_id', $siteId)->first();
        if ($existingInspection) $inspectionModel->update($existingInspection['id'], $insData);
        else $inspectionModel->insert($insData);

        $statusMap = ['Pass' => 'Ready', 'Fail' => 'Needs Attention', 'Repair' => 'Repair'];
        if (isset($statusMap[$result])) $equipmentModel->update($equipmentId, ['status' => $statusMap[$result]]);

        (new OperationalWorkOrderService())->syncFollowUpFromInspection([
            'company_id'      => $companyId,
            'site_id'         => $siteId,
            'equipment_id'    => $equipmentId,
            'group_id'        => $groupId,
            'status'          => $result,
            'inspection_type' => $action,
            'notes'           => $notes,
            'asset_tag'       => $assetTag,
            'technician_id'   => $technicianId,
            'created_by'      => $userId > 0 ? $userId : null,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Inspection recorded successfully',
            'group_id' => $groupId,
            'asset_tag' => $assetTag,
            'inspection_type' => $action
        ]);
    }

    public function deleteRecord($id)
    {
        $companyId = (int) session('company_id');
        (new InspectionModel())->where('company_id', $companyId)->where('id', (int) $id)->delete();
        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    public function reportData($groupId = null)
    {
        $companyId = (int) session('company_id');
        if (!$companyId) return $this->response->setJSON(['success' => false, 'message' => 'Session expired']);
        if (empty($groupId)) $groupId = $this->request->getGet('group_id');
        if (empty($groupId)) return $this->response->setJSON(['success' => false, 'message' => 'Missing group_id parameter']);
        $inspectionModel = new InspectionModel();
        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;
        return $this->response->setJSON(['success' => true, 'group_id' => $groupId, 'latest' => $latest, 'rows' => $rows]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // reportPdf — proper PDF download using Dompdf (no class_exists check)
    // ─────────────────────────────────────────────────────────────────────────
    public function reportPdf($groupId)
    {
        $companyId       = (int) session('company_id');
        $inspectionModel = new InspectionModel();
        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        // Build the full standalone HTML page for PDF rendering
        $html = $this->buildReportHtml($latest, $rows ?? [], $groupId, false);

        // Use Dompdf if available (no Options class required)
        try {
            if (!class_exists('\Dompdf\Dompdf')) {
                throw new \Exception('Dompdf not installed');
            }
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $pdfOutput = $dompdf->output();

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="inspection-report-' . $groupId . '.pdf"')
                ->setHeader('Content-Length', (string) strlen($pdfOutput))
                ->setHeader('Cache-Control', 'private, max-age=0, must-revalidate')
                ->setBody($pdfOutput);
        } catch (\Throwable $e) {
            log_message('info', '[reportPdf] Falling back to HTML: ' . $e->getMessage());
            // Fallback: serve as printable HTML
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=utf-8')
                ->setBody($html);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // reportPreview — returns JSON { success, html } so the view can inject
    // the report INLINE into the existing modal (no new tab)
    // ─────────────────────────────────────────────────────────────────────────
    public function reportPreview($groupId)
    {
        $companyId       = (int) session('company_id');
        $inspectionModel = new InspectionModel();
        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        // Use the same shared view as admin — supports ?inline=1 for dark theme
        $html = view('admin/inspections/report_pdf', [
            'latest'  => $latest,
            'rows'    => $rows,
            'groupId' => $groupId,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=utf-8')
            ->setBody($html);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ── Public proxy so other controllers (Customer) can render reports ──────
    public function buildReportHtmlPublic(?array $latest, array $rows, string $groupId): string
    {
        return $this->buildReportHtml($latest, $rows, $groupId, false);
    }

    // buildReportHtml — admin-matching "Needs Attention / Passed" layout
    // previewMode = true  → inner content only (injected into modal)
    // previewMode = false → full standalone page (for PDF / print)
    // ─────────────────────────────────────────────────────────────────────────
    private function buildReportHtml(?array $latest, array $rows, string $groupId, bool $previewMode = false): string
    {
        $h = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');

        $siteName = $h($latest['site_name']        ?? $latest['customer_name'] ?? '—');
        $techName = $h($latest['technician_name']  ?? 'N/A');
        $action   = $h($latest['action_performed'] ?? $latest['inspection_type'] ?? '');
        $logoHtml = '';
        if (!empty($latest['logo_path'])) {
            $logoHtml = '<img src="' . base_url('uploads/logos/' . $latest['logo_path']) . '" style="height:55px;" alt="Logo">';
        }

        // Partition
        $failed = $repair = $passed = [];
        foreach ($rows as $r) {
            $st = strtolower(trim((string)($r['result'] ?? $r['status'] ?? '')));
            if ($st === 'fail')       $failed[]  = $r;
            elseif ($st === 'repair') $repair[]  = $r;
            else                      $passed[]  = $r;
        }
        $needsAttn = array_merge($failed, $repair);

        $colHeaders = '<th>Result</th><th>Model</th><th>Type</th><th>S/N</th>'
            . '<th>Action Performed</th><th>Asset #</th><th>Dept</th><th>Room</th>'
            . '<th>Tech</th><th>Inspection Date</th><th>Notes</th>';

        $th = 'padding:9px 12px;text-align:left;font-size:10px;font-weight:600;color:#475569;'
            . 'text-transform:uppercase;letter-spacing:0.05em;border-bottom:2px solid #e2e8f0;'
            . 'background:#f8fafc;white-space:nowrap;';
        $td = 'padding:9px 12px;border-bottom:1px solid #f1f5f9;font-size:12px;vertical-align:middle;';

        $buildRows = function (array $items, bool $isAttn) use ($h, $td): string {
            if (empty($items)) {
                $msg = $isAttn ? 'No failed or repair items — all devices passed!' : 'No passed items yet.';
                return '<tr><td colspan="11" style="text-align:center;color:#94a3b8;padding:14px;font-style:italic;">' . $msg . '</td></tr>';
            }
            $out = '';
            foreach ($items as $r) {
                $res = strtolower(trim((string)($r['result'] ?? $r['status'] ?? '')));
                $badge = $res === 'pass'
                    ? '<span style="color:#16a34a;font-weight:600;">&#10003; Pass</span>'
                    : ($res === 'fail'
                        ? '<span style="color:#dc2626;font-weight:600;">&#10007; Fail</span>'
                        : '<span style="color:#d97706;font-weight:600;">&#9881; Repair</span>');
                $date = !empty($r['inspection_date']) ? substr($r['inspection_date'], 0, 16) : '—';
                $out .= '<tr>'
                    . '<td style="' . $td . '">' . $badge . '</td>'
                    . '<td style="' . $td . '">' . $h($r['model'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['device_type'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['serial_number'] ?? 'N/A') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['action_performed'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['asset_tag'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['dept'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['room'] ?? '—') . '</td>'
                    . '<td style="' . $td . '">' . $h($r['technician_name'] ?? 'N/A') . '</td>'
                    . '<td style="' . $td . '">' . $date . '</td>'
                    . '<td style="' . $td . 'max-width:160px;">' . $h($r['notes'] ?? '') . '</td>'
                    . '</tr>';
                if ($isAttn && $res === 'fail') {
                    $out .= '<tr><td colspan="11" style="background:#fef2f2;color:#991b1b;font-weight:600;'
                        . 'font-size:11px;padding:5px 12px;border-left:3px solid #dc2626;">'
                        . '[!] Attention Required — Equipment Failure</td></tr>';
                }
            }
            return $out;
        };

        $sectionHead = function (string $icon, string $label, int $count, string $color): string {
            return '<div style="display:flex;align-items:center;gap:10px;margin:28px 0 5px;">'
                . '<span style="font-size:18px;">' . $icon . '</span>'
                . '<h3 style="font-size:16px;font-weight:700;color:' . $color . ';margin:0;">' . $label . '</h3>'
                . '<span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;'
                . 'border-radius:50%;background:' . $color . ';color:#fff;font-size:11px;font-weight:700;">' . $count . '</span>'
                . '</div>';
        };

        $subText = fn(string $msg) => '<p style="font-size:12px;color:#64748b;margin-bottom:10px;">' . $msg . '</p>';

        $tableBlock = fn(string $rows) =>
        '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;">'
            . '<thead><tr>' . $colHeaders . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '</table></div>';

        // Header block
        $header =
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;'
            . 'padding-bottom:18px;margin-bottom:4px;border-bottom:2px solid #e2e8f0;">'
            . '<div>'
            . '<h2 style="font-size:20px;font-weight:700;color:#0f172a;margin-bottom:4px;">Site: ' . $siteName . '</h2>'
            . '<p style="color:#64748b;font-size:13px;font-style:italic;">' . $action . '</p>'
            . '</div>'
            . '<div>' . $logoHtml . '</div>'
            . '<div style="text-align:right;">'
            . '<p style="font-size:12px;color:#64748b;margin-bottom:3px;"><strong>Inspection #:</strong> ' . $h($groupId) . '</p>'
            . '<p style="font-size:12px;color:#64748b;margin-bottom:3px;"><strong>Technician:</strong> ' . $techName . '</p>'
            . '<p style="font-size:12px;color:#64748b;margin-bottom:3px;"><strong>Generated:</strong> ' . date('M d, Y g:i A') . '</p>'
            . '</div>'
            . '</div>';

        $body = $header
            . $sectionHead('&#9888;', 'Needs Attention', count($needsAttn), '#dc2626')
            . $subText('Devices that failed or require repair.')
            . $tableBlock($buildRows($needsAttn, true))
            . $sectionHead('&#10003;', 'Passed', count($passed), '#16a34a')
            . $subText('Devices that passed inspection.')
            . $tableBlock($buildRows($passed, false));

        // ── Preview mode: inject-ready HTML snippet ───────────────────────────
        if ($previewMode) {
            return '<style>
                table{width:100%;border-collapse:collapse;}
                th{' . $th . '}
                td{' . $td . '}
            </style>' . $body;
        }

        // ── PDF / print mode: full standalone page ────────────────────────────
        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inspection Report &mdash; ' . $h($groupId) . '</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#1e293b;background:#f1f5f9;padding:24px;}
  .page{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.08);padding:32px;max-width:1280px;margin:0 auto;}
  table{width:100%;border-collapse:collapse;}
  th{' . $th . '}
  td{' . $td . '}
  tr:last-child td{border-bottom:none;}
  .print-btn{position:fixed;bottom:28px;right:28px;background:#6366f1;color:#fff;border:none;
             padding:13px 26px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;
             box-shadow:0 4px 14px rgba(99,102,241,.4);}
  .print-btn:hover{background:#4f46e5;}
  @media print{.print-btn{display:none;}body{background:#fff;padding:0;}.page{box-shadow:none;border-radius:0;}}
</style>
</head>
<body>
<div class="page">' . $body . '</div>
<button class="print-btn" onclick="window.print()">&#128438; Print / Save as PDF</button>
</body>
</html>';
    }
    /**
     * Return all inspections for this technician as JSON for the reports DataTable.
     * Route: GET /technician/inspections/listData
     */
    public function listData()
    {
        $companyId    = (int) session('company_id');
        $technicianId = $this->resolveTechnicianId();

        if (!$companyId) {
            return $this->response->setJSON(['data' => []]);
        }

        $db = \Config\Database::connect();

        $sql = "
            SELECT
                i.id,
                i.group_id,
                -- Group-level status: 'open' if ANY device in the group has no pass/fail/repair result,
                -- otherwise use the representative row's status for display in Closed view.
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM inspections sub
                        WHERE sub.group_id = i.group_id
                          AND sub.company_id = i.company_id
                          AND (sub.status IS NULL OR sub.status = '' OR sub.status NOT IN ('Pass','Fail','Repair','pass','fail','repair','completed'))
                    ) THEN 'In Progress'
                    ELSE i.status
                END                AS result,
                i.notes,
                i.inspection_type  AS action_performed,
                i.completed_at     AS inspection_date,
                i.next_due_date,
                i.est,
                i.cal,
                i.pm_frequency,
                COALESCE(e.asset_tag, i.asset_tag) AS asset_tag,
                COALESCE(e.make, i.make)            AS make,
                COALESCE(e.model, i.model)          AS model,
                COALESCE(e.device_type, i.device_type) AS device_type,
                COALESCE(e.serial_number, i.serial_number) AS serial_number,
                COALESCE(e.department, i.department) AS department,
                COALESCE(e.location, i.location)    AS room,
                s.name             AS site_name,
                c.name             AS customer_name,
                u.full_name        AS technician_name
            FROM inspections i
            LEFT JOIN site_equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s      ON s.id = i.site_id
            LEFT JOIN customers c  ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u      ON u.id = t.user_id
            WHERE i.company_id = ?
        ";
        $params = [$companyId];

        if ($technicianId) {
            $sql .= " AND i.technician_id = ?";
            $params[] = $technicianId;
        }

        $sql .= " GROUP BY i.group_id ORDER BY i.id DESC LIMIT 500";

        $rows = $db->query($sql, $params)->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }

}