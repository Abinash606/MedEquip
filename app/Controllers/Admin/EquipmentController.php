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

        // Equipment list (with site name for "Customer Location")
        $equipment = $this->equipmentModel
            ->select('equipment.*, sites.name as site_name')
            ->join('sites', 'sites.id = equipment.site_id', 'left')
            ->where('equipment.company_id', $companyId)
            ->where('equipment.deleted_at', null)
            ->orderBy('equipment.id', 'DESC')
            ->findAll();

        // Sites dropdown for Customer Location
        $sites = $this->siteModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/equipment/index', [
            'equipment' => $equipment,
            'sites'     => $sites,
        ]);
    }

    public function show($id)
    {
        $companyId = (int) session('company_id');

        $row = $this->equipmentModel
            ->where('company_id', $companyId)
            ->find((int)$id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Equipment not found']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $row]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $equipmentModel = new EquipmentModel();
            $companyId = (int) session('company_id');

            $assetTag = $this->request->getPost('asset_tag');

            // ── Duplicate check ──────────────────────────────────────
            $existing = $equipmentModel
                ->where('company_id', $companyId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->first();

            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

            if ($existing) {
                if ($wantsJson) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Asset Tag "' . $assetTag . '" already exists for this company. Please use a unique Asset Tag.',
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Duplicate Asset Tag.');
            }
            $validStatuses = ['ready', 'need_attention', 'repair', 'out_of_service'];
            $statusInput = trim($this->request->getPost('status') ?? 'ready');
            $data = [
                'company_id'    => $companyId,
                'asset_tag'     => $assetTag,
                'make'          => $this->request->getPost('make'),
                'model'         => $this->request->getPost('model'),
                'serial_number' => $this->request->getPost('serial_number'),
                'device_type'   => $this->request->getPost('device_type'),
                'location'      => $this->request->getPost('location'),
                'department'    => $this->request->getPost('department'),
                'status' => in_array($statusInput, $validStatuses) ? $statusInput : 'ready',
                'site_id'       => $this->request->getPost('site_id'),
                'est'           => ($this->request->getPost('est') === 'Yes' || $this->request->getPost('est') === '1') ? '1' : '0',
                'cal'           => ($this->request->getPost('cal') === 'Yes' || $this->request->getPost('cal') === '1') ? '1' : '0',
            ];

            $inserted = $equipmentModel->insert($data);

            if ($wantsJson) {
                if ($inserted) {
                    return $this->response->setJSON([
                        'success'       => true,
                        'message'       => 'Equipment added successfully',
                        'id'            => $equipmentModel->getInsertID(),
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
                    'message' => implode(', ', $equipmentModel->errors() ?: ['Failed to add equipment']),
                ]);
            }

            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
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
            $equipmentModel = new EquipmentModel();
            $companyId = (int) session('company_id');

            $validStatuses = ['ready', 'need_attention', 'repair', 'out_of_service'];
            $statusInput = trim($this->request->getPost('status') ?? 'ready');

            $data = [
                'company_id'         => $companyId,
                'asset_tag'          => $this->request->getPost('asset_tag'),
                'make'               => $this->request->getPost('make'),
                'model'              => $this->request->getPost('model'),
                'serial_number'      => $this->request->getPost('serial_number'),
                'device_type'        => $this->request->getPost('device_type'),
                'location'           => $this->request->getPost('location'),
                'department'         => $this->request->getPost('department'),
                'status' => in_array($statusInput, $validStatuses) ? $statusInput : 'ready',
                'site_id'            => $this->request->getPost('site_id'),
                'pm_kit'             => $this->request->getPost('pm_kit'),
                'fast_notes'         => $this->request->getPost('fast_notes'),
                'installation_date'  => $this->request->getPost('installation_date') ?: null,
                'warranty_expires'   => $this->request->getPost('warranty_expires') ?: null,
            ];

            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

            $updated = $equipmentModel->update((int)$id, $data);

            if ($wantsJson) {
                if ($updated !== false) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Equipment updated successfully',
                    ]);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(', ', $equipmentModel->errors() ?: ['Failed to update equipment']),
                ]);
            }

            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $equipmentModel = new EquipmentModel();
        $equipmentModel->delete($id);
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

        $equipmentModel = new \App\Models\EquipmentModel();
        $db       = \Config\Database::connect(); // needed for INSERT IGNORE and duplicate checks
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            // Normalize keys: trim + lowercase all header names
            $norm = [];
            foreach ($row as $k => $v) {
                $norm[trim(strtolower((string)$k))] = trim((string)$v);
            }

            // Map Excel column headers to DB fields
            // Handles: 'Make ', 'Asset Tag ', 'Device Type ', 'Serial Number ', 'Location Or Room '
            $assetTag   = $norm['asset tag']         ?? $norm['asset_tag']      ?? $norm['asset #']   ?? '';
            $make       = $norm['make']               ?? $norm['manufacturer']   ?? '';
            $model      = $norm['model']              ?? $norm['model number']   ?? '';
            $serial     = $norm['serial number']      ?? $norm['serial_number']  ?? $norm['s/n']       ?? $norm['sn'] ?? '';
            $deviceType = $norm['device type']        ?? $norm['device_type']    ?? $norm['type']      ?? '';
            $department = $norm['department']         ?? $norm['dept']           ?? '';
            $location   = $norm['location or room']   ?? $norm['room']           ?? $norm['location']  ?? '';

            // Convert numeric Excel values to strings
            if (is_numeric($assetTag) && $assetTag !== '') $assetTag = (string)(int)$assetTag;
            if (is_numeric($serial)   && $serial   !== '') $serial   = (string)(int)$serial;

            // Clean N/A serial numbers
            if (strtoupper(trim($serial)) === 'N/A') $serial = '';

            // Skip completely empty rows
            if (empty($make) && empty($model)) {
                $skipped++;
                continue;
            }

            // Check for duplicate asset tag using direct DB query (avoids model state issues)
            if (!empty($assetTag)) {
                $dup = $db->query(
                    "SELECT id FROM equipment WHERE company_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                    [$companyId, $assetTag]
                )->getRow();
                if ($dup) {
                    $skipped++;
                    continue;
                }
            } else {
                // Keep incrementing until we find an unused tag (handles bulk rows)
                do {
                    $lastTag = $db->query(
                        "SELECT asset_tag FROM equipment WHERE company_id = ? AND asset_tag LIKE 'ASSET-%' ORDER BY id DESC LIMIT 1",
                        [$companyId]
                    )->getRow();
                    $num = 1000;
                    if ($lastTag && preg_match('/ASSET-(\d+)/', $lastTag->asset_tag, $m)) {
                        $num = (int)$m[1] + 1;
                    }
                    $assetTag = 'ASSET-' . $num;
                    // Check the tag isn't already used (in DB or already planned this batch)
                    $tagExists = $db->query(
                        "SELECT id FROM equipment WHERE company_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                        [$companyId, $assetTag]
                    )->getRow();
                } while ($tagExists);
            }

            // Use INSERT IGNORE so the DB unique constraint never throws a fatal error
            // (handles any race condition where a duplicate slips past the check above)
            try {
                $db->query(
                    "INSERT IGNORE INTO equipment
                     (company_id, site_id, asset_tag, make, model, serial_number, device_type, department, location, status, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'ready', NOW(), NOW())",
                    [$companyId, $siteId, $assetTag, $make, $model, $serial, $deviceType, $department, $location]
                );
                // INSERT IGNORE returns 0 affected rows for duplicates
                if ($db->affectedRows() > 0) {
                    $imported++;
                } else {
                    $skipped++; // Was a duplicate the check above missed
                }
            } catch (\Throwable $e) {
                // Catch any remaining DB errors (shouldn't happen with INSERT IGNORE, but be safe)
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
}
