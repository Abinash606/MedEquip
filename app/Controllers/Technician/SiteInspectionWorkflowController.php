<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteEquipmentModel;
use App\Models\InspectionModel;
use App\Models\TechnicianModel;
use App\Libraries\OperationalWorkOrderService;

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

    private function findSiteEquipment(int $companyId, int $siteId, string $assetTag): ?array
    {
        if ($siteId <= 0 || $assetTag === '') {
            return null;
        }

        $row = (new SiteEquipmentModel())->findByAssetTag($companyId, $siteId, $assetTag);
        return $row ?: null;
    }

    private function findMasterReference(int $companyId, string $assetTag = '', string $model = ''): ?array
    {
        if ($assetTag === '' && $model === '') {
            return null;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('equipment')
            ->select('id, asset_tag, make, model, device_type, serial_number, department, location, est, cal')
            ->where('company_id', $companyId)
            ->where('deleted_at', null);

        if ($assetTag !== '' && $model !== '') {
            $builder->groupStart()
                ->where('asset_tag', $assetTag)
                ->orWhere('model', $model)
                ->groupEnd();
        } elseif ($assetTag !== '') {
            $builder->where('asset_tag', $assetTag);
        } else {
            $builder->where('model', $model);
        }

        $row = $builder->get()->getRowArray();
        return $row ?: null;
    }

    private function findInspectionSnapshotByAsset(int $companyId, int $siteId, string $assetTag, string $groupId = ''): ?array
    {
        if ($siteId <= 0 || $assetTag === '') {
            return null;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('inspections i')
            ->select([
                'i.id AS inspection_id',
                'i.group_id',
                'i.equipment_id',
                'COALESCE(i.asset_tag, e.asset_tag) AS asset_tag',
                'COALESCE(i.make, e.make) AS make',
                'COALESCE(i.model, e.model) AS model',
                'COALESCE(i.device_type, e.device_type) AS device_type',
                'COALESCE(i.serial_number, e.serial_number) AS serial_number',
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS location',
                'COALESCE(i.est, e.est) AS est',
                'COALESCE(i.cal, e.cal) AS cal',
                'COALESCE(i.action_performed, i.inspection_type) AS action_performed',
                'i.notes',
            ])
            ->join('equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.company_id', $companyId)
            ->where('i.site_id', $siteId)
            ->where('i.deleted_at', null)
            ->where('COALESCE(i.asset_tag, e.asset_tag)', $assetTag, false);

        if ($groupId !== '') {
            $builder->where('i.group_id', $groupId);
        }

        $row = $builder->orderBy('i.id', 'DESC')->get()->getRowArray();
        return $row ?: null;
    }

    private function latestInspectionLocation(int $companyId, int $siteId, string $groupId = ''): ?array
    {
        if ($siteId <= 0) {
            return null;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('inspections i')
            ->select([
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS location',
            ])
            ->join('site_equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.company_id', $companyId)
            ->where('i.site_id', $siteId)
            ->where('i.deleted_at', null)
            ->groupStart()
                ->where('i.department <>', '')
                ->orWhere('i.location <>', '')
                ->orWhere('e.department <>', '')
                ->orWhere('e.location <>', '')
            ->groupEnd();

        if ($groupId !== '') {
            $builder->where('i.group_id', $groupId);
        }

        $row = $builder->orderBy('i.id', 'DESC')->get()->getRowArray();
        return $row ?: null;
    }

    // ─────────────────────────────────────────────────────────────────
    // GET  technician/site-inspection/get-equipment
    // ─────────────────────────────────────────────────────────────────
    // public function getEquipment()
    // {
    //     $assetTag  = trim((string) $this->request->getGet('asset_tag'));
    //     $siteId    = (int) $this->request->getGet('site_id');
    //     $companyId = (int) session('company_id');

    //     if ($assetTag === '' || $siteId === 0) {
    //         return $this->response->setJSON(['found' => false]);
    //     }

    //     $em = new EquipmentModel();
    //     $eq = $em->where('company_id', $companyId)
    //         ->where('site_id',    $siteId)
    //         ->where('asset_tag',  $assetTag)
    //         ->first();

    //     if (!$eq) {
    //         return $this->response->setJSON(['found' => false]);
    //     }

    //     return $this->response->setJSON([
    //         'found'         => true,
    //         'id'            => (int) $eq['id'],
    //         'asset_tag'     => $eq['asset_tag'],
    //         'make'          => $eq['make']          ?? '',
    //         'model'         => $eq['model']         ?? '',
    //         'device_type'   => $eq['device_type']   ?? '',
    //         'serial_number' => $eq['serial_number'] ?? '',
    //         'department'    => $eq['department']    ?? '',
    //         'location'      => $eq['location']      ?? '',
    //         'est'           => $eq['est']            ?? '0',
    //         'cal'           => $eq['cal']            ?? '0',
    //     ]);
    // }

    public function getEquipment()
    {
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');
        $companyId = (int) session('company_id');

        if ($assetTag === '' || $siteId === 0) {
            return $this->response->setJSON(['found' => false]);
        }

        // 1. Look up in site_equipment for this site
        $seModel = new SiteEquipmentModel();
        $eq = $seModel->findByAssetTag($companyId, $siteId, $assetTag);

        // FIX 4 (tech): If not found, do a direct DB lookup to catch assets
        // whose asset_tag was just renamed via recordInspection. Without this
        // the next scan opens 'Add New Device' instead of the pass/fail screen.
        if (!$eq) {
            $dbConn = \Config\Database::connect();
            $directRow = $dbConn->table('site_equipment')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->get()->getRowArray();
            if ($directRow) $eq = $directRow;
        }

        if ($eq) {
            return $this->response->setJSON([
                'found'         => true,
                'id'            => (int) $eq['id'],
                'asset_tag'     => $eq['asset_tag'],
                'make'          => $eq['make'] ?? '',
                'model'         => $eq['model'] ?? '',
                'device_type'   => $eq['device_type'] ?? '',
                'serial_number' => $eq['serial_number'] ?? '',
                'department'    => $eq['department'] ?? '',
                'location'      => $eq['location'] ?? '',
                'est'           => $eq['est'] ?? '0',
                'cal'           => $eq['cal'] ?? '0',
            ]);
        }

        // 2. Not in site_equipment — check master catalogue for make/model auto-fill
        $master = (new EquipmentModel())
            ->where('company_id', $companyId)
            ->where('asset_tag', $assetTag)
            ->where('deleted_at', null)
            ->first();

        if ($master) {
            // Auto-create a site_equipment entry from master catalogue
            $newId = $seModel->createFromMaster($master, $siteId);
            return $this->response->setJSON([
                'found'         => true,
                'id'            => $newId,
                'asset_tag'     => $master['asset_tag'],
                'make'          => $master['make'] ?? '',
                'model'         => $master['model'] ?? '',
                'device_type'   => $master['device_type'] ?? '',
                'serial_number' => $master['serial_number'] ?? '',
                'department'    => $master['department'] ?? '',
                'location'      => $master['location'] ?? '',
                'est'           => $master['est'] ?? '0',
                'cal'           => $master['cal'] ?? '0',
                'from_master'   => true,
            ]);
        }

        // 3. Not found anywhere — frontend will open Add Device modal
        return $this->response->setJSON(['found' => false]);
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
        $technicianId = $this->resolveTechnicianId();
        $siteId       = (int) $this->request->getPost('site_id');
        $assetTag     = trim((string) $this->request->getPost('asset_tag'));
        $result       = trim((string) $this->request->getPost('result'));
        $notes        = trim((string) $this->request->getPost('notes'));
        $dept         = trim((string) $this->request->getPost('department'));
        $room         = trim((string) $this->request->getPost('room'));
        $serial       = trim((string) $this->request->getPost('serial_number'));
        $action       = trim((string) $this->request->getPost('action_performed'));
        $pmFreq       = trim((string) $this->request->getPost('pm_frequency'));
        $est          = trim((string) $this->request->getPost('est'));
        $cal          = trim((string) $this->request->getPost('cal'));
        $make         = trim((string) $this->request->getPost('make'));
        $model        = trim((string) $this->request->getPost('model'));
        $deviceType   = trim((string) $this->request->getPost('device_type'));
        $groupId      = trim((string) $this->request->getPost('group_id'));
        $originalTag  = trim((string) $this->request->getPost('original_asset_tag'));
        $lookupTag    = $originalTag !== '' ? $originalTag : $assetTag;

        if ($assetTag === '' || $siteId === 0 || $result === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $db        = \Config\Database::connect();
        $seModel   = new \App\Models\SiteEquipmentModel();
        $equipment = $this->findSiteEquipment($companyId, $siteId, $lookupTag);

        if (!$equipment && $assetTag !== $lookupTag) {
            $equipment = $this->findSiteEquipment($companyId, $siteId, $assetTag);
        }

        $masterRef = $this->findMasterReference($companyId, $lookupTag, $model);
        $now       = date('Y-m-d H:i:s');

        if ($groupId === '') {
            $groupId = 'INSP-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        }

        $nextDueDate = null;
        if ($pmFreq !== '') {
            preg_match('/^(\d+)/', $pmFreq, $m);
            $months = isset($m[1]) ? (int) $m[1] : 0;
            if ($months > 0) {
                $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }
        }

        // IMPORTANT:
        // site_equipment.id is NOT valid for inspections.equipment_id
        // inspections.equipment_id must reference equipment.id
        $siteEquipmentId   = !empty($equipment['id']) ? (int) $equipment['id'] : null;
        $masterEquipmentId = !empty($equipment['master_equipment_id'])
            ? (int) $equipment['master_equipment_id']
            : (!empty($masterRef['id']) ? (int) $masterRef['id'] : null);

        $candidate = [
            'company_id'       => $companyId,
            'site_id'          => $siteId,
            'equipment_id'     => $masterEquipmentId, // FK -> equipment.id
            'group_id'         => $groupId,
            'asset_tag'        => $assetTag,
            'make'             => $make !== '' ? $make : (($equipment['make'] ?? '') ?: ($masterRef['make'] ?? '')),
            'model'            => $model !== '' ? $model : (($equipment['model'] ?? '') ?: ($masterRef['model'] ?? '')),
            'device_type'      => $deviceType !== '' ? $deviceType : (($equipment['device_type'] ?? '') ?: ($masterRef['device_type'] ?? '')),
            'serial_number'    => $serial !== '' ? $serial : (($equipment['serial_number'] ?? '') ?: ($masterRef['serial_number'] ?? '')),
            'department'       => $dept !== '' ? $dept : (($equipment['department'] ?? '') ?: ($masterRef['department'] ?? '')),
            'location'         => $room !== '' ? $room : (($equipment['location'] ?? '') ?: ($masterRef['location'] ?? '')),
            'scheduled_at'     => $now,
            'completed_at'     => $now,
            'status'           => $result,
            'result'           => $result,
            'technician_id'    => $technicianId,
            'findings'         => '',
            'notes'            => $notes,
            'inspection_type'  => $action,
            'action_performed' => $action,
            'est'              => $est !== '' ? $est : (($equipment['est'] ?? '') ?: ($masterRef['est'] ?? 'No')),
            'cal'              => $cal !== '' ? $cal : (($equipment['cal'] ?? '') ?: ($masterRef['cal'] ?? 'No')),
            'pm_frequency'     => $pmFreq,
            'next_due_date'    => $nextDueDate,
            'device_complete'  => 'Yes',
            'created_by'       => $userId > 0 ? $userId : null,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];

        $insData = $this->filterToColumns($candidate, 'inspections');

        // FIX 3 (tech): When user changed the asset tag on the pass/fail screen,
        // rename the site_equipment record in-place so the old tag leaves
        // 'Not Inspected' and the new tag is found on the next scan (FIX 4).
        if ($assetTag !== $lookupTag && $equipment) {
            $seModel->update((int) $equipment['id'], ['asset_tag' => $assetTag]);
            $equipment       = $this->findSiteEquipment($companyId, $siteId, $assetTag);
            $siteEquipmentId = $equipment ? (int) $equipment['id'] : $siteEquipmentId;
        }

        // Duplicate check: match strictly by asset_tag within this group.
        $builder = $db->table('inspections')
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('group_id', $groupId)
            ->where('deleted_at', null)
            ->groupStart();

        $builder->where('asset_tag', $lookupTag);
        if ($assetTag !== $lookupTag) {
            $builder->orWhere('asset_tag', $assetTag);
        }
        $builder->groupEnd();
        $existing = $builder->orderBy('id', 'DESC')->get()->getRowArray();

        if ($existing) {
            $update = $insData;
            unset($update['created_at'], $update['created_by']);
            $update['updated_at'] = $now;
            // FIX 3: also update asset_tag in the inspection row to the new value
            $update['asset_tag'] = $assetTag;
            $db->table('inspections')->where('id', (int) $existing['id'])->update($update);
        } else {
            $db->table('inspections')->insert($insData);
        }

        // update site working copy status using site_equipment.id
        $statusMap = ['Pass' => 'ready', 'Fail' => 'need_attention', 'Repair' => 'need_attention'];
        if ($siteEquipmentId && isset($statusMap[$result])) {
            $seModel->update($siteEquipmentId, ['status' => $statusMap[$result]]);
        }

        // work_orders.equipment_id also points to equipment.id
        if ($masterEquipmentId) {
            (new \App\Libraries\OperationalWorkOrderService())->syncFollowUpFromInspection([
                'company_id'      => $companyId,
                'site_id'         => $siteId,
                'equipment_id'    => $masterEquipmentId,
                'group_id'        => $groupId,
                'status'          => $result,
                'inspection_type' => $action,
                'notes'           => $notes,
                'asset_tag'       => $assetTag,
                'technician_id'   => $technicianId,
                'created_by'      => $userId > 0 ? $userId : null,
            ]);
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
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $companyId = (int) session('company_id');

        if ($serial === '') {
            return $this->response->setJSON(['found' => false]);
        }

        $em = new SiteEquipmentModel();
        $builder = $em->where('company_id', $companyId)->where('serial_number', $serial)->where('deleted_at', null);
        if ($siteId > 0) {
            $builder->where('site_id', $siteId);
        }
        $eq = $builder->first();
        if ($eq) {
            return $this->response->setJSON([
                'found'         => true,
                'source'        => 'equipment',
                'id'            => (int) $eq['id'],
                'equipment_id'  => (int) $eq['id'],
                'asset_tag'     => $eq['asset_tag'],
                'make'          => $eq['make'] ?? '',
                'model'         => $eq['model'] ?? '',
                'device_type'   => $eq['device_type'] ?? '',
                'serial_number' => $eq['serial_number'] ?? '',
                'department'    => $eq['department'] ?? '',
                'location'      => $eq['location'] ?? '',
                'est'           => $eq['est'] ?? '0',
                'cal'           => $eq['cal'] ?? '0',
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('inspections i')
            ->select([
                'i.id AS inspection_id', 'i.group_id', 'i.equipment_id',
                'COALESCE(i.asset_tag, e.asset_tag) AS asset_tag',
                'COALESCE(i.make, e.make) AS make',
                'COALESCE(i.model, e.model) AS model',
                'COALESCE(i.device_type, e.device_type) AS device_type',
                'COALESCE(i.serial_number, e.serial_number) AS serial_number',
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS location',
                'COALESCE(i.est, e.est) AS est',
                'COALESCE(i.cal, e.cal) AS cal',
            ])
            ->join('site_equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.company_id', $companyId)
            ->where('i.deleted_at', null)
            ->where('COALESCE(i.serial_number, e.serial_number)', $serial, false);
        if ($siteId > 0) {
            $builder->where('i.site_id', $siteId);
        }
        if ($groupId !== '') {
            $builder->where('i.group_id', $groupId);
        }
        $row = $builder->orderBy('i.id', 'DESC')->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON(['found' => false]);
        }

        return $this->response->setJSON([
            'found'         => true,
            'source'        => 'inspection',
            'id'            => (int) ($row['inspection_id'] ?? 0),
            'inspection_id' => (int) ($row['inspection_id'] ?? 0),
            'equipment_id'  => !empty($row['equipment_id']) ? (int) $row['equipment_id'] : null,
            'group_id'      => $row['group_id'] ?? '',
            'asset_tag'     => $row['asset_tag'] ?? '',
            'make'          => $row['make'] ?? '',
            'model'         => $row['model'] ?? '',
            'device_type'   => $row['device_type'] ?? '',
            'serial_number' => $row['serial_number'] ?? '',
            'department'    => $row['department'] ?? '',
            'location'      => $row['location'] ?? '',
            'est'           => $row['est'] ?? '0',
            'cal'           => $row['cal'] ?? '0',
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

        $em      = new SiteEquipmentModel();
        $results = $em->where('company_id', $companyId)
            ->groupStart()
            ->like('model',       $keyword)
            ->orLike('make',        $keyword)
            ->orLike('device_type', $keyword)
            ->groupEnd()
            ->select('id, make, model, device_type, serial_number, asset_tag, department, location, est, cal')
            ->limit(50)
            ->findAll();

        // De-duplicate by make+model — same as admin portal
        // Prevents the same model appearing once per site/equipment record
        $seen   = [];
        $unique = [];
        foreach ($results as $row) {
            $key = strtolower(trim($row['make'] ?? '') . '|' . trim($row['model'] ?? ''));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[]   = $row;
            }
        }

        return $this->response->setJSON($unique);
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

        $db = \Config\Database::connect();
        $rows = $db->table('inspections i')
            ->select([
                'i.id AS inspections_id', 'i.group_id',
                'COALESCE(i.result, i.status) AS inspection_status',
                'i.notes', 'COALESCE(i.action_performed, i.inspection_type) AS inspection_type',
                'i.scheduled_at', 'i.updated_at',
                'COALESCE(i.asset_tag, e.asset_tag) AS asset_tag',
                'COALESCE(i.model, e.model) AS model',
                'COALESCE(i.make, e.make) AS make',
                'COALESCE(i.device_type, e.device_type) AS device_type',
                'COALESCE(i.serial_number, e.serial_number) AS serial_number',
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS room',
                'COALESCE(i.est, e.est) AS est',
                'COALESCE(i.cal, e.cal) AS cal',
                's.name AS customer_site',
                'COALESCE(u.full_name, u_direct.full_name) AS technician_name',
            ])
            ->join('site_equipment e', 'e.id = i.equipment_id', 'left')
            ->join('sites s', 's.id = i.site_id', 'left')
            ->join('technicians t', 't.id = i.technician_id', 'left')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->join('users u_direct', 'u_direct.id = i.technician_id', 'left')
            ->where('i.group_id', $groupId)
            ->where('i.company_id', $companyId)
            ->where('i.deleted_at', null)
            ->orderBy('i.id', 'DESC')
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

        $db = \Config\Database::connect();
        $row = $db->table('inspections i')
            ->select([
                'i.*',
                'COALESCE(i.asset_tag, e.asset_tag) AS asset_tag',
                'COALESCE(i.model, e.model) AS model',
                'COALESCE(i.make, e.make) AS make',
                'COALESCE(i.device_type, e.device_type) AS device_type',
                'COALESCE(i.serial_number, e.serial_number) AS serial_number',
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS location',
                'COALESCE(i.est, e.est) AS est',
                'COALESCE(i.cal, e.cal) AS cal',
            ])
            ->join('site_equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.id', $id)
            ->where('i.company_id', $companyId)
            ->where('i.deleted_at', null)
            ->get()->getRowArray();

        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }
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
            ->where('id', $inspectionId)
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Inspection not found']);
        }

        $technicianId = (int) $this->request->getPost('technician_id');
        if ($technicianId <= 0) {
            $technicianId = (int) ($existing['technician_id'] ?? 0) ?: (int) $this->resolveTechnicianId();
        }

        $candidate = [
            'asset_tag'        => $this->request->getPost('asset_tag'),
            'make'             => $this->request->getPost('make') ?? $this->request->getPost('manufacturer'),
            'model'            => $this->request->getPost('model') ?? $this->request->getPost('model_name'),
            'device_type'      => $this->request->getPost('device_type') ?? $this->request->getPost('description'),
            'serial_number'    => $this->request->getPost('serial_number'),
            'department'       => $this->request->getPost('department'),
            'location'         => $this->request->getPost('location'),
            'site_id'          => $this->request->getPost('site_id'),
            'scheduled_at'     => $this->request->getPost('scheduled_at'),
            'status'           => $this->request->getPost('status'),
            'result'           => $this->request->getPost('status'),
            'technician_id'    => $technicianId,
            'next_due_date'    => $this->request->getPost('next_due_date'),
            'notes'            => $this->request->getPost('notes'),
            'inspection_type'  => $this->request->getPost('inspection_type'),
            'action_performed' => $this->request->getPost('inspection_type'),
            'pm_frequency'     => $this->request->getPost('pm_frequency'),
            'device_complete'  => $this->request->getPost('device_complete'),
            'est'              => $this->request->getPost('est'),
            'cal'              => $this->request->getPost('cal'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $data = [];
        foreach ($candidate as $field => $val) {
            if ($val !== null) {
                $data[$field] = $val;
            }
        }

        if (empty($data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No fields to update']);
        }

        $updateData = $this->filterToColumns($data, 'inspections');
        $db->table('inspections')->where('id', $inspectionId)->update($updateData);

        return $this->response->setJSON(['success' => true, 'message' => 'Updated successfully', 'csrf_hash' => csrf_hash()]);
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
        $em  = new SiteEquipmentModel();
        $now = date('Y-m-d H:i:s');

        foreach ($items as $item) {
            $siteId      = (int) ($item['site_id']      ?? 0);
            $equipmentId = (int) ($item['equipment_id'] ?? 0);
            $status      = trim((string) ($item['status'] ?? ''));
            $pmFreq      = trim((string) ($item['pm_frequency'] ?? ''));
            $action      = trim((string) ($item['inspection_type'] ?? ''));

            if ($equipmentId === 0 && !empty($item['asset_tag'])) {
                $eq = $em->findByAssetTag($companyId, $siteId, $item['asset_tag']);
                if ($eq) $equipmentId = (int) $eq['id'];
            }

            $nextDueDate = null;
            if ($pmFreq !== '') {
                preg_match('/^(\d+)/', $pmFreq, $m2);
                $months = isset($m2[1]) ? (int) $m2[1] : 0;
                if ($months > 0) $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }

            $itemTechId = !empty($item['technician_id']) ? (int) $item['technician_id'] : $technicianId;

            $snapshotEq = $equipmentId > 0 ? ($em->find($equipmentId) ?: []) : [];
            $candidate = [
                'company_id'      => $companyId,
                'site_id'         => $siteId,
                'equipment_id'    => $equipmentId > 0 ? $equipmentId : null,
                'group_id'        => $groupId,
                'asset_tag'       => trim((string) ($item['asset_tag'] ?? ($snapshotEq['asset_tag'] ?? ''))),
                'make'            => trim((string) ($item['make'] ?? ($snapshotEq['make'] ?? ''))),
                'model'           => trim((string) ($item['model'] ?? ($snapshotEq['model'] ?? ''))),
                'device_type'     => trim((string) ($item['device_type'] ?? ($snapshotEq['device_type'] ?? ''))),
                'serial_number'   => trim((string) ($item['serial_number'] ?? ($snapshotEq['serial_number'] ?? ''))),
                'department'      => trim((string) ($item['department'] ?? ($snapshotEq['department'] ?? ''))),
                'location'        => trim((string) ($item['location'] ?? ($snapshotEq['location'] ?? ''))),
                'scheduled_at'    => !empty($item['scheduled_at']) ? $item['scheduled_at'] : $now,
                'completed_at'    => $now,
                'status'          => $status,
                'result'          => $status,
                'technician_id'   => $itemTechId,
                'findings'        => '',
                'notes'           => $item['notes']  ?? '',
                'inspection_type' => $action,
                'action_performed' => $action,
                'est'             => $item['est'] ?? ($snapshotEq['est'] ?? ''),
                'cal'             => $item['cal'] ?? ($snapshotEq['cal'] ?? ''),
                'pm_frequency'    => $pmFreq,
                'next_due_date'   => $nextDueDate,
                'device_complete' => $item['device_complete'] ?? 'Yes',
                'created_by'      => $userId > 0 ? $userId : null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            $insData = $this->filterToColumns($candidate, 'inspections');

            // Match only by asset_tag — using equipment_id would merge
            // separate devices of the same model into one inspection row.
            $existingBuilder = $db->table('inspections')
                ->where('group_id', $groupId)
                ->where('site_id', $siteId)
                ->where('deleted_at', null);
            if (!empty($candidate['asset_tag'])) {
                $existingBuilder->where('asset_tag', $candidate['asset_tag']);
            } elseif ($equipmentId > 0) {
                $existingBuilder->where('equipment_id', $equipmentId);
            }
            $existing = $existingBuilder->get()->getRowArray();

            if ($existing) {
                $upd = $insData;
                unset($upd['created_at']);
                $upd['updated_at'] = $now;
                $db->table('inspections')->where('id', $existing['id'])->update($upd);
            } else {
                $db->table('inspections')->insert($insData);
            }

            $statusMap = ['Pass' => 'ready', 'Fail' => 'need_attention', 'Repair' => 'need_attention'];
            if ($equipmentId > 0 && isset($statusMap[$status])) {
                $em->update($equipmentId, ['status' => $statusMap[$status]]);
            }

            if ($equipmentId > 0) {
                (new OperationalWorkOrderService())->syncFollowUpFromInspection([
                    'company_id'      => $companyId,
                    'site_id'         => $siteId,
                    'equipment_id'    => $equipmentId,
                    'group_id'        => $groupId,
                    'status'          => $status,
                    'inspection_type' => $action,
                    'notes'           => trim((string) ($item['notes'] ?? '')),
                    'asset_tag'       => trim((string) ($item['asset_tag'] ?? '')),
                    'technician_id'   => $itemTechId,
                    'created_by'      => $userId > 0 ? $userId : null,
                    'start_date'      => !empty($item['scheduled_at']) ? $item['scheduled_at'] : date('Y-m-d'),
                ]);
            }
        }

        return $this->response->setJSON(['success' => true, 'group_id' => $groupId]);
    }
    // ─────────────────────────────────────────────────────────────────
    // POST technician/site-inspection/add-device
    // ─────────────────────────────────────────────────────────────────
    // public function addDevice()
    // {
    //     if (!$this->request->is('post')) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    //     }

    //     $companyId  = (int) session('company_id');
    //     $siteId     = (int) $this->request->getPost('site_id');
    //     $assetTag   = trim((string) $this->request->getPost('asset_tag'));
    //     $model      = trim((string) $this->request->getPost('model'));
    //     $serial     = trim((string) $this->request->getPost('serial_number'));
    //     $make       = trim((string) $this->request->getPost('make'));
    //     $deviceType = trim((string) $this->request->getPost('device_type'));
    //     $dept       = trim((string) $this->request->getPost('department'));
    //     $location   = trim((string) $this->request->getPost('location'));
    //     $est        = trim((string) $this->request->getPost('est')) ?: 'No';
    //     $cal        = trim((string) $this->request->getPost('cal')) ?: 'No';

    //     if ($siteId === 0 || $companyId === 0) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Site ID or Company ID missing.']);
    //     }
    //     if ($assetTag === '') {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Asset # is required.']);
    //     }

    //     $existingAsset = $this->findSiteEquipment($companyId, $siteId, $assetTag);
    //     if ($existingAsset) {
    //         return $this->response->setJSON([
    //             'success'          => true,
    //             'message'          => 'Asset already exists in site inventory.',
    //             'equipment_id'     => (int) $existingAsset['id'],
    //             'asset_tag'        => $existingAsset['asset_tag'],
    //             'make'             => $existingAsset['make'] ?? '',
    //             'model'            => $existingAsset['model'] ?? '',
    //             'device_type'      => $existingAsset['device_type'] ?? '',
    //             'serial_number'    => $existingAsset['serial_number'] ?? '',
    //             'department'       => $existingAsset['department'] ?? $dept,
    //             'location'         => $existingAsset['location'] ?? $location,
    //             'est'              => $existingAsset['est'] ?? $est,
    //             'cal'              => $existingAsset['cal'] ?? $cal,
    //             'start_inspection' => true,
    //             'reused'           => true,
    //             'csrf_hash'        => csrf_hash(),
    //         ]);
    //     }

    //     $masterRef = $this->findMasterReference($companyId, $assetTag, $model);
    //     return $this->response->setJSON([
    //         'success'          => true,
    //         'message'          => 'Device ready for inspection.',
    //         'asset_tag'        => $assetTag,
    //         'make'             => $make !== '' ? $make : ($masterRef['make'] ?? ''),
    //         'model'            => $model !== '' ? $model : ($masterRef['model'] ?? ''),
    //         'device_type'      => $deviceType !== '' ? $deviceType : ($masterRef['device_type'] ?? ''),
    //         'serial_number'    => $serial,
    //         'department'       => $dept,
    //         'location'         => $location,
    //         'est'              => $est,
    //         'cal'              => $cal,
    //         'start_inspection' => true,
    //         'reused'           => false,
    //         'inventory_added'  => false,
    //         'csrf_hash'        => csrf_hash(),
    //     ]);
    // }

    public function addDevice()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $companyId = (int) session('company_id');
        $siteId    = (int) $this->request->getPost('site_id');

        if ($siteId === 0 || $companyId === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Site ID or Company ID missing.'
            ]);
        }

        $assetTag    = trim((string) $this->request->getPost('asset_tag'));
        $model       = trim((string) $this->request->getPost('model'));
        $serial      = trim((string) $this->request->getPost('serial_number'));
        $make        = trim((string) $this->request->getPost('make'));
        $deviceType  = trim((string) $this->request->getPost('device_type'));
        $description = trim((string) $this->request->getPost('description'));
        $dept        = trim((string) $this->request->getPost('department'));
        $location    = trim((string) $this->request->getPost('location'));
        $technician  = trim((string) $this->request->getPost('technician'));
        $est         = trim((string) $this->request->getPost('est')) ?: 'No';
        $cal         = trim((string) $this->request->getPost('cal')) ?: 'No';
        $groupId     = trim((string) $this->request->getPost('group_id'));

        if ($assetTag === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Asset # is required.'
            ]);
        }

        $em = new SiteEquipmentModel();
        $db = \Config\Database::connect();

        // 1) Reuse existing site_equipment if already in site inventory
        $existingAsset = $em->findByAssetTag($companyId, $siteId, $assetTag);

        if ($existingAsset) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => 'Asset already exists in site inventory.',
                'equipment_id'     => (int) $existingAsset['id'],
                'id'               => (int) $existingAsset['id'],
                'asset_tag'        => $existingAsset['asset_tag'] ?? $assetTag,
                'make'             => $existingAsset['make'] ?? $make,
                'model'            => $existingAsset['model'] ?? $model,
                'device_type'      => $existingAsset['device_type'] ?? $deviceType,
                'serial_number'    => $existingAsset['serial_number'] ?? $serial,
                'department'       => $existingAsset['department'] ?? $dept,
                'location'         => $existingAsset['location'] ?? $location,
                'description'      => $description,
                'start_inspection' => true,
                'reused'           => true,
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        // 2) Read-only lookup from master equipment DB to fill in missing make/device_type
        if ((empty($make) || empty($deviceType)) && !empty($model)) {
            $masterRef = $db->query(
                "SELECT make, device_type, est, cal
                FROM equipment
                WHERE company_id = ? AND model = ?
                ORDER BY id ASC
                LIMIT 1",
                [$companyId, $model]
            )->getRow();

            if ($masterRef) {
                if ($make === '')       $make       = (string) $masterRef->make;
                if ($deviceType === '') $deviceType = (string) $masterRef->device_type;
                if ($est === 'No' && $masterRef->est) $est = $masterRef->est ? 'Yes' : 'No';
                if ($cal === 'No' && $masterRef->cal) $cal = $masterRef->cal ? 'Yes' : 'No';
            }
        }

        // 3) Insert new device into site_equipment (per-site working copy)
        //    Link to master catalogue if asset_tag exists there
        $masterEquip = (new EquipmentModel())
            ->where('company_id', $companyId)
            ->where('asset_tag', $assetTag)
            ->where('deleted_at', null)
            ->first();

        $newEquipData = [
            'company_id'          => $companyId,
            'site_id'             => $siteId,
            'master_equipment_id' => $masterEquip ? (int) $masterEquip['id'] : null,
            'asset_tag'           => $assetTag,
            'make'                => $make,
            'model'               => $model,
            'serial_number'       => $serial,
            'device_type'         => $deviceType,
            'department'          => $dept,
            'location'            => $location,
            'est'                 => ($est === 'Yes' || $est === '1') ? 1 : 0,
            'cal'                 => ($cal === 'Yes' || $cal === '1') ? 1 : 0,
            'status'              => 'ready',
        ];

        $newEquipId = $em->safeInsert($newEquipData);

        return $this->response->setJSON([
            'success'          => true,
            'message'          => 'Device added to site inventory and ready to inspect.',
            'equipment_id'     => (int) $newEquipId,
            'id'               => (int) $newEquipId,
            'asset_tag'        => $assetTag,
            'make'             => $make,
            'model'            => $model,
            'device_type'      => $deviceType,
            'serial_number'    => $serial,
            'department'       => $dept,
            'location'         => $location,
            'description'      => $description,
            'start_inspection' => true,
            'reused'           => false,
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
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $companyId = (int) session('company_id');

        if ($siteId === 0) {
            return $this->response->setJSON(['department' => '', 'location' => '']);
        }

        $latest = $this->latestInspectionLocation($companyId, $siteId, $groupId);
        if (!$latest && $groupId !== '') {
            $latest = $this->latestInspectionLocation($companyId, $siteId, '');
        }
        if (!$latest) {
            $latest = (new SiteEquipmentModel())
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->first() ?: [];
        }

        return $this->response->setJSON([
            'department' => $latest['department'] ?? '',
            'location'   => $latest['location'] ?? '',
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