<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\EquipmentModel;
use App\Models\SiteModel;
use App\Models\TechnicianModel;
use Dompdf\Dompdf;

class InspectionController extends BaseController
{
    private function resolveTechnicianId(): ?int
    {
        $userId    = (int) session('user_id');
        $companyId = (int) session('company_id');

        if ($userId === 0) return null;

        $techModel = new TechnicianModel();

        $row = $techModel
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if ($row) return (int) $row['id'];

        $row = $techModel->where('user_id', $userId)->first();

        return $row ? (int) $row['id'] : null;
    }


    public function index()
    {
        $companyId    = (int) session('company_id');
        $technicianId = $this->resolveTechnicianId();

        $siteModel = new SiteModel();
        $sites     = $siteModel->where('company_id', $companyId)->findAll();
        $db      = \Config\Database::connect();
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type,
                          e.asset_tag, e.department, e.location,
                          s.name as site_name,
                          u.full_name as technician_name');
        $builder->join('equipment e',   'e.id = i.equipment_id',  'left');
        $builder->join('sites s',       's.id = i.site_id',       'left');
        $builder->join('technicians t', 't.id = i.technician_id', 'left');
        $builder->join('users u',       'u.id = t.user_id',       'left');
        $builder->where('i.company_id', $companyId);
        if ($technicianId) {
            $builder->where('i.technician_id', $technicianId);
        }

        $builder->groupBy('i.group_id');
        $builder->orderBy('i.created_at', 'DESC');

        $inspections = $builder->get()->getResultArray();

        return view('technician/inspection/index', [
            'sites'       => $sites,
            'inspections' => $inspections,
        ]);
    }
    public function getEquipment()
    {
        $companyId = (int) session('company_id');
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');

        if ($assetTag === '' || $siteId === 0) {
            return $this->response->setJSON(['found' => false]);
        }

        $equipmentModel = new EquipmentModel();
        $eq = $equipmentModel
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('asset_tag', $assetTag)
            ->first();

        if (!$eq) {
            return $this->response->setJSON(['found' => false]);
        }

        return $this->response->setJSON([
            'found'         => true,
            'id'            => (int) $eq['id'],
            'asset_tag'     => $eq['asset_tag'],
            'make'          => $eq['make']          ?? '',
            'model'         => $eq['model']         ?? '',
            'device_type'   => $eq['device_type']   ?? '',
            'serial_number' => $eq['serial_number'] ?? '',
            'department'    => $eq['department']    ?? '',
            'location'      => $eq['location']      ?? '',
            'est'           => $eq['est']           ?? '0',
            'cal'           => $eq['cal']           ?? '0',
        ]);
    }
    public function searchByModel()
    {
        $companyId = (int) session('company_id');
        $keyword   = $this->request->getGet('keyword');

        if (empty($keyword) || strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }

        $equipmentModel = new EquipmentModel();
        $results = $equipmentModel
            ->where('company_id', $companyId)
            ->groupStart()
            ->like('model', $keyword)
            ->orLike('make', $keyword)
            ->orLike('device_type', $keyword)
            ->groupEnd()
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

        if ($siteId === 0) {
            return $this->response->setJSON(['success' => false, 'equipment' => []]);
        }

        $equipmentModel = new EquipmentModel();
        $equipment = $equipmentModel
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->findAll();

        return $this->response->setJSON(['success' => true, 'equipment' => $equipment]);
    }

