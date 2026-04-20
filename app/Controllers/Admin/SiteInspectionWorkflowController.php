<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteEquipmentModel;
use App\Models\InspectionModel;
use App\Libraries\OperationalWorkOrderService;

/**
 * SiteInspectionWorkflowController
 *
 * This controller provides lightweight AJAX endpoints to support the
 * interactive inspection workflow embedded in the site details page.  It
 * exposes actions for looking up equipment by asset tag and recording
 * inspection results without requiring a full page refresh.  These
 * endpoints are intentionally minimalist and do not include complex
 * business logic – they simply read and write records and return JSON
 * responses for the frontend to consume.
 */
class SiteInspectionWorkflowController extends BaseController
{
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
        if ($siteId <= 0 || $assetTag === '') return null;
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
        if ($siteId <= 0 || $assetTag === '') return null;
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
                'COALESCE(i.action_performed, i.inspection_type) AS action_performed',
                'i.notes',
            ])
            ->join('equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.company_id', $companyId)
            ->where('i.site_id', $siteId)
            ->where('i.deleted_at', null)
            ->where('COALESCE(i.asset_tag, e.asset_tag)', $assetTag, false);
        if ($groupId !== '') $builder->where('i.group_id', $groupId);
        $row = $builder->orderBy('i.id', 'DESC')->get()->getRowArray();
        return $row ?: null;
    }

    private function latestInspectionLocation(int $companyId, int $siteId, string $groupId = ''): ?array
    {
        if ($siteId <= 0) return null;
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i')
            ->select([
                'COALESCE(i.department, e.department) AS department',
                'COALESCE(i.location, e.location) AS location',
            ])
            ->join('equipment e', 'e.id = i.equipment_id', 'left')
            ->where('i.company_id', $companyId)
            ->where('i.site_id', $siteId)
            ->where('i.deleted_at', null)
            ->groupStart()->where('i.department <>', '')->orWhere('i.location <>', '')->orWhere('e.department <>', '')->orWhere('e.location <>', '')->groupEnd();
        if ($groupId !== '') $builder->where('i.group_id', $groupId);
        $row = $builder->orderBy('i.id', 'DESC')->get()->getRowArray();
        return $row ?: null;
    }
    /**
     * Lookup equipment by asset tag within a site.
     *
     * Accepts GET parameters:
     *   - asset_tag: the asset tag string to search for
     *   - site_id: the current site id
     *
     * Returns JSON indicating whether the equipment was found and, if so,
     * basic details about the device.  This endpoint is used by the
     * inspection workflow to pre-fill form fields when scanning or
     * entering an asset number.
     */
    // public function getEquipment()
    // {
    //     $assetTag  = trim((string) $this->request->getGet('asset_tag'));
    //     $siteId    = (int) $this->request->getGet('site_id');
    //     $groupId   = trim((string) $this->request->getGet('group_id'));
    //     $companyId = (int) session('company_id');

    //     if ($assetTag === '' || $siteId === 0) {
    //         return $this->response->setJSON(['found' => false]);
    //     }

    //     $eq = $this->findSiteEquipment($companyId, $siteId, $assetTag);
    //     if ($eq) {
    //         return $this->response->setJSON([
    //             'found'         => true,
    //             'source'        => 'equipment',
    //             'id'            => (int) $eq['id'],
    //             'equipment_id'  => (int) $eq['id'],
    //             'asset_tag'     => $eq['asset_tag'],
    //             'make'          => $eq['make'] ?? '',
    //             'model'         => $eq['model'] ?? '',
    //             'device_type'   => $eq['device_type'] ?? '',
    //             'serial_number' => $eq['serial_number'] ?? '',
    //             'department'    => $eq['department'] ?? '',
    //             'location'      => $eq['location'] ?? '',
    //             'est'           => $eq['est'] ?? '0',
    //             'cal'           => $eq['cal'] ?? '0',
    //         ]);
    //     }

    //     $snapshot = $this->findInspectionSnapshotByAsset($companyId, $siteId, $assetTag, $groupId);
    //     if (!$snapshot && $groupId !== '') {
    //         $snapshot = $this->findInspectionSnapshotByAsset($companyId, $siteId, $assetTag, '');
    //     }
    //     if (!$snapshot) {
    //         return $this->response->setJSON(['found' => false]);
    //     }

    //     return $this->response->setJSON([
    //         'found'            => true,
    //         'source'           => 'inspection',
    //         'id'               => (int) ($snapshot['inspection_id'] ?? 0),
    //         'inspection_id'    => (int) ($snapshot['inspection_id'] ?? 0),
    //         'equipment_id'     => !empty($snapshot['equipment_id']) ? (int) $snapshot['equipment_id'] : null,
    //         'group_id'         => $snapshot['group_id'] ?? '',
    //         'asset_tag'        => $snapshot['asset_tag'] ?? $assetTag,
    //         'make'             => $snapshot['make'] ?? '',
    //         'model'            => $snapshot['model'] ?? '',
    //         'device_type'      => $snapshot['device_type'] ?? '',
    //         'serial_number'    => $snapshot['serial_number'] ?? '',
    //         'department'       => $snapshot['department'] ?? '',
    //         'location'         => $snapshot['location'] ?? '',
    //         'est'              => $snapshot['est'] ?? '0',
    //         'cal'              => $snapshot['cal'] ?? '0',
    //         'notes'            => $snapshot['notes'] ?? '',
    //         'action_performed' => $snapshot['action_performed'] ?? '',
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

        // 1. Look up in site_equipment (per-site working copy)
        $seModel = new SiteEquipmentModel();
        $eq = $seModel->findByAssetTag($companyId, $siteId, $assetTag);


        // FIX 4: If not found by findByAssetTag, do a direct DB lookup.
        // This catches assets whose asset_tag was just updated via recordInspection.
        // Without this, rescanning an updated tag opens 'Add New Device' incorrectly.
        if (!$eq) {
            $dbConn = \Config\Database::connect();
            $directRow = $dbConn->table('site_equipment')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->get()->getRowArray();
            if ($directRow) $eq = $directRow;
        }        if ($eq) {
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

        // 2. Check master equipment catalogue for make/model auto-fill
        $master = (new EquipmentModel())
            ->where('company_id', $companyId)
            ->where('asset_tag', $assetTag)
            ->where('deleted_at', null)
            ->first();

        if ($master) {
            // Auto-create a site_equipment record from the master catalogue
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

        return $this->response->setJSON(['found' => false]);
    }
    /**
     * Return the department and room from the most recently saved equipment
     * record for a given site.  This is used to auto-fill the Department and
     * Room fields in the "Add New Device" modal so operators don't have to
     * re-type the same location on every device in the same area.
     *
     * The value is fetched fresh from the database on every call so it
     * persists across sessions and logins without any client-side storage.
     *
     * Accepts GET parameter:
     *   - site_id: the current site id
     *
     * Returns JSON { department: string, location: string }
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
     * Record an inspection result for a single device.
     *
     * This endpoint accepts a POST request with the details of the
     * inspection and creates a corresponding record in the inspections
     * table.  It optionally updates the associated equipment record
     * with the provided department, room and serial number.  After
     * saving the inspection the equipment status is updated based on
     * the result.  The statuses used here (Ready, Needs Attention,
     * Repair) are suggestions – adjust them to match your business
     * rules as needed.
     *
     * Expected POST fields:
     *   - asset_tag (string)
     *   - site_id (int)
     *   - result (string)       Pass | Fail | Repair
     *   - notes (string)
     *   - department (string)
     *   - room (string)
     *   - serial_number (string)
     *   - action_performed (string)
     *   - pm_frequency (string)
     *   - est (string: Yes/No)
     *   - cal (string: Yes/No)
     *
     * Returns JSON { success: bool, message: string }
     */
    public function recordInspection()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $companyId    = (int) session('company_id');
        $userId       = (int) session('user_id');
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
            'technician_id'    => null, // keep admin-side simple for now
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

        // FIX 3: When user changed the asset tag on pass/fail screen,
        // update site_equipment in-place so old tag leaves 'Not Inspected'
        // and new tag is recognised on the next scan (FIX 4).
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
            // FIX 3: update asset_tag in the inspection row to the new value
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

    /**
     * POST admin/site-inspection/add-device
     *
     * Adds a new device to SITE INVENTORY ONLY.
     * The master equipment DB (site_id = 1) is treated as READ-ONLY.
     * Serial number and asset tag are validated for uniqueness within the site.
     */
    // public function addDevice()
    // {
    //     if (!$this->request->is('post')) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    //     }

    //     $companyId = (int) session('company_id');
    //     $siteId    = (int) $this->request->getPost('site_id');

    //     if ($siteId === 0 || $companyId === 0) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Site ID or Company ID missing.']);
    //     }

    //     $assetTag   = trim((string) $this->request->getPost('asset_tag'));
    //     $model      = trim((string) $this->request->getPost('model'));
    //     $serial     = trim((string) $this->request->getPost('serial_number'));
    //     $make       = trim((string) $this->request->getPost('make'));
    //     $deviceType = trim((string) $this->request->getPost('device_type'));
    //     $dept       = trim((string) $this->request->getPost('department'));
    //     $location   = trim((string) $this->request->getPost('location'));
    //     $est        = trim((string) $this->request->getPost('est')) ?: 'No';
    //     $cal        = trim((string) $this->request->getPost('cal')) ?: 'No';

    //     if ($assetTag === '') {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Asset # is required.']);
    //     }

    //     $em = new EquipmentModel();
    //     $db = \Config\Database::connect();

    //     // ── If the asset already exists in this site's inventory, REUSE IT ────
    //     // Do NOT create a duplicate — return the existing record so the
    //     // inspection can link to it directly.
    //     $existingAsset = $em->where('company_id', $companyId)
    //         ->where('site_id', $siteId)
    //         ->where('asset_tag', $assetTag)
    //         ->where('deleted_at', null)
    //         ->first();
    //     if ($existingAsset) {
    //         return $this->response->setJSON([
    //             'success'      => true,
    //             'message'      => 'Asset already in site inventory — using existing record.',
    //             'id'           => $existingAsset['id'],
    //             'asset_tag'    => $existingAsset['asset_tag'],
    //             'model'        => $existingAsset['model']       ?? '',
    //             'make'         => $existingAsset['make']        ?? '',
    //             'device_type'  => $existingAsset['device_type'] ?? '',
    //             'department'   => $existingAsset['department']  ?? '',
    //             'location'     => $existingAsset['location']    ?? '',
    //             'serial_number'=> $existingAsset['serial_number'] ?? '',
    //             'reused'       => true,
    //             'csrf_hash'    => csrf_hash(),
    //         ]);
    //     }

    //     // ── Pull brand/description from master equipment DB (READ-ONLY) ───────
    //     // Never write to the master DB (site_id = 1). Only read make/device_type
    //     // from it so the new site-inventory record is populated correctly.
    //     if ((empty($make) || empty($deviceType)) && !empty($model)) {
    //         $masterRef = $db->query(
    //             "SELECT make, device_type FROM equipment
    //              WHERE company_id = ? AND model = ? AND site_id = 1 LIMIT 1",
    //             [$companyId, $model]
    //         )->getRow();
    //         if ($masterRef) {
    //             if (empty($make))       $make       = $masterRef->make;
    //             if (empty($deviceType)) $deviceType = $masterRef->device_type;
    //         }
    //     }

    //     // ── Insert ONE new record into the site inventory ─────────────────────
    //     $payload = [
    //         'company_id'    => $companyId,
    //         'site_id'       => $siteId,
    //         'asset_tag'     => $assetTag,
    //         'model'         => $model,
    //         'make'          => $make,
    //         'serial_number' => $serial,
    //         'device_type'   => $deviceType,
    //         'department'    => $dept,
    //         'location'      => $location,
    //         'est'           => ($est === 'Yes' || $est === '1') ? '1' : '0',
    //         'cal'           => ($cal === 'Yes' || $cal === '1') ? '1' : '0',
    //         'status'        => 'ready',
    //         'created_at'    => date('Y-m-d H:i:s'),
    //         'updated_at'    => date('Y-m-d H:i:s'),
    //     ];

    //     // Strip any keys not present in the equipment table
    //     $columns = $db->query("SHOW COLUMNS FROM equipment")->getResultArray();
    //     $colNames = array_column($columns, 'Field');
    //     $insertData = array_intersect_key($payload, array_flip($colNames));

    //     $em->insert($insertData);
    //     $newId = $em->getInsertID();

    //     if (!$newId) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Failed to save device to site inventory.']);
    //     }

    //     return $this->response->setJSON([
    //         'success'      => true,
    //         'message'      => 'Device added to site inventory.',
    //         'id'           => $newId,
    //         'asset_tag'    => $assetTag,
    //         'model'        => $model,
    //         'make'         => $make,
    //         'device_type'  => $deviceType,
    //         'department'   => $dept,
    //         'location'     => $location,
    //         'serial_number'=> $serial,
    //         'reused'       => false,
    //         'csrf_hash'    => csrf_hash(),
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

        $assetTag   = trim((string) $this->request->getPost('asset_tag'));
        $model      = trim((string) $this->request->getPost('model'));
        $serial     = trim((string) $this->request->getPost('serial_number'));
        $make       = trim((string) $this->request->getPost('make'));
        $deviceType = trim((string) $this->request->getPost('device_type'));
        $dept       = trim((string) $this->request->getPost('department'));
        $location   = trim((string) $this->request->getPost('location'));
        $est        = trim((string) $this->request->getPost('est')) ?: 'No';
        $cal        = trim((string) $this->request->getPost('cal')) ?: 'No';

        if ($assetTag === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Asset # is required.'
            ]);
        }

        $seModel = new SiteEquipmentModel();
        $db = \Config\Database::connect();

        // 1) Reuse existing site_equipment if already in site inventory
        $existingAsset = $seModel->findByAssetTag($companyId, $siteId, $assetTag);

        if ($existingAsset) {
            return $this->response->setJSON([
                'success'       => true,
                'message'       => 'Asset already exists in site inventory.',
                'id'            => (int) $existingAsset['id'],
                'equipment_id'  => (int) $existingAsset['id'],
                'asset_tag'     => $existingAsset['asset_tag'] ?? $assetTag,
                'model'         => $existingAsset['model'] ?? $model,
                'make'          => $existingAsset['make'] ?? $make,
                'device_type'   => $existingAsset['device_type'] ?? $deviceType,
                'department'    => $existingAsset['department'] ?? $dept,
                'location'      => $existingAsset['location'] ?? $location,
                'serial_number' => $existingAsset['serial_number'] ?? $serial,
                'reused'        => true,
                'start_inspection' => true,
                'csrf_hash'     => csrf_hash(),
            ]);
        }

        // 2) Pull make/device_type from master equipment catalogue if missing
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

        // 3) Insert new device into site_equipment (working copy for this site)
        //    Links to master catalogue via master_equipment_id if asset_tag matches.
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

        $newEquipId = $seModel->safeInsert($newEquipData);

        return $this->response->setJSON([
            'success'       => true,
            'message'       => 'Device added to site inventory and ready to inspect.',
            'id'            => (int) $newEquipId,
            'equipment_id'  => (int) $newEquipId,
            'asset_tag'     => $assetTag,
            'model'         => $model,
            'make'          => $make,
            'device_type'   => $deviceType,
            'department'    => $dept,
            'location'      => $location,
            'serial_number' => $serial,
            'reused'        => false,
            'start_inspection' => true,
            'csrf_hash'     => csrf_hash(),
        ]);
    }
}