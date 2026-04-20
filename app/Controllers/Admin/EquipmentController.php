<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteModel;

class EquipmentController extends BaseController
{
    protected EquipmentModel $equipmentModel;
    protected SiteModel $siteModel;

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->siteModel      = new SiteModel();
    }

    public function index()
    {
        $companyId = (int) session('company_id');

        // Group by make + model + device_type — one visible row per model.
        // all_ids and serial_numbers are pipe-separated so the view can render
        // expandable sub-rows (one per series) each with its own Edit/Delete buttons.
        $db = \Config\Database::connect();
        $equipment = $db->query(
            "SELECT
                MIN(id)                          AS id,
                COALESCE(make,'')                AS make,
                COALESCE(model,'')               AS model,
                COALESCE(device_type,'')         AS device_type,
                GROUP_CONCAT(id              ORDER BY serial_number ASC SEPARATOR '|') AS all_ids,
                GROUP_CONCAT(COALESCE(serial_number,'') ORDER BY serial_number ASC SEPARATOR '|') AS serial_numbers,
                MIN(service_manual_path) AS service_manual_path,
                MIN(pm_manual_path)      AS pm_manual_path,
                MIN(photo_path)          AS photo_path,
                company_id
             FROM equipment
             WHERE company_id = ? AND deleted_at IS NULL
             GROUP BY make, model, device_type
             ORDER BY make ASC, model ASC",
            [$companyId]
        )->getResultArray();

        return view('admin/equipment/index', [
            'equipment' => $equipment,
        ]);
    }

    public function show($id)
    {
        $companyId = (int) session('company_id');

        // Must query site_equipment (not the master equipment catalogue).
        // The edit button on the site details page uses site_equipment.id,
        // so looking up in the equipment table returns nothing for most records.
        $seModel = new \App\Models\SiteEquipmentModel();
        $row = $seModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->find((int)$id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Equipment not found']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $row]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $seModel   = new \App\Models\SiteEquipmentModel();
            $companyId = (int) session('company_id');
            $siteId    = (int) $this->request->getPost('site_id');

            $assetTag = $this->request->getPost('asset_tag');

            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

            // ── Duplicate check against site_equipment ───────────────
            $existing = $seModel
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->first();

            if ($existing) {
                if ($wantsJson) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Asset Tag "' . $assetTag . '" already exists for this site. Please use a unique Asset Tag.',
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Duplicate Asset Tag.');
            }

            // ── Link to master catalogue if asset_tag exists there ───
            $masterEquip = (new EquipmentModel())
                ->where('company_id', $companyId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->first();

            $validStatuses = ['ready', 'need_attention', 'repair', 'out_of_service'];
            $statusInput   = trim($this->request->getPost('status') ?? 'ready');

            $data = [
                'company_id'          => $companyId,
                'site_id'             => $siteId,
                'master_equipment_id' => $masterEquip ? (int) $masterEquip['id'] : null,
                'asset_tag'           => $assetTag,
                'make'                => $this->request->getPost('make'),
                'model'               => $this->request->getPost('model'),
                'serial_number'       => $this->request->getPost('serial_number'),
                'device_type'         => $this->request->getPost('device_type'),
                'location'            => $this->request->getPost('location'),
                'department'          => $this->request->getPost('department'),
                'status'              => in_array($statusInput, $validStatuses) ? $statusInput : 'ready',
                'pm_kit'              => $this->request->getPost('pm_kit'),
                'fast_notes'          => $this->request->getPost('fast_notes'),
                'installation_date'   => $this->request->getPost('installation_date') ?: null,
                'warranty_expires'    => $this->request->getPost('warranty_expires') ?: null,
                'est'                 => ($this->request->getPost('est') === 'Yes' || $this->request->getPost('est') === '1') ? '1' : '0',
                'cal'                 => ($this->request->getPost('cal') === 'Yes' || $this->request->getPost('cal') === '1') ? '1' : '0',
            ];

            $newId    = $seModel->safeInsert($data);
            $inserted = $newId > 0;

            if ($wantsJson) {
                if ($inserted) {
                    return $this->response->setJSON([
                        'success'       => true,
                        'message'       => 'Equipment added successfully',
                        'id'            => $newId,
                        'asset_tag'     => $data['asset_tag'],
                        'make'          => $data['make'],
                        'model'         => $data['model'],
                        'serial_number' => $data['serial_number'],
                        'device_type'   => $data['device_type'],
                        'department'    => $data['department'],
                        'location'      => $data['location'],
                        'site_id'       => $data['site_id'],
                    ]);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(', ', $seModel->errors() ?: ['Failed to add equipment']),
                ]);
            }

            return redirect()->to('/admin/sites/' . $siteId);
        }
    }

    public function edit($id)
    {
        $equipmentModel = new EquipmentModel();
        $data['equipment'] = $equipmentModel->find($id);
        return view('admin/equipment/edit', $data);
    }

    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $seModel   = new \App\Models\SiteEquipmentModel();
            $companyId = (int) session('company_id');

            $validStatuses = ['ready', 'need_attention', 'repair', 'out_of_service'];
            $statusInput   = trim($this->request->getPost('status') ?? 'ready');

            $data = [
                'company_id'         => $companyId,
                'asset_tag'          => $this->request->getPost('asset_tag'),
                'make'               => $this->request->getPost('make'),
                'model'              => $this->request->getPost('model'),
                'serial_number'      => $this->request->getPost('serial_number'),
                'device_type'        => $this->request->getPost('device_type'),
                'location'           => $this->request->getPost('location'),
                'department'         => $this->request->getPost('department'),
                'status'             => in_array($statusInput, $validStatuses) ? $statusInput : 'ready',
                'site_id'            => $this->request->getPost('site_id'),
                'pm_kit'             => $this->request->getPost('pm_kit'),
                'fast_notes'         => $this->request->getPost('fast_notes'),
                'installation_date'  => $this->request->getPost('installation_date') ?: null,
                'warranty_expires'   => $this->request->getPost('warranty_expires') ?: null,
            ];

            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

            $updated = $seModel->update((int)$id, $data);

            if ($wantsJson) {
                if ($updated !== false) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Equipment updated successfully',
                    ]);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(', ', $seModel->errors() ?: ['Failed to update equipment']),
                ]);
            }

            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $seModel = new \App\Models\SiteEquipmentModel();
        $seModel->delete($id);
        return redirect()->back();
    }


    /* ================= ADD + UPDATE ================= */


    // public function save()
    // {
    //     $companyId = (int) session('company_id');
    //     $id        = (int) ($this->request->getPost('id') ?? 0);

    //     $rules = [
    //         'make'        => 'permit_empty|max_length[100]',
    //         'model'       => 'permit_empty|max_length[100]',
    //         'device_type' => 'permit_empty|max_length[100]',
    //     ];

    //     if (! $this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }

    //     // 🔹 Asset tag auto-generate
    //     $assetTag = trim((string) $this->request->getPost('asset_tag'));
    //     if ($assetTag === '') {
    //         $assetTag = 'AT-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    //     }

    //     // 🔹 Existing record (for edit)
    //     $existing = $id ? $this->equipmentModel->find($id) : [];

    //     /** =========================
    //      *  FILE UPLOADS
    //      *  ========================= */
    //     $pmPath    = $existing['pm_manual_path'] ?? null;
    //     $photoPath = $existing['photo_path'] ?? null;

    //     // 📄 PM MANUAL
    //     $pmFile = $this->request->getFile('pm_manual');
    //     if ($pmFile && $pmFile->isValid()) {
    //         $newName = $pmFile->getRandomName();
    //         $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
    //         $pmPath = 'uploads/pm_manuals/' . $newName;
    //     }

    //     // 🖼 PHOTO
    //     $photoFile = $this->request->getFile('photo');
    //     if ($photoFile && $photoFile->isValid()) {
    //         $newName = $photoFile->getRandomName();
    //         $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
    //         $photoPath = 'uploads/equipment_photos/' . $newName;
    //     }

    //     $payload = [
    //         'company_id'      => $companyId,
    //         'site_id'         => 1,
    //         'make'            => trim($this->request->getPost('make')),
    //         'model'           => trim($this->request->getPost('model')),
    //         'device_type'     => trim($this->request->getPost('device_type')),
    //         'serial_number'   => trim($this->request->getPost('serial_number')),
    //         'asset_tag'       => $assetTag,
    //         'status'          => 'ready',
    //         'pm_manual_path'  => $pmPath,
    //         'photo_path'      => $photoPath,
    //     ];

    //     if ($id > 0) {
    //         $this->equipmentModel->update($id, $payload);
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Equipment updated',
    //             'data' => $payload
    //         ]);
    //     }

    //     $newId = $this->equipmentModel->insert($payload);

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'message' => 'Equipment added',
    //         'id' => $newId,
    //         'data' => $payload
    //     ]);
    // }

    public function save()
    {
        $companyId = (int) session('company_id');
        $id        = (int) ($this->request->getPost('id') ?? 0);

        $rules = [
            'make'            => 'permit_empty|max_length[100]',
            'model'           => 'permit_empty|max_length[100]',
            'device_type'     => 'permit_empty|max_length[100]',
            'pm_manual'       => 'permit_empty|max_size[pm_manual,5120]',
            'service_manual'  => 'permit_empty|max_size[service_manual,5120]',
            'photo'           => 'permit_empty|max_size[photo,5120]',
        ];

        // if (! $this->validate($rules)) {
        //     return $this->response->setJSON([
        //         'status' => 'error',
        //         'errors' => $this->validator->getErrors()
        //     ]);
        // }

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode("\n", $this->validator->getErrors())
            ]);
        }


        // 🔹 Auto-generate asset tag
        $assetTag = 'AT-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // Existing record (edit case)
        $existing = $id ? $this->equipmentModel->find($id) : [];

        $pmPath      = $existing['pm_manual_path'] ?? null;
        $servicePath = $existing['service_manual_path'] ?? null;
        $photoPath   = $existing['photo_path'] ?? null;

        // 📄 PM Manual
        $pmFile = $this->request->getFile('pm_manual');
        if ($pmFile && $pmFile->isValid()) {
            $newName = $pmFile->getRandomName();
            $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
            $pmPath = 'uploads/pm_manuals/' . $newName;
        }

        // 📘 Service Manual (PDF)
        $serviceFile = $this->request->getFile('service_manual');
        if ($serviceFile && $serviceFile->isValid()) {
            $newName = $serviceFile->getRandomName();
            $serviceFile->move(FCPATH . 'uploads/service_manuals', $newName);
            $servicePath = 'uploads/service_manuals/' . $newName;
        }

        // 🖼 Photo
        $photoFile = $this->request->getFile('photo');
        if ($photoFile && $photoFile->isValid()) {
            $newName = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
            $photoPath = 'uploads/equipment_photos/' . $newName;
        }

        $payload = [
            'company_id'            => $companyId,
            'site_id'               => 1,
            'make'                  => trim($this->request->getPost('make')),
            'model'                 => trim($this->request->getPost('model')),
            'device_type'           => trim($this->request->getPost('device_type')),
            'serial_number'         => trim($this->request->getPost('serial_number')),
            'asset_tag'             => $assetTag,
            'status'                => 'ready',
            'pm_manual_path'        => $pmPath,
            'service_manual_path'   => $servicePath,
            'photo_path'            => $photoPath,
        ];

        if ($id > 0) {
            $this->equipmentModel->update($id, $payload);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Equipment updated'
            ]);
        }

        $this->equipmentModel->insert($payload);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Equipment added'
        ]);
    }


    /* ================= DELETE ================= */
    public function deletedb($id)
    {
        $this->equipmentModel->delete((int) $id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Equipment deleted'
        ]);
    }


    /**
     * Bulk import equipment from Excel/CSV.
     * Matches columns: Make, Model, Device Type, Asset Tag, Serial Number, Department, Location Or Room
     * Route: POST /admin/equipment/bulk-import
     */
    public function bulkImport()
    {
        $companyId = (int) session('company_id');
        $siteId    = (int) $this->request->getPost('site_id');

        if (!$siteId) {
            return $this->response->setJSON(['success' => false, 'message' => 'No site_id provided.']);
        }

        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid file uploaded.']);
        }

        $ext     = strtolower($file->getClientExtension());
        $tmpPath = $file->getTempName();
        $rows    = [];

        if ($ext === 'csv') {
            if (($handle = fopen($tmpPath, 'r')) !== false) {
                $headers = null;
                while (($line = fgetcsv($handle)) !== false) {
                    if ($headers === null) {
                        $headers = array_map('trim', array_map('strtolower', $line));
                        continue;
                    }
                    if (!empty(array_filter($line))) {
                        $rows[] = array_combine($headers, array_pad($line, count($headers), ''));
                    }
                }
                fclose($handle);
            }
        } elseif (in_array($ext, ['xlsx', 'xls'])) {
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                // Fallback: use openpyxl-style reading via direct file parse
                // Try using openpyxl via Python as a system call
                $jsonOut = shell_exec('python3 -c "
import json, sys
try:
    from openpyxl import load_workbook
    wb = load_workbook(\"' . addslashes($tmpPath) . '\", read_only=True)
    ws = wb.active
    data = []
    for row in ws.iter_rows(values_only=True):
        data.append([str(v) if v is not None else \"\" for v in row])
    print(json.dumps(data))
except Exception as e:
    print(json.dumps({\"error\": str(e)}))
" 2>/dev/null');

                if ($jsonOut) {
                    $parsed = json_decode($jsonOut, true);
                    if (!empty($parsed) && !isset($parsed['error']) && count($parsed) > 1) {
                        $headers = array_map('trim', array_map('strtolower', $parsed[0]));
                        for ($i = 1; $i < count($parsed); $i++) {
                            if (!empty(array_filter($parsed[$i]))) {
                                $rows[] = array_combine($headers, array_pad($parsed[$i], count($headers), ''));
                            }
                        }
                    }
                }

                if (empty($rows)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'PhpSpreadsheet not installed and Python fallback failed. Please upload a CSV file.']);
                }
            } else {
                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpPath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $data  = $sheet->toArray(null, true, true, false);
                    if (!empty($data)) {
                        $headers = array_map('trim', array_map('strtolower', $data[0]));
                        for ($i = 1; $i < count($data); $i++) {
                            if (!empty(array_filter($data[$i]))) {
                                $rows[] = array_combine($headers, array_pad($data[$i], count($headers), ''));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Could not read Excel file: ' . $e->getMessage()]);
                }
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Only .xlsx, .xls, or .csv files accepted.']);
        }

        if (empty($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No data rows found in file. Check the file has a header row.']);
        }

        $seModel  = new \App\Models\SiteEquipmentModel();
        $db       = \Config\Database::connect();
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            // Normalize keys: trim + lowercase all header names
            $norm = [];
            foreach ($row as $k => $v) {
                $norm[trim(strtolower((string)$k))] = trim((string)$v);
            }

            // Map Excel column headers to DB fields
            $assetTag   = $norm['asset tag']         ?? $norm['asset_tag']      ?? $norm['asset #']   ?? '';
            $make       = $norm['make']               ?? $norm['manufacturer']   ?? '';
            $model      = $norm['model']              ?? $norm['model number']   ?? '';
            $serial     = $norm['serial number']      ?? $norm['serial_number']  ?? $norm['s/n']       ?? $norm['sn'] ?? '';
            $deviceType = $norm['device type']        ?? $norm['device_type']    ?? $norm['type']      ?? '';
            $department = $norm['department']         ?? $norm['dept']           ?? '';
            $location   = $norm['location or room']   ?? $norm['room']           ?? $norm['location']  ?? '';

            if (is_numeric($assetTag) && $assetTag !== '') $assetTag = (string)(int)$assetTag;
            if (is_numeric($serial)   && $serial   !== '') $serial   = (string)(int)$serial;
            if (strtoupper(trim($serial)) === 'N/A') $serial = '';

            if (empty($make) && empty($model)) {
                $skipped++;
                continue;
            }

            // Duplicate check against site_equipment for this site
            if (!empty($assetTag)) {
                $dup = $db->query(
                    "SELECT id FROM site_equipment WHERE company_id = ? AND site_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                    [$companyId, $siteId, $assetTag]
                )->getRow();
                if ($dup) {
                    $skipped++;
                    continue;
                }
            } else {
                // Auto-generate asset tag from site_equipment
                do {
                    $lastTag = $db->query(
                        "SELECT asset_tag FROM site_equipment WHERE company_id = ? AND asset_tag LIKE 'ASSET-%' ORDER BY id DESC LIMIT 1",
                        [$companyId]
                    )->getRow();
                    $num = 1000;
                    if ($lastTag && preg_match('/ASSET-(\d+)/', $lastTag->asset_tag, $m)) {
                        $num = (int)$m[1] + 1;
                    }
                    $assetTag = 'ASSET-' . $num;
                    $tagExists = $db->query(
                        "SELECT id FROM site_equipment WHERE company_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                        [$companyId, $assetTag]
                    )->getRow();
                } while ($tagExists);
            }

            // Link to master catalogue if asset_tag matches
            $master = $db->query(
                "SELECT id FROM equipment WHERE company_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                [$companyId, $assetTag]
            )->getRow();

            try {
                $db->query(
                    "INSERT IGNORE INTO site_equipment
                     (company_id, site_id, master_equipment_id, asset_tag, make, model, serial_number, device_type, department, location, status, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ready', NOW(), NOW())",
                    [$companyId, $siteId, $master ? $master->id : null, $assetTag, $make, $model, $serial, $deviceType, $department, $location]
                );
                if ($db->affectedRows() > 0) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                log_message('warning', '[bulkImport] Skipping row due to DB error: ' . $e->getMessage());
                $skipped++;
            }
        }

        return $this->response->setJSON([
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'message'  => "$imported imported, $skipped skipped.",
        ]);
    }
    public function getDropdownOptions()
    {
        $companyId = (int) session('company_id');
        $db = \Config\Database::connect();

        // Union master equipment catalogue with site_equipment so that
        // devices added directly to a site (without a master record) also
        // appear in the autocomplete suggestions.
        $makes = $db->query(
            "SELECT DISTINCT make FROM (
                SELECT make FROM equipment WHERE make IS NOT NULL AND make != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT make FROM site_equipment WHERE make IS NOT NULL AND make != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY make",
            [$companyId, $companyId]
        )->getResultArray();

        $models = $db->query(
            "SELECT DISTINCT model FROM (
                SELECT model FROM equipment WHERE model IS NOT NULL AND model != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT model FROM site_equipment WHERE model IS NOT NULL AND model != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY model",
            [$companyId, $companyId]
        )->getResultArray();

        $deviceTypes = $db->query(
            "SELECT DISTINCT device_type FROM (
                SELECT device_type FROM equipment WHERE device_type IS NOT NULL AND device_type != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT device_type FROM site_equipment WHERE device_type IS NOT NULL AND device_type != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY device_type",
            [$companyId, $companyId]
        )->getResultArray();

        return $this->response->setJSON([
            'success'      => true,
            'makes'        => array_column($makes, 'make'),
            'models'       => array_column($models, 'model'),
            'device_types' => array_column($deviceTypes, 'device_type'),
        ]);
    }
}
