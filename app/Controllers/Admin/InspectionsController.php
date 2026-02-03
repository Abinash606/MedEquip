<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\EquipmentModel;
use App\Models\SiteModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        $model = new InspectionModel();
        $companyId = $this->session->get('company_id');
        $data['inspections'] = $model->where('company_id', $companyId)->findAll();
        return view('admin/inspections/index', $data);
    }

    // public function create()
    // {
    //     if ($this->request->getMethod() === 'POST') {
    //         $companyId = (int) session('company_id');
    //         $inspectionModel = new InspectionModel();

    //         // ── If the asset was not found in site inventory,
    //         //    optionally create an equipment record first so we
    //         //    can link the inspection to it.
    //         $equipmentId = $this->request->getPost('equipment_id');

    //         if ($this->request->getPost('asset_not_found') === '1') {
    //             $equipmentModel = new EquipmentModel();
    //             $newEquip = [
    //                 'company_id'    => $companyId,
    //                 'site_id'       => $this->request->getPost('site_id'),
    //                 'asset_tag'     => 'NEW-' . strtoupper(uniqid()),   // auto-generate until user assigns one
    //                 'make'          => $this->request->getPost('manufacturer'),
    //                 'model'         => $this->request->getPost('model_name'),
    //                 'serial_number' => $this->request->getPost('serial_number'),
    //                 'device_type'   => $this->request->getPost('description'),
    //                 'status'        => 'Pending',
    //             ];
    //             $equipmentModel->insert($newEquip);
    //             $equipmentId = $equipmentModel->getInsertID();
    //         }

    //         // ── Build findings string from Step-2 data (for audit trail) ──
    //         $findings = '';
    //         if ($this->request->getPost('asset_not_found') === '1') {
    //             $findings = 'Asset not found in inventory. '
    //                 . 'Manufacturer: ' . $this->request->getPost('manufacturer') . '; '
    //                 . 'Model: '        . $this->request->getPost('model_name')    . '; '
    //                 . 'Description: '  . $this->request->getPost('description')   . '; '
    //                 . 'Serial #: '     . $this->request->getPost('serial_number');
    //         }

    //         $data = [
    //             'company_id'      => $companyId,
    //             'site_id'         => $this->request->getPost('site_id'),
    //             'equipment_id'    => $equipmentId,
    //             'scheduled_at'    => $this->request->getPost('scheduled_at'),
    //             'status'          => $this->request->getPost('status'),           // Pass | Fail | Repair
    //             'technician_id'   => $this->request->getPost('technician_id'),
    //             'completed_at'    => date('Y-m-d H:i:s'),                         // mark completed now
    //             'next_due_date'   => $this->request->getPost('next_due_date'),
    //             'findings'        => $findings,
    //             'notes'           => $this->request->getPost('notes'),
    //             'inspection_type' => $this->request->getPost('inspection_type'),
    //             'pm_frequency'    => $this->request->getPost('pm_frequency'),
    //             'device_complete' => $this->request->getPost('device_complete'),
    //         ];

    //         $inspectionModel->insert($data);

    //         // If submitted via classic form POST (non-AJAX), redirect.
    //         // AJAX calls from the wizard will get a 200 OK automatically.
    //         if (!$this->request->isJSON() && $this->request->getHeader('X-Requested-With') === null) {
    //             return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
    //         }

    //         // AJAX response — just return 200
    //         return $this->response->setStatusCode(200)->setBody('OK');
    //     }
    // }

    public function create()
{
    if ($this->request->getMethod() === 'POST') {
        $companyId = (int) session('company_id');
        $inspectionModel = new InspectionModel();

        // ── If the asset was not found in site inventory,
        //    optionally create an equipment record first so we
        //    can link the inspection to it.
        $equipmentId = $this->request->getPost('equipment_id');

        if ($this->request->getPost('asset_not_found') === '1') {
            $equipmentModel = new EquipmentModel();
            $newEquip = [
                'company_id'    => $companyId,
                'site_id'       => $this->request->getPost('site_id'),
                'asset_tag'     => 'NEW-' . strtoupper(uniqid()),   // auto-generate until user assigns one
                'make'          => $this->request->getPost('manufacturer'),
                'model'         => $this->request->getPost('model_name'),
                'serial_number' => $this->request->getPost('serial_number'),
                'device_type'   => $this->request->getPost('description'),
                'status'        => 'Pending',
            ];
            $equipmentModel->insert($newEquip);
            $equipmentId = $equipmentModel->getInsertID();
        }

        // ── Build findings string from Step-2 data (for audit trail) ──
        $findings = '';
        if ($this->request->getPost('asset_not_found') === '1') {
            $findings = 'Asset not found in inventory. '
                . 'Manufacturer: ' . $this->request->getPost('manufacturer') . '; '
                . 'Model: '        . $this->request->getPost('model_name')    . '; '
                . 'Description: '  . $this->request->getPost('description')   . '; '
                . 'Serial #: '     . $this->request->getPost('serial_number');
        }

        $data = [
            'company_id'      => $companyId,
            'site_id'         => $this->request->getPost('site_id'),
            'equipment_id'    => $equipmentId,
            'scheduled_at'    => $this->request->getPost('scheduled_at'),
            'status'          => $this->request->getPost('status'),           // Pass | Fail | Repair
            'technician_id'   => $this->request->getPost('technician_id'),
            'completed_at'    => date('Y-m-d H:i:s'),                         // mark completed now
            'next_due_date'   => $this->request->getPost('next_due_date'),
            'findings'        => $findings,
            'notes'           => $this->request->getPost('notes'),
            'inspection_type' => $this->request->getPost('inspection_type'),
            'pm_frequency'    => $this->request->getPost('pm_frequency'),
            'device_complete' => $this->request->getPost('device_complete'),
        ];

        $inspectionModel->insert($data);

        // If submitted via classic form POST (non-AJAX), redirect.
        // AJAX calls from the wizard will get a 200 OK automatically.
        $isJsonRequest = strpos($this->request->getHeader('Content-Type'), 'application/json') !== false;

        if (!$isJsonRequest && $this->request->getHeader('X-Requested-With') === null) {
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }

        // AJAX response — just return 200
        return $this->response->setStatusCode(200)->setBody('OK');
    }
}


    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $companyId = (int) session('company_id');
            $inspectionModel = new InspectionModel();
            $data = [
                'company_id'      => $companyId,
                'equipment_id'    => $this->request->getPost('equipment_id'),
                'scheduled_at'    => $this->request->getPost('scheduled_at'),
                'status'          => $this->request->getPost('status'),
                'technician_id'   => $this->request->getPost('technician_id'),
                'completed_at'    => $this->request->getPost('completed_at'),
                'next_due_date'   => $this->request->getPost('next_due_date'),
                'findings'        => $this->request->getPost('findings'),
                'notes'           => $this->request->getPost('notes'),
                'inspection_type' => $this->request->getPost('inspection_type'),
                'pm_frequency'    => $this->request->getPost('pm_frequency'),
                'device_complete' => $this->request->getPost('device_complete'),
            ];
            $inspectionModel->update($id, $data);
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $inspectionModel = new InspectionModel();
        $inspectionModel->delete($id);
        return redirect()->back();
    }

    // ── AJAX: Step 1 — search equipment by asset_tag (exact match) ──
    // GET  /admin/inspections/searchByAssetTag?asset_tag=XXX&site_id=YYY
    public function searchByAssetTag()
    {
        $companyId = (int) session('company_id');
        $assetTag  = $this->request->getGet('asset_tag');
        $siteId    = $this->request->getGet('site_id');

        if (empty($assetTag)) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode(['found' => false]));
        }

        $equipmentModel = new EquipmentModel();

        $query = $equipmentModel->where('company_id', $companyId)
                                ->where('asset_tag', $assetTag);

        // Optionally scope to the current site
        if (!empty($siteId)) {
            $query = $query->where('site_id', $siteId);
        }

        $equipment = $query->first();

        if ($equipment) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([
                    'found'         => true,
                    'id'            => $equipment['id'],
                    'asset_tag'     => $equipment['asset_tag'],
                    'make'          => $equipment['make'] ?? '',
                    'model'         => $equipment['model'] ?? '',
                    'serial_number' => $equipment['serial_number'] ?? '',
                    'device_type'   => $equipment['device_type'] ?? '',
                    'location'      => $equipment['location'] ?? '',
                    'department'    => $equipment['department'] ?? '',
                ]));
        }

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode(['found' => false]));
    }

    // ── AJAX: Step 2 — live search equipment by model / make (LIKE, partial) ──
    // GET  /admin/inspections/searchByModel?keyword=XXX
    public function searchByModel()
    {
        $companyId = (int) session('company_id');
        $keyword = $this->request->getGet('keyword');

        if (empty($keyword) || strlen($keyword) < 2) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([]));
        }

        $equipmentModel = new EquipmentModel();

        // Search across model, make, and device_type columns
        $results = $equipmentModel
            ->where('company_id', $companyId)
            ->groupStart()  // Start a group of conditions
            ->like('model', $keyword)
            ->orLike('make', $keyword)
            ->orLike('device_type', $keyword)
            ->groupEnd()  // End the group of conditions
            ->select('id, asset_tag, make, model, serial_number, device_type, location, department')
            ->findAll(20); // Limit to 20 results

        // De-duplicate by make+model so the dropdown stays clean
        $seen = [];
        $unique = [];
        foreach ($results as $row) {
            $key = strtolower(($row['make'] ?? '') . '|' . ($row['model'] ?? ''));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $row;
            }
        }

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($unique));
    }

}