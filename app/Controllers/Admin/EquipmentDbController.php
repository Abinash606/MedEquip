<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;

/**
 * EquipmentDbController
 *
 * Handles the master Equipment DB catalogue (admin/equipment page).
 * Operates ONLY on the `equipment` table via EquipmentModel.
 *
 * EquipmentController handles site-level equipment (site_equipment table).
 * These two controllers must never be mixed up.
 *
 * Routes:
 *   GET  admin/equipment-db/:id       -> show()         (load record for edit modal)
 *   POST admin/equipment-db/save      -> save()         (insert or update)
 *   POST admin/equipment-db/delete/:id -> deleteRecord() (soft delete)
 */
class EquipmentDbController extends BaseController
{
    private EquipmentModel $model;

    public function __construct()
    {
        $this->model = new EquipmentModel();
    }

    // ─────────────────────────────────────────────────────────────────────
    // SHOW — return a single master equipment record as JSON for edit modal
    // GET admin/equipment-db/:id
    // ─────────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $companyId = (int) session('company_id');

        $row = $this->model
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->find((int) $id);

        if (!$row) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Equipment not found.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $row]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // SAVE — insert (new) or update (edit) a master equipment record
    // POST admin/equipment-db/save
    // ─────────────────────────────────────────────────────────────────────
    public function save()
    {
        $companyId = (int) session('company_id');
        $id        = (int) ($this->request->getPost('id') ?? 0);

        // Validate file sizes (max 5 MB each)
        $rules = [
            'service_manual' => 'permit_empty|max_size[service_manual,5120]',
            'pm_manual'      => 'permit_empty|max_size[pm_manual,5120]',
            'photo'          => 'permit_empty|max_size[photo,5120]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode("\n", $this->validator->getErrors()),
            ]);
        }

        // Load existing record to preserve file paths and asset_tag on update
        $existing = $id > 0
            ? $this->model->where('company_id', $companyId)->find($id)
            : null;

        if ($id > 0 && !$existing) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Equipment record not found.',
            ]);
        }

        // ── File uploads ──────────────────────────────────────────────────
        $servicePath = $existing['service_manual_path'] ?? null;
        $pmPath      = $existing['pm_manual_path']      ?? null;
        $photoPath   = $existing['photo_path']          ?? null;

        $serviceFile = $this->request->getFile('service_manual');
        if ($serviceFile && $serviceFile->isValid() && !$serviceFile->hasMoved()) {
            $newName     = $serviceFile->getRandomName();
            $serviceFile->move(FCPATH . 'uploads/service_manuals', $newName);
            $servicePath = 'uploads/service_manuals/' . $newName;
        }

        $pmFile = $this->request->getFile('pm_manual');
        if ($pmFile && $pmFile->isValid() && !$pmFile->hasMoved()) {
            $newName = $pmFile->getRandomName();
            $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
            $pmPath  = 'uploads/pm_manuals/' . $newName;
        }

        $photoFile = $this->request->getFile('photo');
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            $newName   = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
            $photoPath = 'uploads/equipment_photos/' . $newName;
        }

        // ── Asset tag — preserve on update, generate for new records ──────
        if ($existing) {
            $assetTag = $existing['asset_tag'] ?? ('ASSET-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT));
        } else {
            $assetTag = 'ASSET-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        }

        // Helper: strip any accidental "new_" prefix left by Select2 tags
        $stripNew = function(string $v): string {
            return preg_replace('/^new_/i', '', trim($v));
        };

        $make       = $stripNew((string) $this->request->getPost('make'));
        $model      = $stripNew((string) $this->request->getPost('model'));
        $deviceType = $stripNew((string) $this->request->getPost('device_type'));

        if ($make === '' || $model === '' || $deviceType === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Brand, Model and Description are all required.',
            ]);
        }

        // ── Build payload ─────────────────────────────────────────────────
        $payload = [
            'company_id'          => $companyId,
            'make'                => $make,
            'model'               => $model,
            'device_type'         => $deviceType,
            'serial_number'       => trim((string) $this->request->getPost('serial_number')),
            'asset_tag'           => $assetTag,
            'status'              => $existing['status'] ?? 'ready',
            'service_manual_path' => $servicePath,
            'pm_manual_path'      => $pmPath,
            'photo_path'          => $photoPath,
        ];

        if ($id > 0) {
            // UPDATE existing record
            $this->model->update($id, $payload);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Equipment updated successfully.',
            ]);
        }

        // INSERT new record
        $this->model->insert($payload);
        $newId = $this->model->getInsertID();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Equipment added successfully.',
            'id'      => $newId,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // DELETE — soft-delete a master equipment record
    // POST admin/equipment-db/delete/:id
    // ─────────────────────────────────────────────────────────────────────
    public function deleteRecord($id)
    {
        $companyId = (int) session('company_id');

        $row = $this->model
            ->where('company_id', $companyId)
            ->find((int) $id);

        if (!$row) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Equipment not found.',
            ]);
        }

        $this->model->delete((int) $id); // soft-delete via useSoftDeletes

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Equipment deleted successfully.',
        ]);
    }
}
