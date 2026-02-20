<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\EquipmentModel;
use App\Models\SiteModel;
use App\Models\UserModel;
use Dompdf\Dompdf;


class InspectionsController extends BaseController
{
    public function index()
    {
        $model = new InspectionModel();
        $companyId = $this->session->get('company_id');

        // Get all inspections with equipment and technician details
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type, e.asset_tag, e.department, e.location');
        $builder->join('equipment e', 'e.id = i.equipment_id', 'left');
        $builder->where('i.company_id', $companyId);
        $builder->groupBy('i.group_id'); // Add GROUP BY clause for group_id
        $builder->orderBy('i.created_at', 'DESC');

        $data['inspections'] = $builder->get()->getResultArray();

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

            // Generate a unique group ID for this inspection session
            $groupId = $this->request->getPost('group_id');
            if (empty($groupId)) {
                $groupId = 'INSP-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Get the inspection items array from the request
            $inspectionItems = json_decode($this->request->getPost('inspection_items'), true);
            
            if (empty($inspectionItems)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No inspection items found'
                ]);
            }

            // Insert each inspection in the group
            foreach ($inspectionItems as $item) {
                $equipmentId = $item['equipment_id'];
                
                // If asset not found, create new equipment record
                if (!empty($item['asset_not_found']) && $item['asset_not_found'] === '1') {
            $equipmentModel = new EquipmentModel();
            $newEquip = [
                'company_id'    => $companyId,
                        'site_id'       => $item['site_id'],
                        'asset_tag'     => 'NEW-' . strtoupper(uniqid()),
                        'make'          => $item['manufacturer'] ?? '',
                        'model'         => $item['model_name'] ?? '',
                        'serial_number' => $item['serial_number'] ?? '',
                        'device_type'   => $item['description'] ?? '',
                'status'        => 'Pending',
            ];
            $equipmentModel->insert($newEquip);
            $equipmentId = $equipmentModel->getInsertID();
        }

                // Build findings string
        $findings = '';
                if (!empty($item['asset_not_found']) && $item['asset_not_found'] === '1') {
            $findings = 'Asset not found in inventory. '
                        . 'Manufacturer: ' . ($item['manufacturer'] ?? '') . '; '
                        . 'Model: '        . ($item['model_name'] ?? '')    . '; '
                        . 'Description: '  . ($item['description'] ?? '')   . '; '
                        . 'Serial #: '     . ($item['serial_number'] ?? '');
        }

        $data = [
            'company_id'      => $companyId,
                    'site_id'         => $item['site_id'],
            'equipment_id'    => $equipmentId,
                    'group_id'        => $groupId,
                    'scheduled_at'    => $item['scheduled_at'] ?? date('Y-m-d'),
                    'status'          => $item['status'] ?? 'Pass',
                    'technician_id'   => $item['technician_id'] ?? null,
                    'completed_at'    => date('Y-m-d H:i:s'),
                    'next_due_date'   => $item['next_due_date'] ?? (function() use ($item) {
                        // Auto-calculate from pm_frequency if next_due_date not set
                        $pmFreq = $item['pm_frequency'] ?? '';
                        if ($pmFreq !== '' && preg_match('/^(\d+)/', $pmFreq, $m)) {
                            $months = (int) $m[1];
                            if ($months > 0) return date('Y-m-d', strtotime("+{$months} months"));
                        }
                        return null;
                    })(),
            'findings'        => $findings,
                    'notes'           => $item['notes'] ?? '',
                    'inspection_type' => $item['inspection_type'] ?? 'PM',
                    'pm_frequency'    => $item['pm_frequency'] ?? '',
                    'device_complete' => $item['device_complete'] ?? 'Yes',
                    'created_by'      => session('user_id'),
        ];

        $inspectionModel->insert($data);
            }

            // Return success response
        $isJsonRequest = strpos($this->request->getHeader('Content-Type'), 'application/json') !== false;