    public function groupItems()
    {
        $companyId = (int) session('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));

        if ($groupId === '') {
            return $this->response->setJSON(['success' => false, 'data' => []]);
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type,
                          e.asset_tag, e.department, e.location,
                          u.full_name as technician_name');
        $builder->join('equipment e',   'e.id = i.equipment_id',  'left');
        $builder->join('technicians t', 't.id = i.technician_id', 'left');
        $builder->join('users u',       'u.id = t.user_id',       'left');
        $builder->where('i.company_id', $companyId);
        $builder->where('i.group_id', $groupId);
        $builder->orderBy('i.created_at', 'ASC');

        $rows = $builder->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }
    public function groupWorkOrders()
    {
        $companyId = (int) session('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $siteId    = (int) $this->request->getGet('site_id');

        $db      = \Config\Database::connect();
        $builder = $db->table('work_orders wo');

        $builder->select('wo.*, e.asset_tag, e.serial_number, u.full_name as assigned_to_name');
        $builder->join('equipment e', 'e.id = wo.equipment_id', 'left');
        $builder->join('technicians t', 't.id = wo.assigned_to', 'left');
        $builder->join('users u', 'u.id = t.user_id', 'left');

        $builder->where('wo.company_id', $companyId);

        if ($groupId !== '') {
            $builder->where('wo.group_id', $groupId);
        } elseif ($siteId > 0) {
            $builder->where('wo.site_id', $siteId);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'data'    => [],
                'message' => 'Missing group_id or site_id'
            ]);
        }

        $builder->orderBy('wo.created_at', 'DESC');

        $rows = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $rows
        ]);
    }
    public function createWorkOrder()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $companyId    = (int) session('company_id');
        $userId       = (int) session('user_id');
        $technicianId = $this->resolveTechnicianId();

        if (!$technicianId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Technician not found'
            ]);
        }

        $siteId      = (int) $this->request->getPost('site_id');
        $equipmentId = (int) $this->request->getPost('equipment_id');
        $groupId     = trim((string) $this->request->getPost('group_id'));
        $title       = trim((string) $this->request->getPost('title'));
        $description = trim((string) $this->request->getPost('description'));
        $status      = trim((string) $this->request->getPost('status'));
        $priority    = trim((string) $this->request->getPost('priority'));
        $startDate   = $this->request->getPost('start_date') ?: null;
        $endDate     = $this->request->getPost('end_date') ?: null;

        if ($siteId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Site is required'
            ]);
        }

        if ($equipmentId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Equipment is required'
            ]);
        }

        if ($groupId === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Inspection group ID is missing. Please save the inspection first.'
            ]);
        }

        if ($title === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Title is required'
            ]);
        }

        $data = [
            'company_id'   => $companyId,
            'site_id'      => $siteId,
            'equipment_id' => $equipmentId,
            'group_id'     => $groupId,
            'title'        => $title,
            'description'  => $description,
            'status'       => strtolower($status),
            'priority'     => strtolower($priority),
            'assigned_to'  => $technicianId,
            'created_by'   => $userId,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();

        if ($db->table('work_orders')->insert($data)) {
            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Work order created successfully',
                'id'       => $db->insertID(),
                'group_id' => $groupId
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Insert failed'
        ]);
    }

    public function record()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        }

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

        if ($siteId === 0 || $result === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields',
            ]);
        }

        $technicianId = $this->resolveTechnicianId();

        $equipmentModel  = new EquipmentModel();
        $inspectionModel = new InspectionModel();

        $equipmentId = 0;

        if ($assetNotFound === '1') {
            $manufacturer = trim((string) $this->request->getPost('manufacturer'));
            $modelName    = trim((string) $this->request->getPost('model_name'));
            $description  = trim((string) $this->request->getPost('description'));
            $newAssetTag = $assetTag !== '' ? $assetTag : ('NEW-' . strtoupper(uniqid()));
            $existing = $equipmentModel
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $newAssetTag)
                ->first();

            if ($existing) {
                $equipmentId = (int) $existing['id'];
            } else {
                $equipmentModel->insert([
                    'company_id'    => $companyId,
                    'site_id'       => $siteId,
                    'asset_tag'     => $newAssetTag,
                    'make'          => $manufacturer,
                    'model'         => $modelName,
                    'serial_number' => $serial,
                    'device_type'   => $description,
                    'department'    => $dept,
                    'location'      => $room,
                    'status'        => 'Pending',
                ]);
                $equipmentId = (int) $equipmentModel->getInsertID();
            }
            $assetTag = $newAssetTag;
        } else {
            $eq = $equipmentModel
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $assetTag)
                ->first();

            if (!$eq) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipment not found',
                ]);
            }
            $equipmentId = (int) $eq['id'];
        }
        $updateFields = [];
        if ($dept   !== '') $updateFields['department']    = $dept;
        if ($room   !== '') $updateFields['location']      = $room;
        if ($serial !== '') $updateFields['serial_number'] = $serial;
        if (!empty($updateFields)) {
            $equipmentModel->update($equipmentId, $updateFields);
        }
        $existingGroupId = trim((string) $this->request->getPost('group_id'));
        $groupId = ($existingGroupId !== '')
            ? $existingGroupId
            : ('INSP-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)));

        $nextDueDate = null;
        if ($pmFreq !== '') {
            preg_match('/^(\d+)/', $pmFreq, $m);
            $months = isset($m[1]) ? (int) $m[1] : 0;
            if ($months > 0) {
                $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }
        }

        $now     = date('Y-m-d H:i:s');
        $insData = [
            'company_id'      => $companyId,
            'site_id'         => $siteId,
            'equipment_id'    => $equipmentId,
            'group_id'        => $groupId,
            'scheduled_at'    => $now,
            'completed_at'    => $now,
            'status'          => $result,
            'technician_id'   => $technicianId,  
            'findings'        => $assetNotFound === '1'
                ? 'Asset not found in inventory. Manufacturer: ' . $this->request->getPost('manufacturer')
                . '; Model: ' . $this->request->getPost('model_name')
                . '; Serial #: ' . $serial
                : '',
            'notes'           => $notes,
            'inspection_type' => $action,
            'est'             => $est,
            'cal'             => $cal,
            'pm_frequency'    => $pmFreq,
            'next_due_date'   => $nextDueDate,
            'device_complete' => 'Yes',
            'created_by'      => $userId > 0 ? $userId : null,
        ];

        $existingInspection = $inspectionModel
            ->where('equipment_id', $equipmentId)
            ->where('group_id', $groupId)
            ->where('site_id', $siteId)
            ->first();

        if ($existingInspection) {
            $inspectionModel->update($existingInspection['id'], $insData);
        } else {
            $inspectionModel->insert($insData);
        }

        $statusMap = ['Pass' => 'Ready', 'Fail' => 'Needs Attention', 'Repair' => 'Repair'];
        if (isset($statusMap[$result])) {
            $equipmentModel->update($equipmentId, ['status' => $statusMap[$result]]);
        }

        return $this->response->setJSON([
            'success'         => true,
            'message'         => 'Inspection recorded successfully',
            'group_id'        => $groupId,
            'asset_tag'       => $assetTag,
            'inspection_type' => $action,
        ]);
    }

    public function deleteRecord($id)
    {
        $companyId       = (int) session('company_id');
        $inspectionModel = new InspectionModel();

        $inspectionModel
            ->where('company_id', $companyId)
            ->where('id', (int) $id)
            ->delete();

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    public function reportData($groupId = null)
    {
        $companyId = (int) session('company_id');

        if (!$companyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session expired']);
        }

        if (empty($groupId)) {
            $groupId = $this->request->getGet('group_id');
        }

        if (empty($groupId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing group_id parameter']);
        }

        $inspectionModel = new InspectionModel();
        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        return $this->response->setJSON([
            'success'  => true,
            'group_id' => $groupId,
            'latest'   => $latest,
            'rows'     => $rows,
        ]);
    }
    public function reportPdf($groupId)
    {
        $companyId       = (int) session('company_id');
        $inspectionModel = new InspectionModel();

        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        $html = $this->renderCleanView('admin/inspections/report_pdf', [
            'latest'  => $latest,
            'rows'    => $rows,
            'groupId' => $groupId,
        ]);

        if (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="inspection-report-' . $groupId . '.pdf"');
            header('Content-Length: ' . strlen($dompdf->output()));
            echo $dompdf->output();
            exit;
        }

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="inspection-report-' . $groupId . '.html"');
        echo $html;
        exit;
    }
    public function reportPreview($groupId)
    {
        $companyId       = (int) session('company_id');
        $inspectionModel = new InspectionModel();

        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        $html = $this->renderCleanView('admin/inspections/report_pdf', [
            'latest'  => $latest,
            'rows'    => $rows,
            'groupId' => $groupId,
        ]);

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    private function renderCleanView(string $viewPath, array $data = []): string
    {

        if (function_exists('config')) {
            $toolbarConfig = config('Toolbar');
            if ($toolbarConfig && property_exists($toolbarConfig, 'collectors')) {
                $toolbarConfig->collectors = [];
            }
        }
        ob_start();
        echo view($viewPath, $data);
        $html = (string) ob_get_clean();
        $html = preg_replace('/<!--\\s*DEBUG-VIEW[\\s\\S]*?-->/i', '', $html);
        $html = preg_replace('/<script[^>]*(debugbar_loader|kint-rich-script)[^>]*>[\\s\\S]*?<\\/script>/i', '', $html);
        $html = preg_replace('/<script[^>]+id="debugbar[^"]*"[^>]*>[\\s\\S]*?<\\/script>/i', '', $html);
        $html = preg_replace('/<style[^>]+id="debugbar[^"]*"[^>]*>[\\s\\S]*?<\\/style>/i', '', $html);
        $html = preg_replace('/<script[^>]+debugbar[^>]*>[\\s\\S]*?<\\/script>/is', '', $html);

        return trim($html);
    }

    private function buildReportHtml(?array $latest, array $rows, string $groupId): string
    {
        $siteName  = htmlspecialchars($latest['site_name']        ?? $latest['customer_name'] ?? '—');
        $techName  = htmlspecialchars($latest['technician_name']  ?? 'N/A');
        $action    = htmlspecialchars($latest['action_performed'] ?? $latest['inspection_type'] ?? '');
        $logoHtml  = '';

        if (!empty($latest['logo_path'])) {
            $logoHtml = '<img src="' . base_url('uploads/logos/' . $latest['logo_path'])
                . '" style="height:55px;" alt="Logo">';
        }

        $statusOrder = ['Fail' => 0, 'Repair' => 1, 'Pass' => 2];
        usort($rows, function ($a, $b) use ($statusOrder) {
            $ra = $a['result'] ?? $a['status'] ?? '';
            $rb = $b['result'] ?? $b['status'] ?? '';
            return ($statusOrder[$ra] ?? 3) <=> ($statusOrder[$rb] ?? 3);
        });

        $groups     = [];
        $groupOrder = [];
        foreach ($rows as $r) {
            $st = $r['result'] ?? $r['status'] ?? 'Unknown';
            if (!isset($groups[$st])) {
                $groups[$st] = [];
                $groupOrder[] = $st;
            }
            $groups[$st][] = $r;
        }

        $groupMeta = [
            'Fail'   => ['label' => 'Failed',  'color' => '#dc3545'],
            'Repair' => ['label' => 'Repair',  'color' => '#ffc107'],
            'Pass'   => ['label' => 'Passed',  'color' => '#198754'],
        ];

        $overviewRows = '';
        foreach ($groupOrder as $st) {
            $meta  = $groupMeta[$st] ?? ['label' => $st, 'color' => '#6c757d'];
            $count = count($groups[$st]);

            $overviewRows .= '<tr style="background:#f8fafc;">'
                . '<td colspan="12" style="padding:8px 12px;">'
                . '<span style="display:inline-block;padding:3px 10px;border-radius:4px;'
                . 'background:' . $meta['color'] . ';color:#fff;font-size:12px;font-weight:700;">'
                . htmlspecialchars($meta['label']) . '</span>'
                . ' <span style="color:#64748b;font-size:12px;">'
                . $count . ' device' . ($count !== 1 ? 's' : '') . '</span>'
                . '</td></tr>';

            foreach ($groups[$st] as $r) {
                $res = $r['result'] ?? $r['status'] ?? '';
                $resultHtml = match ($res) {
                    'Pass'   => '<span style="color:#198754;font-weight:600;">&#10003; Pass</span>',
                    'Fail'   => '<span style="color:#dc3545;font-weight:600;">&#10007; Fail</span>',
                    'Repair' => '<span style="color:#ffc107;font-weight:600;">&#9881; Repair</span>',
                    default  => htmlspecialchars($res),
                };
                $date = !empty($r['inspection_date']) ? substr($r['inspection_date'], 0, 10) : '—';
                $overviewRows .= '<tr>'
                    . '<td>' . $resultHtml . '</td>'
                    . '<td>' . htmlspecialchars($r['customer_name'] ?? $r['site_name'] ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['model']         ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['device_type']   ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['serial_number'] ?? 'N/A') . '</td>'
                    . '<td>' . htmlspecialchars($r['action_performed'] ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['asset_tag']     ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['dept']          ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['room']          ?? '—') . '</td>'
                    . '<td>' . htmlspecialchars($r['technician_name'] ?? 'N/A') . '</td>'
                    . '<td>' . $date . '</td>'
                    . '<td style="max-width:160px;">' . htmlspecialchars($r['notes'] ?? '') . '</td>'
                    . '</tr>';
            }
        }

        if (empty($overviewRows)) {
            $overviewRows = '<tr><td colspan="12" style="text-align:center;color:#94a3b8;padding:20px;">No inspection records found.</td></tr>';
        }

        $latestRow = $latest
            ? '<tr>'
            . '<td>' . htmlspecialchars($latest['model']            ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['device_type']      ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['serial_number']    ?? 'N/A') . '</td>'
            . '<td>' . htmlspecialchars($latest['action_performed'] ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['asset_tag']        ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['dept']             ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['room']             ?? '—') . '</td>'
            . '<td>' . htmlspecialchars($latest['technician_name']  ?? 'N/A') . '</td>'
            . '<td>' . htmlspecialchars($latest['notes']            ?? '') . '</td>'
            . '</tr>'
            : '<tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:16px;">No device data.</td></tr>';

        // ── Shared cell styles ────────────────────────────────────────────────
        $th = 'padding:10px 12px;text-align:left;font-size:11px;font-weight:600;'
            . 'color:#475569;text-transform:uppercase;letter-spacing:0.05em;'
            . 'border-bottom:2px solid #e2e8f0;background:#f8fafc;white-space:nowrap;';
        $td = 'padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:13px;vertical-align:middle;';

        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Inspection Report &mdash; ' . htmlspecialchars($groupId) . '</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#1e293b; background:#f1f5f9; padding:24px; }
  .page  { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); padding:32px; max-width:1280px; margin:0 auto; }
  .report-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:20px; margin-bottom:24px; border-bottom:2px solid #e2e8f0; }
  .report-header .left h2  { font-size:20px; font-weight:700; color:#0f172a; margin-bottom:4px; }
  .report-header .left p   { color:#64748b; font-size:13px; }
  .report-header .right    { text-align:right; }
  .report-header .right p  { font-size:12px; color:#64748b; margin-bottom:3px; }
  .report-header .right strong { color:#1e293b; }
  h3  { font-size:15px; font-weight:700; color:#0f172a; margin:28px 0 12px; }
  table { width:100%; border-collapse:collapse; }
  th  { ' . $th . ' }
  td  { ' . $td . ' }
  tr:last-child td { border-bottom:none; }
  .print-btn { position:fixed; bottom:28px; right:28px; background:#6366f1; color:#fff; border:none;
               padding:13px 26px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;
               box-shadow:0 4px 14px rgba(99,102,241,.4); letter-spacing:.02em; }
  .print-btn:hover { background:#4f46e5; }
  @media print {
    .print-btn { display:none; }
    body { background:#fff; padding:0; }
    .page { box-shadow:none; border-radius:0; }
  }
</style>
</head>
<body>
<div class="page">

  <!-- Header: site, logo, inspection # -->
  <div class="report-header">
    <div class="left">
      <h2>' . $siteName . '</h2>
      <p>' . $action . '</p>
    </div>
    <div>' . $logoHtml . '</div>
    <div class="right">
      <p><strong>Inspection #:</strong> ' . htmlspecialchars($groupId) . '</p>
      <p><strong>Technician:</strong> '   . $techName . '</p>
      <p><strong>Generated:</strong> '    . date('M d, Y g:i A') . '</p>
    </div>
  </div>

  <!-- Latest Added Device -->
  <h3>Latest Added Device</h3>
  <div style="overflow-x:auto;">
  <table>
    <thead><tr>
      <th>Model</th><th>Type</th><th>S/N</th><th>Action Performed</th>
      <th>Asset #</th><th>Dept</th><th>Room</th><th>Tech</th><th>Notes</th>
    </tr></thead>
    <tbody>' . $latestRow . '</tbody>
  </table>
  </div>

  <!-- Inspection Report Overview -->
  <h3>Inspection Report Overview</h3>
  <div style="overflow-x:auto;">
  <table>
    <thead><tr>
      <th>Result</th><th>Customer</th><th>Model</th><th>Type</th><th>S/N</th>
      <th>Action Performed</th><th>Asset #</th><th>Dept</th><th>Room</th>
      <th>Tech</th><th>Inspection Date</th><th>Notes</th>
    </tr></thead>
    <tbody>' . $overviewRows . '</tbody>
  </table>
  </div>

</div>
<button class="print-btn" onclick="window.print()">&#128438; Print / Save as PDF</button>
</body>
</html>';
    }
}