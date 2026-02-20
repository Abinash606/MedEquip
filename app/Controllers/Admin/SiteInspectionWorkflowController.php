<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\InspectionModel;

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
    public function getEquipment()
    {
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');
        $companyId = (int) session('company_id');

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
            'make'          => $eq['make'] ?? '',
            'model'         => $eq['model'] ?? '',
            'device_type'   => $eq['device_type'] ?? '',
            'serial_number' => $eq['serial_number'] ?? '',
            'department'    => $eq['department'] ?? '',
            'location'      => $eq['location'] ?? '',
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
        // Ensure this route is only hit via POST
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method',
            ]);
        }

        $companyId = (int) session('company_id');
        $userId    = (int) session('user_id');
        $siteId    = (int) $this->request->getPost('site_id');
        $assetTag  = trim((string) $this->request->getPost('asset_tag'));
        $result    = trim((string) $this->request->getPost('result'));
        $notes     = trim((string) $this->request->getPost('notes'));
        $dept      = trim((string) $this->request->getPost('department'));
        $room      = trim((string) $this->request->getPost('room'));
        $serial    = trim((string) $this->request->getPost('serial_number'));
        $action    = trim((string) $this->request->getPost('action_performed'));
        $pmFreq    = trim((string) $this->request->getPost('pm_frequency'));

        // Basic validation
        if ($assetTag === '' || $siteId === 0 || $result === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields',
            ]);
        }

        // Lookup equipment by asset tag
        $equipmentModel = new EquipmentModel();
        $equipment = $equipmentModel
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('asset_tag', $assetTag)
            ->first();

        if (!$equipment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Equipment not found',
            ]);
        }

        $equipmentId = (int) $equipment['id'];

        // Update equipment details if provided
        $updateFields = [];
        if ($dept !== '') {
            $updateFields['department'] = $dept;
        }
        if ($room !== '') {
            $updateFields['location'] = $room;
        }
        if ($serial !== '') {
            $updateFields['serial_number'] = $serial;
        }
        if (!empty($updateFields)) {
            $equipmentModel->update($equipmentId, $updateFields);
        }

        // Build inspection record
        $inspectionModel = new InspectionModel();
        $groupId = 'INSP-' . date('YmdHis');
        $now     = date('Y-m-d H:i:s');
        // Calculate next_due_date from pm_frequency (e.g. "12 Month", "6 Month", "3 Month", "1 Month")
        $nextDueDate = null;
        if ($pmFreq !== '') {
            preg_match('/^(\d+)/', $pmFreq, $m);
            $months = isset($m[1]) ? (int) $m[1] : 0;
            if ($months > 0) {
                $nextDueDate = date('Y-m-d', strtotime("+{$months} months"));
            }
        }

        $insData = [
            'company_id'      => $companyId,
            'site_id'         => $siteId,
            'equipment_id'    => $equipmentId,
            'group_id'        => $groupId,
            'scheduled_at'    => $now,
            'completed_at'    => $now,
            'status'          => $result,
            'technician_id'   => $userId > 0 ? $userId : null,
            'findings'        => '',
            'notes'           => $notes,
            'inspection_type' => $action,
            'pm_frequency'    => $pmFreq,
            'next_due_date'   => $nextDueDate,
            'device_complete' => 'Yes',
            'created_by'      => $userId > 0 ? $userId : null,
        ];
        $inspectionModel->insert($insData);

        // Update equipment status based on result
        if ($result === 'Pass') {
            $equipmentModel->update($equipmentId, ['status' => 'Ready']);
        } elseif ($result === 'Fail') {
            $equipmentModel->update($equipmentId, ['status' => 'Needs Attention']);
        } elseif ($result === 'Repair') {
            $equipmentModel->update($equipmentId, ['status' => 'Repair']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Inspection recorded successfully',
        ]);
    }
}