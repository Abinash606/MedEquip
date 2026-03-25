<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\InspectionModel;
use App\Models\TechnicianModel;

class SiteInspectionWorkflowController extends BaseController
{
    // ─────────────────────────────────────────────────────────────────
    // THE ROOT CAUSE OF THE 500:
    //
    //   inspections.technician_id has a FK constraint:
    //   FOREIGN KEY (technician_id) REFERENCES technicians (id)
    //
    //   The admin controller stores users.id directly — this works for
    //   the ADMIN because the admin's DB has no FK on technician_id.
    //
    //   The TECHNICIAN database (tscameri_medquip) DOES have a FK:
    //   fk_inspections_technician → technicians.id
    //
    //   Inserting session('user_id') [= users.id] into a column that
    //   expects technicians.id violates this constraint → 500.
    //
    //   FIX: Always use resolveTechnicianId() which looks up the correct
    //   technicians.id for the logged-in user.
    // ─────────────────────────────────────────────────────────────────
    private function resolveTechnicianId(): ?int
    {
        $userId    = (int) session('user_id');
        $companyId = (int) session('company_id');

        if ($userId === 0) return null;

        $techModel = new TechnicianModel();

        // Prefer company-scoped match
        $row = $techModel
            ->where('user_id',    $userId)
            ->where('company_id', $companyId)
            ->first();

        if ($row) return (int) $row['id'];

        // Fallback: any technician row for this user
        $row = $techModel->where('user_id', $userId)->first();

        return $row ? (int) $row['id'] : null;
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPER — strips keys not in the real DB schema.
    // Prevents "Unknown column" crashes for optional columns.
    // ─────────────────────────────────────────────────────────────────
    private array $cachedCols = [];

    private function filterToColumns(array $data, string $table): array
    {
        if (empty($this->cachedCols[$table])) {
            $this->cachedCols[$table] = \Config\Database::connect()->getFieldNames($table) ?: [];
        }
        if (empty($this->cachedCols[$table])) return $data;

        $safe = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $this->cachedCols[$table], true)) {
                $safe[$k] = $v;
            }
        }
        return $safe;
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/site-inspection/get-equipment
    // ─────────────────────────────────────────────────────────────────
    public function getEquipment()
    {
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');
        $companyId = (int) session('company_id');

        if ($assetTag === '' || $siteId === 0) {
            return $this->response->setJSON(['found' => false]);
        }

        $em = new EquipmentModel();
        $eq = $em->where('company_id', $companyId)
            ->where('site_id',    $siteId)
            ->where('asset_tag',  $assetTag)
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
            'est'           => $eq['est']            ?? '0',
            'cal'           => $eq['cal']            ?? '0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POST technician/site-inspection/record
    // ─────────────────────────────────────────────────────────────────
    public function recordInspection()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $companyId    = (int) session('company_id');
        $userId       = (int) session('user_id');

        // ── CRITICAL: use technicians.id not users.id ────────────
        $technicianId = $this->resolveTechnicianId();

        $siteId   = (int)    $this->request->getPost('site_id');
        $assetTag = trim((string) $this->request->getPost('asset_tag'));
        $result   = trim((string) $this->request->getPost('result'));
        $notes    = trim((string) $this->request->getPost('notes'));
        $dept     = trim((string) $this->request->getPost('department'));
        $room     = trim((string) $this->request->getPost('room'));
        $serial   = trim((string) $this->request->getPost('serial_number'));
        $action   = trim((string) $this->request->getPost('action_performed'));
        $pmFreq   = trim((string) $this->request->getPost('pm_frequency'));
        $est      = trim((string) $this->request->getPost('est'));
        $cal      = trim((string) $this->request->getPost('cal'));

        if ($assetTag === '' || $siteId === 0 || $result === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields',
            ]);
        }

        $db = \Config\Database::connect();
        $em = new EquipmentModel();

        // ── Find equipment ───────────────────────────────────────
        $equipment = $em->where('company_id', $companyId)
            ->where('site_id',    $siteId)
            ->where('asset_tag',  $assetTag)
            ->first();

        if (!$equipment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Equipment "' . $assetTag . '" not found in site #' . $siteId,
            ]);
        }

        $equipmentId = (int) $equipment['id'];

        // ── Update equipment metadata if provided ────────────────
        $eqUpdate = [];
        if ($dept   !== '') $eqUpdate['department']    = $dept;
        if ($room   !== '') $eqUpdate['location']      = $room;
        if ($serial !== '') $eqUpdate['serial_number'] = $serial;
        if (!empty($eqUpdate)) {
            $em->update($equipmentId, $eqUpdate);
        }

        // ── Resolve group_id ─────────────────────────────────────
        $existingGroupId = trim((string) $this->request->getPost('group_id'));
        $groupId = $existingGroupId !== ''
            ? $existingGroupId
            : ('INSP-' . date('YmdHis'));

        $now = date('Y-m-d H:i:s');

        // ── Next due date ────────────────────────────────────────
        $nextDueDate = null;
        if ($pmFreq !== '') {
            preg_match('/^(\d+)/', $pmFreq, $m);
            $months = isset($m[1]) ? (int) $m[1] : 0;
            if ($months > 0) {
                $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }
        }

        // ── Build candidate data ─────────────────────────────────
        $candidate = [
            'company_id'      => $companyId,
            'site_id'         => $siteId,
            'equipment_id'    => $equipmentId,
            'group_id'        => $groupId,
            'scheduled_at'    => $now,
            'completed_at'    => $now,
            'status'          => $result,
            'result'          => $result,
            // ↓ technicians.id — satisfies FK fk_inspections_technician
            'technician_id'   => $technicianId,
            'findings'        => '',
            'notes'           => $notes,
            'inspection_type' => $action,
            'action_performed' => $action,
            'est'             => $est,
            'cal'             => $cal,
            'pm_frequency'    => $pmFreq,
            'next_due_date'   => $nextDueDate,
            'device_complete' => 'Yes',
            'created_by'      => $userId > 0 ? $userId : null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        // Strip keys not in actual DB columns
        $insData = $this->filterToColumns($candidate, 'inspections');

        // ── Upsert ───────────────────────────────────────────────
        $existing = $db->table('inspections')
            ->where('equipment_id', $equipmentId)
            ->where('group_id',     $groupId)
            ->where('site_id',      $siteId)
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if ($existing) {
            $upd = $insData;
            unset($upd['created_at']);
            $upd['updated_at'] = $now;
            $db->table('inspections')->where('id', $existing['id'])->update($upd);
        } else {
            $db->table('inspections')->insert($insData);
        }

        // ── Sync equipment status ────────────────────────────────
        $statusMap = [
            'Pass'   => 'ready',
            'Fail'   => 'need_attention',
            'Repair' => 'need_attention',
        ];
        if (isset($statusMap[$result])) {
            $em->update($equipmentId, ['status' => $statusMap[$result]]);
        }

        return $this->response->setJSON([
            'success'         => true,
            'message'         => 'Inspection recorded successfully',
            'group_id'        => $groupId,
            'inspection_type' => $action,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/inspections/searchBySerial
    // ─────────────────────────────────────────────────────────────────
    public function searchBySerial()
    {
        $serial    = trim((string) $this->request->getGet('serial_number'));
        $siteId    = (int) $this->request->getGet('site_id');
        $companyId = (int) session('company_id');

        if ($serial === '') return $this->response->setJSON(['found' => false]);

        $em      = new EquipmentModel();
        $builder = $em->where('company_id', $companyId)->where('serial_number', $serial);
        if ($siteId > 0) $builder->where('site_id', $siteId);
        $eq = $builder->first();

        if (!$eq) return $this->response->setJSON(['found' => false]);

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
            'est'           => $eq['est']            ?? '0',
            'cal'           => $eq['cal']            ?? '0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/inspections/searchByModel
    // ─────────────────────────────────────────────────────────────────
    public function searchByModel()
    {
        $keyword   = trim((string) $this->request->getGet('keyword'));
        $companyId = (int) session('company_id');

        if (strlen($keyword) < 2) return $this->response->setJSON([]);

        $em      = new EquipmentModel();
        $results = $em->where('company_id', $companyId)
            ->groupStart()
            ->like('model',       $keyword)
            ->orLike('make',        $keyword)
            ->orLike('device_type', $keyword)
            ->groupEnd()
            ->select('id, make, model, device_type, serial_number, asset_tag, department, location, est, cal')
            ->limit(20)
            ->findAll();

        return $this->response->setJSON($results);
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/inspections/getByGroupId
    // ─────────────────────────────────────────────────────────────────
    public function getByGroupId()
    {
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $companyId = (int) session('company_id');

        if ($groupId === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'group_id required']);
        }

        $db   = \Config\Database::connect();
        $rows = $db->table('inspections i')
            ->select([
                'i.id            AS inspections_id',
                'i.group_id',
                'i.status        AS inspection_status',
                'i.notes',
                'i.inspection_type',
                'i.scheduled_at',
                'i.updated_at',
                'e.asset_tag',
                'e.model',
                'e.make',
                'e.device_type',
                'e.serial_number',
                'e.department',
                'e.location      AS room',
                'e.est',
                'e.cal',
                's.name          AS customer_site',
                // join via technicians table to get user's full_name
                'u.full_name     AS technician_name',
            ])
            ->join('equipment e',   'e.id = i.equipment_id',   'left')
            ->join('sites s',       's.id = i.site_id',        'left')
            ->join('technicians t', 't.id = i.technician_id',  'left')
            ->join('users u',       'u.id = t.user_id',        'left')
            ->where('i.group_id',   $groupId)
            ->where('i.company_id', $companyId)
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/inspections/getInspectionById/(:num)
    // ─────────────────────────────────────────────────────────────────
    public function getInspectionById($id)
    {
        $companyId = (int) session('company_id');
        $id        = (int) $id;

        $db  = \Config\Database::connect();
        $row = $db->table('inspections i')
            ->select('i.*, e.asset_tag, e.model, e.make, e.device_type,
                      e.serial_number, e.department, e.location, e.est, e.cal')
            ->join('equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.id', $id)->where('i.company_id', $companyId)
            ->get()->getRowArray();

        if (!$row) return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        return $this->response->setJSON(['success' => true, 'data' => $row]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POST technician/inspections/updateInspection
    // ─────────────────────────────────────────────────────────────────
    public function updateInspection()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        }

        $companyId    = (int) session('company_id');
        $inspectionId = (int) $this->request->getPost('inspection_id');
        $db           = \Config\Database::connect();

        $existing = $db->table('inspections')
            ->where('id', $inspectionId)->where('company_id', $companyId)
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Inspection not found']);
        }

        $em          = new EquipmentModel();
        $equipmentId = (int) $this->request->getPost('equipment_id');

        if ($equipmentId > 0) {
            $eqUpdate = [];
            foreach (
                [
                    'make' => 'manufacturer',
                    'model' => 'model_name',
                    'device_type' => 'description',
                    'serial_number' => 'serial_number',
                    'asset_tag' => 'asset_tag',
                    'department' => 'department',
                    'location' => 'location',
                ] as $col => $postKey
            ) {
                $val = trim((string) $this->request->getPost($postKey));
                if ($val !== '') $eqUpdate[$col] = $val;
            }
            if (!empty($eqUpdate)) $em->update($equipmentId, $eqUpdate);
        }

        // Resolve technician_id from post OR current user
        $techIdPost   = (int) $this->request->getPost('technician_id');
        $technicianId = $techIdPost > 0 ? $techIdPost : $this->resolveTechnicianId();

        $candidate = [
            'pm_frequency'    => $this->request->getPost('pm_frequency'),
            'inspection_type' => $this->request->getPost('inspection_type'),
            'action_performed' => $this->request->getPost('inspection_type'),
            'technician_id'   => $technicianId,
            'scheduled_at'    => $this->request->getPost('scheduled_at'),
            'notes'           => $this->request->getPost('notes'),
            'status'          => $this->request->getPost('status'),
            'result'          => $this->request->getPost('status'),
            'device_complete' => $this->request->getPost('device_complete'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $updateData = $this->filterToColumns($candidate, 'inspections');
        $db->table('inspections')->where('id', $inspectionId)->update($updateData);

        $result    = $this->request->getPost('status');
        $statusMap = ['Pass' => 'ready', 'Fail' => 'need_attention', 'Repair' => 'need_attention'];
        if ($equipmentId > 0 && isset($statusMap[$result])) {
            $em->update($equipmentId, ['status' => $statusMap[$result]]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Updated successfully',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POST technician/inspections/deleteById/(:any)
    // ─────────────────────────────────────────────────────────────────
    public function deleteById($id)
    {
        $companyId = (int) session('company_id');
        $id        = (int) $id;
        $db        = \Config\Database::connect();

        $rec = $db->table('inspections')
            ->where('id', $id)->where('company_id', $companyId)
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if (!$rec) return $this->response->setJSON(['success' => false, 'message' => 'Not found']);

        $db->table('inspections')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POST technician/inspections/deleteGroup
    // ─────────────────────────────────────────────────────────────────
    public function deleteGroup()
    {
        $companyId = (int) session('company_id');
        $groupId   = trim((string) $this->request->getPost('group_id'));

        if ($groupId === '') return $this->response->setJSON(['success' => false, 'message' => 'group_id required']);

        \Config\Database::connect()->table('inspections')
            ->where('group_id', $groupId)->where('company_id', $companyId)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/inspections/reportData
    // ─────────────────────────────────────────────────────────────────
    public function reportData($groupId = null)
    {
        $companyId = (int) session('company_id');
        if (!$groupId) $groupId = trim((string) $this->request->getGet('group_id'));
        if ($groupId === '') return $this->response->setJSON(['success' => false, 'message' => 'group_id required']);

        $im   = new InspectionModel();
        $rows = $im->getReportRowsByGroup($companyId, $groupId);

        return $this->response->setJSON([
            'success'  => true,
            'group_id' => $groupId,
            'latest'   => !empty($rows) ? $rows[0] : null,
            'rows'     => $rows,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POST technician/inspections/create  (batch wizard queue)
    // ─────────────────────────────────────────────────────────────────
    public function create()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        }

        $companyId    = (int) session('company_id');
        $userId       = (int) session('user_id');
        $technicianId = $this->resolveTechnicianId(); // ← FK-safe
        $groupId      = trim((string) $this->request->getPost('group_id'));
        $itemsJson    = $this->request->getPost('inspection_items');

        if ($groupId === '' || $itemsJson === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing data']);
        }

        $items = json_decode($itemsJson, true);
        if (!is_array($items) || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No inspection items']);
        }

        $db  = \Config\Database::connect();
        $em  = new EquipmentModel();
        $now = date('Y-m-d H:i:s');

        foreach ($items as $item) {
            $siteId      = (int) ($item['site_id']      ?? 0);
            $equipmentId = (int) ($item['equipment_id'] ?? 0);
            $status      = trim((string) ($item['status'] ?? ''));
            $pmFreq      = trim((string) ($item['pm_frequency'] ?? ''));
            $action      = trim((string) ($item['inspection_type'] ?? ''));

            if ($equipmentId === 0 && !empty($item['asset_tag'])) {
                $eq = $em->where('company_id', $companyId)
                    ->where('site_id',    $siteId)
                    ->where('asset_tag',  $item['asset_tag'])
                    ->first();
                if ($eq) $equipmentId = (int) $eq['id'];
            }

            $nextDueDate = null;
            if ($pmFreq !== '') {
                preg_match('/^(\d+)/', $pmFreq, $m2);
                $months = isset($m2[1]) ? (int) $m2[1] : 0;
                if ($months > 0) $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }

            // Technician from item (must be technicians.id) or current user's technician row
            $itemTechId = !empty($item['technician_id']) ? (int) $item['technician_id'] : $technicianId;

            $candidate = [
                'company_id'      => $companyId,
                'site_id'         => $siteId,
                'equipment_id'    => $equipmentId > 0 ? $equipmentId : null,
                'group_id'        => $groupId,
                'scheduled_at'    => !empty($item['scheduled_at']) ? $item['scheduled_at'] : $now,
                'completed_at'    => $now,
                'status'          => $status,
                'result'          => $status,
                'technician_id'   => $itemTechId,
                'findings'        => '',
                'notes'           => $item['notes']  ?? '',
                'inspection_type' => $action,
                'action_performed' => $action,
                'est'             => $item['est'] ?? '',
                'cal'             => $item['cal'] ?? '',
                'pm_frequency'    => $pmFreq,
                'next_due_date'   => $nextDueDate,
                'device_complete' => $item['device_complete'] ?? 'Yes',
                'created_by'      => $userId > 0 ? $userId : null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            $insData = $this->filterToColumns($candidate, 'inspections');

            if ($equipmentId > 0) {
                $existing = $db->table('inspections')
                    ->where('equipment_id', $equipmentId)
                    ->where('group_id',     $groupId)
                    ->where('site_id',      $siteId)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()->getRowArray();

                if ($existing) {
                    $upd = $insData;
                    unset($upd['created_at']);
                    $upd['updated_at'] = $now;
                    $db->table('inspections')->where('id', $existing['id'])->update($upd);
                } else {
                    $db->table('inspections')->insert($insData);
                }

                $statusMap = ['Pass' => 'ready', 'Fail' => 'need_attention', 'Repair' => 'need_attention'];
                if (isset($statusMap[$status])) {
                    $em->update($equipmentId, ['status' => $statusMap[$status]]);
                }
            } else {
                $db->table('inspections')->insert($insData);
            }
        }

        return $this->response->setJSON(['success' => true, 'group_id' => $groupId]);
    }
    // ─────────────────────────────────────────────────────────────────
    // POST technician/site-inspection/add-device
    // ─────────────────────────────────────────────────────────────────
    public function addDevice()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $companyId = (int) session('company_id');
        $siteId    = (int) $this->request->getPost('site_id');

        if ($siteId === 0 || $companyId === 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Site ID or Company ID missing.']);
        }

        $assetTag   = trim((string) $this->request->getPost('asset_tag'));
        $model      = trim((string) $this->request->getPost('model'));
        $serial     = trim((string) $this->request->getPost('serial_number'));
        $make       = trim((string) $this->request->getPost('make'));
        $deviceType = trim((string) $this->request->getPost('device_type'));
        $description = trim((string) $this->request->getPost('description'));
        $dept       = trim((string) $this->request->getPost('department'));
        $location   = trim((string) $this->request->getPost('location'));
        $technician = trim((string) $this->request->getPost('technician'));
        $est        = trim((string) $this->request->getPost('est')) ?: 'No';
        $cal        = trim((string) $this->request->getPost('cal')) ?: 'No';
        $groupId    = trim((string) $this->request->getPost('group_id'));

        if ($assetTag === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Asset # is required.']);
        }

        $em = new EquipmentModel();

        // Check for duplicate asset tag in this site
        $existing = $em->where('company_id', $companyId)
            ->where('site_id',   $siteId)
            ->where('asset_tag', $assetTag)
            ->first();

        if ($existing) {
            return $this->response->setJSON([
                'success'          => false,
                'message'          => 'Asset # "' . $assetTag . '" already exists in this site\'s inventory.',
                'start_inspection' => false,
            ]);
        }

        $candidate = [
            'company_id'    => $companyId,
            'site_id'       => $siteId,
            'asset_tag'     => $assetTag,
            'model'         => $model,
            'make'          => $make,
            'serial_number' => $serial,
            'device_type'   => $deviceType,
            'description'   => $description,
            'department'    => $dept,
            'location'      => $location,
            'technician'    => $technician,
            'est'           => $est,
            'cal'           => $cal,
            'status'        => 'Not Inspected',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Strip keys not in actual DB columns
        $insertData = $this->filterToColumns($candidate, 'equipment');

        $em->insert($insertData);
        $newId = $em->getInsertID();

        if (!$newId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to save device to database.']);
        }

        return $this->response->setJSON([
            'success'          => true,
            'message'          => 'Device added successfully.',
            'equipment_id'     => $newId,
            'asset_tag'        => $assetTag,
            'start_inspection' => true,
            'csrf_hash'        => csrf_hash(),
        ]);
    }
    /**
     * Return the department and room from the most recently saved equipment
     * record for a given site. Server-side persistent autofill for Add Device modal.
     */
    public function getLastDeviceForSite()
    {
        $siteId    = (int) $this->request->getGet('site_id');
        $companyId = (int) session('company_id');

        if ($siteId === 0) {
            return $this->response->setJSON(['department' => '', 'location' => '']);
        }

        $equipmentModel = new \App\Models\EquipmentModel();
        $eq = $equipmentModel
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->orderBy('id', 'DESC')
            ->first();

        return $this->response->setJSON([
            'department' => $eq['department'] ?? '',
            'location'   => $eq['location']   ?? '',
        ]);
    }

    /**
     * Return IQ Notes for this company so the technician workflow
     * can populate the Action Performed dropdown with Title/Note pairs.
     */
    public function getIqNotes()
    {
        $companyId = (int) session('company_id');
        $db  = \Config\Database::connect();
        $rows = $db->table('iq_notes')
            ->where('company_id', $companyId)
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }

}