            if ($isJsonRequest || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Inspections saved successfully',
                    'group_id' => $groupId
                ]);
            }

            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
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

    public function delete($groupId)
    {
        $inspectionModel = new InspectionModel();
        // $inspectionModel->delete($id);
        $inspectionModel->where('group_id', $groupId)->delete();
        return redirect()->back();
    }

    // ── AJAX: Search equipment by serial number (exact match) ──
    // GET  /admin/inspections/searchBySerial?serial_number=XXX&site_id=YYY
    public function searchBySerial()
    {
        $companyId = (int) session('company_id');
        $serialNumber = $this->request->getGet('serial_number');
        $siteId = $this->request->getGet('site_id');

        if (empty($serialNumber)) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode(['found' => false]));
        }

        $equipmentModel = new EquipmentModel();

        $query = $equipmentModel->where('company_id', $companyId)
                                ->where('serial_number', $serialNumber);

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

    // ── AJAX: Live search equipment by model / make (LIKE, partial) ──
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
            ->groupStart()
            ->like('model', $keyword)
            ->orLike('make', $keyword)
            ->orLike('device_type', $keyword)
            ->groupEnd()
            ->select('id, asset_tag, make, model, serial_number, device_type, location, department')
            ->findAll(20);

        // De-duplicate by make+model
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

    // ── Get inspections by group ID ──
    public function getInspectionGroup($groupId)
    {
        $companyId = (int) session('company_id');
        $db = \Config\Database::connect();
        
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type, e.asset_tag, e.department, e.location');
        $builder->join('equipment e', 'e.id = i.equipment_id', 'left');
        $builder->where('i.company_id', $companyId);
        $builder->where('i.group_id', $groupId);
        $builder->orderBy('i.created_at', 'ASC');
        
        $inspections = $builder->get()->getResultArray();
        
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($inspections));
    }

    public function getByGroupId() {
            $groupId = $this->request->getVar('group_id');

            // Load the required models
            $inspectionModel = new InspectionModel();
            $equipmentModel = new EquipmentModel();
            $siteModel = new SiteModel();
            $userModel = new UserModel();

            // Query inspections based on group_id and join the necessary tables
            $inspections = $inspectionModel
                ->select('inspections.*, inspections.status as inspection_status, inspections.id as inspections_id, sites.name as customer_site, equipment.*, users.full_name as technician_name')
                ->join('sites', 'sites.id = inspections.site_id', 'left')
                ->join('equipment', 'equipment.id = inspections.equipment_id', 'left')
                ->join('users', 'users.id = inspections.technician_id', 'left')
                ->where('inspections.group_id', $groupId)
                ->findAll();

            if ($inspections) {
                // Return data as JSON response
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $inspections
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No inspections found for this group.'
                ]);
            }
        }


        /**
     * NEW METHOD: Get a single inspection by ID for editing
     * This returns the inspection data to populate the edit wizard
     */
    public function getInspectionById($id)
    {
        $companyId = (int) session('company_id');
        $inspectionModel = new InspectionModel();
        $equipmentModel = new EquipmentModel();
        
        // Get inspection with equipment details
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i');
        $builder->select('i.*, e.make, e.model, e.serial_number, e.device_type, e.asset_tag, e.department, e.location');
        $builder->join('equipment e', 'e.id = i.equipment_id', 'left');
        $builder->where('i.company_id', $companyId);
        $builder->where('i.id', $id);
        
        $inspection = $builder->get()->getRowArray();
        
        if ($inspection) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([
                    'success' => true,
                    'data' => $inspection
                ]));
        }
        
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode([
                'success' => false,
                'message' => 'Inspection not found'
            ]));
    }

    /**
     * Delete a single inspection record by ID (AJAX-friendly).
     * Falls back to group delete if the ID is not purely numeric.
     *
     * POST /admin/inspections/delete/{id}
     */
    public function deleteById($id)
    {
        $inspectionModel = new InspectionModel();
        $companyId = (int) session('company_id');

        if (is_numeric($id)) {
            // Delete the single inspection row
            $inspectionModel
                ->where('company_id', $companyId)
                ->where('id', (int) $id)
                ->delete();
        } else {
            // Treat as group_id
            $inspectionModel
                ->where('company_id', $companyId)
                ->where('group_id', $id)
                ->delete();
        }

        // Support both AJAX (JSON) and classic form POST (redirect)
        if ($this->request->isAJAX() || strpos((string)($this->request->getHeader('Accept') ?? ''), 'application/json') !== false) {
            return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
        }

        return redirect()->back();
    }

    /**
     * NEW METHOD: Update a single inspection
     * This handles the update after editing through the wizard
     */
    public function updateInspection()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $companyId    = (int) session('company_id');
        $inspectionId = (int) $this->request->getPost('inspection_id');

        if ($inspectionId === 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing inspection_id']);
        }

        $inspectionModel = new InspectionModel();
        $equipmentModel  = new EquipmentModel();

        // ── Resolve equipment_id ──────────────────────────────────────────────
        $equipmentId = (int) $this->request->getPost('equipment_id');

        // Handle "asset not found" new equipment creation
            if ($this->request->getPost('asset_not_found') === '1') {
            $siteId = (int) $this->request->getPost('site_id');
                $newEquip = [
                    'company_id'    => $companyId,
                'site_id'       => $siteId,
                'asset_tag'     => $this->request->getPost('asset_tag') ?: ('NEW-' . strtoupper(uniqid())),
                'make'          => (string)($this->request->getPost('manufacturer') ?? ''),
                'model'         => (string)($this->request->getPost('model_name')   ?? ''),
                'serial_number' => (string)($this->request->getPost('serial_number') ?? ''),
                'device_type'   => (string)($this->request->getPost('description')  ?? ''),
                'department'    => (string)($this->request->getPost('department')    ?? ''),
                'location'      => (string)($this->request->getPost('location')      ?? ''),
                'status'        => 'Pending',
                ];
                $equipmentModel->insert($newEquip);
            $equipmentId = (int) $equipmentModel->getInsertID();
            }
            
        // ── Update equipment table fields if they were sent ───────────────────
        $dept   = trim((string)($this->request->getPost('department')    ?? ''));
        $room   = trim((string)($this->request->getPost('location')      ?? ''));
        $serial = trim((string)($this->request->getPost('serial_number') ?? ''));

        if ($equipmentId > 0 && ($dept !== '' || $room !== '' || $serial !== '')) {
                $eqUpdate = [];
                if ($dept   !== '') $eqUpdate['department']    = $dept;
                if ($room   !== '') $eqUpdate['location']      = $room;
                if ($serial !== '') $eqUpdate['serial_number'] = $serial;
            $equipmentModel->where('company_id', $companyId)->where('id', $equipmentId)->set($eqUpdate)->update();
            }

        // ── Build inspection $data ONLY from fields actually posted ───────────
        // This prevents overwriting DB values with nulls when the edit form
        // only submits a subset of fields (e.g. the Edit Device modal).
        $data = [];

        $postMap = [
            'site_id'         => 'site_id',
            'equipment_id'    => null,          // handled separately below
            'scheduled_at'    => 'scheduled_at',
            'status'          => 'status',
            'technician_id'   => 'technician_id',
            'next_due_date'   => 'next_due_date',
            'notes'           => 'notes',
            'inspection_type' => 'inspection_type',
            'pm_frequency'    => 'pm_frequency',
            'device_complete' => 'device_complete',
            'est'             => 'est',
            'cal'             => 'cal',
        ];

        foreach ($postMap as $field => $postKey) {
            if ($postKey === null) continue;
            $val = $this->request->getPost($postKey);
            if ($val !== null) {   // only include if actually sent in POST
                $data[$field] = $val;
            }
        }

        // Always include equipment_id if we resolved one
        if ($equipmentId > 0) {
            $data['equipment_id'] = $equipmentId;
        }

        // ── Auto-calculate next_due_date from pm_frequency if not provided ────
        if (empty($data['next_due_date']) && !empty($data['pm_frequency'])) {
            preg_match('/^(\d+)/', $data['pm_frequency'], $m);
            $months = isset($m[1]) ? (int)$m[1] : 0;
            if ($months > 0) {
                $data['next_due_date'] = date('Y-m-d', strtotime("+{$months} months"));
            }
        }

        // ── Add findings for new-equipment case ───────────────────────────────
        if ($this->request->getPost('asset_not_found') === '1') {
            $data['findings'] = 'Asset updated/created. '
                . 'Manufacturer: ' . ($this->request->getPost('manufacturer')  ?? '') . '; '
                . 'Model: '        . ($this->request->getPost('model_name')    ?? '') . '; '
                . 'Description: '  . ($this->request->getPost('description')   ?? '') . '; '
                . 'Serial #: '     . ($this->request->getPost('serial_number') ?? '');
            }

        if (empty($data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No fields to update']);
        }

        // ── Perform update using CI4's update($id, $data) ──────────────────────
        // First verify this inspection belongs to this company
        $existing = $inspectionModel->where('company_id', $companyId)->find($inspectionId);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Inspection not found']);
        }

        try {
            $updated = $inspectionModel->update($inspectionId, $data);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ]);
        }

        if ($updated === false) {
            $errors = $inspectionModel->errors();
            $dbErr  = $inspectionModel->db->error();
            $msg    = !empty($errors) ? implode(', ', $errors)
                    : (!empty($dbErr['message']) ? $dbErr['message'] : 'Update failed');
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        // ── Respond ───────────────────────────────────────────────────────────
        $wantsJson = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'),       'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;

            if ($wantsJson) {
                return $this->response->setJSON([
                    'success'       => true,
                    'message'       => 'Inspection updated successfully',
                    'inspection_id' => $inspectionId,
                    'csrf_hash'     => csrf_hash(),
                ]);
            }

        $siteId = (int)($this->request->getPost('site_id') ?? 0);
        return redirect()->to('/admin/sites/' . $siteId);
    }


     /**
     * Provide a JSON payload containing the latest device and full history
     * rows for a given inspection group. This is the endpoint consumed by
     * openInspectionReport() in the view. The response includes the
     * `latest` device (the most recently created record in the group) and
     * all rows sorted from newest to oldest. The model handles the heavy
     * lifting of joining data across equipment, sites, and technicians.
     *
     * Route: GET /admin/inspections/reportData/{groupId}
     *
     * @param string $groupId The unique group identifier for the inspection
     */
    public function reportData($groupId = null)
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired',
            ]);
        }

        // Accept group_id from URL segment OR from query string (?group_id=...)
        if (empty($groupId)) {
            $groupId = $this->request->getGet('group_id');
        }

        if (empty($groupId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing group_id parameter',
            ]);
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

    /**
     * Generate a PDF report for a given inspection group. Uses Dompdf to
     * render the HTML template into a downloadable PDF file. The view
     * `admin/inspections/report_pdf.php` should be created alongside this
     * controller to define the markup and styling of the exported report.
     *
     * Route: GET /admin/inspections/reportPdf/{groupId}
     *
     * @param string $groupId The unique group identifier for the inspection
     */
    public function reportPdf($groupId)
    {
        $companyId = (int) session('company_id');
        $inspectionModel = new InspectionModel();

        $rows   = $inspectionModel->getReportRowsByGroup($companyId, $groupId);
        $latest = !empty($rows) ? $rows[0] : null;

        $html = view('admin/inspections/report_pdf', [
            'latest'   => $latest,
            'rows'     => $rows,
            'groupId'  => $groupId,
        ]);

        // Attempt to use Dompdf if it is installed. If not, fall back to an
        // HTML download so the feature does not hard-crash.
        if (class_exists('\Dompdf\Dompdf')) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="inspection-report-' . $groupId . '.pdf"')
            ->setBody($dompdf->output());
    }
    
        // Fallback: serve HTML as a downloadable file
        return $this->response
            ->setHeader('Content-Type', 'text/html')
            ->setHeader('Content-Disposition', 'attachment; filename="inspection-report-' . $groupId . '.html"')
            ->setBody($html);
    }
    

}