<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteModel;

class AssetsController extends BaseController
{
    public function index()
    {
        $siteModel = new SiteModel();
        $equipModel = new EquipmentModel();
        $companyId = $this->session->get('company_id');
        // Get all sites for the company
        $sites = $siteModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->findAll();

        // Get all equipment for these sites
        $siteIds = array_column($sites, 'id');
        $equipment = [];

        if (!empty($siteIds)) {
            $equipment = $equipModel
                ->whereIn('site_id', $siteIds)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            log_message('debug', 'Equipment found: ' . count($equipment));
        }

        $data['sites'] = $sites;
        $data['equipment'] = $equipment;

        return view('customer/assets/index', $data);
    }

    /**
     * Display the details of a site including equipment, inspections and work orders.
     */
    public function show(int $id)
    {
        $siteModel   = new SiteModel();
        $equipModel  = new \App\Models\EquipmentModel();
        $inspModel   = new \App\Models\InspectionModel();
        $workModel   = new \App\Models\WorkOrderModel();
        $companyId   = $this->session->get('company_id');
        $customerId  = $this->session->get('customer_id');
        $site = $siteModel->where('company_id', $companyId)
            ->where('customer_id', $customerId)
            ->where('id', $id)
            ->first();
        if (! $site) {
            return redirect()->to('/customer/sites');
        }
        $data['site']       = $site;
        $data['equipment']  = $equipModel->where('site_id', $id)->findAll();
        $data['inspections'] = $inspModel->where('site_id', $id)->findAll();
        $data['workOrders'] = $workModel->where('site_id', $id)->findAll();
        return view('customer/sites/show', $data);
    }
    public function store()
    {
        $equipModel = new EquipmentModel();
        $companyId = $this->session->get('company_id');

        // Backend validation
        $validation = \Config\Services::validation();

        $validation->setRules([
            'serial_number' => 'required|min_length[1]',
            'make' => 'required|min_length[2]',
            'model' => 'required|min_length[2]',
            'device_type' => 'required',
            'status' => 'required|in_list[ready,need_attention,out_of_service]',
        ], [
            'serial_number' => [
                'required' => 'Serial number is required.'
            ],
            'make' => [
                'required' => 'Make is required.',
                'min_length' => 'Make must be at least 2 characters.'
            ],
            'model' => [
                'required' => 'Model is required.',
                'min_length' => 'Model must be at least 2 characters.'
            ],
            'device_type' => [
                'required' => 'Device type is required.'
            ],
            'status' => [
                'required' => 'Status is required.',
                'in_list' => 'Invalid status. Use: ready, need_attention, out_of_service'
            ]
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            $errorMsg = implode(', ', $errors);
            return redirect()->back()->with('error', $errorMsg);
        }

        // Get asset tag from form or auto-generate
        $assetTag = trim($this->request->getPost('asset_tag'));

        if (empty($assetTag)) {
            // Auto-generate asset tag
            $assetTag = $this->generateAssetTag($companyId);
        } else {
            // Check if manually entered asset tag already exists
            $existing = $equipModel
                ->where('company_id', $companyId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Asset Tag already exists for this company');
            }
        }

        $data = [
            'company_id' => $companyId,
            'site_id' => $this->request->getPost('site_id'),
            'asset_tag' => $assetTag,
            'serial_number' => $this->request->getPost('serial_number'),
            'make' => $this->request->getPost('make'),
            'model' => $this->request->getPost('model'),
            'device_type' => $this->request->getPost('device_type'),
            'department' => $this->request->getPost('department'),
            'location' => $this->request->getPost('location'),
            'status' => (function($s) { $m = ['Operational'=>'ready','Needs Attention'=>'need_attention','Out of Service'=>'out_of_service']; return $m[$s] ?? strtolower(str_replace(' ','_',$s)) ?: 'ready'; })($this->request->getPost('status')),
        ];

        if ($equipModel->insert($data)) {
            return redirect()->to('/customer/assets')
                ->with('success', 'Equipment added successfully!');
        }

        return redirect()->back()
            ->with('error', 'Failed to add equipment. Please try again.');
    }

    /**
     * Generate auto-increment asset tag in format ASSET-1000, ASSET-1001, etc.
     */
    private function generateAssetTag(int $companyId): string
    {
        $equipModel = new EquipmentModel();

        $lastEquipment = $equipModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->like('asset_tag', 'ASSET-', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastEquipment && preg_match('/ASSET-(\d+)/', $lastEquipment['asset_tag'], $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1000;
        }

        return 'ASSET-' . $newNumber;
    }

    /**
     * Handle Report Issue form submission.
     * Auto-fills equipment details from the posted data and sends an email
     * notification to the company admin.
     */
    public function reportIssue()
    {
        $equipModel = new EquipmentModel();
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $username   = $this->session->get('username') ?? 'Customer';
        $db         = \Config\Database::connect();

        $equipmentId      = (int) $this->request->getPost('equipment_id');
        $issueDescription = trim((string) $this->request->getPost('issue_description'));
        $priority         = trim((string) $this->request->getPost('priority')) ?: 'normal';
        $assetTag         = trim((string) $this->request->getPost('asset_tag'));
        $make             = trim((string) $this->request->getPost('make'));
        $model            = trim((string) $this->request->getPost('model'));
        $serialNumber     = trim((string) $this->request->getPost('serial_number'));
        $deviceType       = trim((string) $this->request->getPost('device_type'));

        if ($issueDescription === '') {
            return redirect()->back()->with('error', 'Please describe the issue.');
        }

        // Map priority to work_orders enum: low|normal|high
        $priorityMap = ['low'=>'low','medium'=>'normal','normal'=>'normal','high'=>'high','critical'=>'high','urgent'=>'high'];
        $dbPriority  = $priorityMap[strtolower($priority)] ?? 'normal';

        // Resolve site_id (work_orders.site_id is NOT NULL)
        $siteId = 0;
        if ($equipmentId) {
            $eq = $db->query("SELECT site_id FROM equipment WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$equipmentId])->getRow();
            if ($eq) $siteId = (int)$eq->site_id;
        }
        if (!$siteId && $customerId) {
            $site = $db->query("SELECT id FROM sites WHERE customer_id = ? AND deleted_at IS NULL LIMIT 1", [$customerId])->getRow();
            if ($site) $siteId = (int)$site->id;
        }
        if (!$siteId) {
            $site = $db->query("SELECT id FROM sites WHERE company_id = ? AND deleted_at IS NULL LIMIT 1", [$companyId])->getRow();
            if ($site) $siteId = (int)$site->id;
        }

        // Get customer email from customers table
        $customerEmail = null;
        if ($customerId) {
            $cust = $db->query("SELECT email FROM customers WHERE id = ? LIMIT 1", [$customerId])->getRow();
            if ($cust && !empty($cust->email)) $customerEmail = $cust->email;
        }
        // Fallback: admin user email
        if (!$customerEmail) {
            $admin = $db->query(
                "SELECT email FROM users WHERE company_id = ? AND role_id = 1 AND deleted_at IS NULL ORDER BY id ASC LIMIT 1",
                [$companyId]
            )->getRow();
            if ($admin && !empty($admin->email)) $customerEmail = $admin->email;
        }

        // Send email to customer email address
        if ($customerEmail) {
            try {
                $email = \Config\Services::email();
                $email->setTo($customerEmail);
                $email->setSubject('[' . strtoupper($priority) . '] Equipment Issue Reported: ' . ($assetTag ?: 'Unknown'));
                $body  = "<h3>Equipment Issue Report</h3>";
                $body .= "<p><strong>Reported by:</strong> " . htmlspecialchars($username) . "</p>";
                $body .= "<p><strong>Priority:</strong> " . htmlspecialchars(ucfirst($priority)) . "</p><hr>";
                $body .= "<h4>Equipment Details</h4>";
                $body .= "<p><strong>Asset Tag:</strong> " . htmlspecialchars($assetTag) . "</p>";
                $body .= "<p><strong>Type:</strong> " . htmlspecialchars($deviceType) . "</p>";
                $body .= "<p><strong>Make/Model:</strong> " . htmlspecialchars($make . ' ' . $model) . "</p>";
                $body .= "<p><strong>S/N:</strong> " . htmlspecialchars($serialNumber) . "</p><hr>";
                $body .= "<h4>Issue Description</h4>";
                $body .= "<p>" . nl2br(htmlspecialchars($issueDescription)) . "</p>";
                $email->setMessage($body);
                $email->setMailType('html');
                $email->send();
            } catch (\Exception $e) {
                log_message('error', 'Report Issue email failed: ' . $e->getMessage());
            }
        }

        // Create work order — all NOT NULL columns provided
        try {
            $db->table('work_orders')->insert([
                'company_id'   => $companyId,
                'site_id'      => $siteId ?: 1,
                'equipment_id' => $equipmentId ?: 0,
                'title'        => 'Issue Reported: ' . ($assetTag ?: 'Unknown Asset'),
                'description'  => $issueDescription,
                'status'       => 'open',
                'priority'     => $dbPriority,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Report Issue WO insert failed: ' . $e->getMessage());
        }

        return redirect()->to('/customer/assets')
            ->with('success', 'Issue reported.' . ($customerEmail ? ' Email notification sent to ' . $customerEmail . '.' : ''));
    }


    /**
     * Bulk import equipment from an Excel/CSV file.
     * Expects columns: asset_tag, make, model, serial_number, device_type, department, room
     */
    public function bulkImport()
    {
        // Always return JSON for this endpoint
        $isAjax = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('X-Requested-With'), 'XMLHttpRequest') !== false;

        $companyId = (int) $this->session->get('company_id');
        $siteId    = (int) $this->request->getPost('site_id');

        if (!$siteId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a site before importing.',
            ]);
        }

        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid file uploaded. Please select a CSV file.',
            ]);
        }

        $ext = strtolower($file->getClientExtension());
        if ($ext !== 'csv') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only .csv files are accepted. Please save your Excel file as CSV first.',
            ]);
        }

        $tmpPath = $file->getTempName();
        $rows    = [];

        if (($handle = fopen($tmpPath, 'r')) !== false) {
            $headers = null;
            while (($line = fgetcsv($handle)) !== false) {
                if ($headers === null) {
                    $headers = array_map('strtolower', array_map('trim', $line));
                    continue;
                }
                if (!empty(array_filter($line))) {
                    $rows[] = array_combine(
                        $headers,
                        array_pad($line, count($headers), '')
                    );
                }
            }
            fclose($handle);
        }

        if (empty($rows)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No data rows found. Make sure Row 1 contains column headers.',
            ]);
        }

        $db       = \Config\Database::connect();
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            // Normalize keys
            $norm = [];
            foreach ($row as $k => $v) {
                $norm[trim(strtolower((string)$k))] = trim((string)$v);
            }

            $assetTag   = $norm['asset tag']       ?? $norm['asset_tag']     ?? $norm['asset #'] ?? '';
            $make       = $norm['make']             ?? $norm['manufacturer']  ?? '';
            $model      = $norm['model']            ?? $norm['model number']  ?? '';
            $serial     = $norm['serial number']    ?? $norm['serial_number'] ?? $norm['s/n']     ?? $norm['sn'] ?? '';
            $deviceType = $norm['device type']      ?? $norm['device_type']   ?? $norm['type']    ?? '';
            $department = $norm['department']       ?? $norm['dept']          ?? '';
            $location   = $norm['location or room'] ?? $norm['location']      ?? $norm['room']    ?? '';

            // Clean numeric values (Excel stores numbers without quotes)
            if (is_numeric($assetTag) && $assetTag !== '') $assetTag = (string)(int)$assetTag;
            if (is_numeric($serial)   && $serial   !== '') $serial   = (string)(int)$serial;
            if (strtoupper(trim($serial)) === 'N/A') $serial = '';

            // Skip completely empty rows
            if (empty($make) && empty($model)) {
                $skipped++;
                continue;
            }

            // Auto-generate asset tag if blank
            if (empty($assetTag)) {
                $lastTag = $db->query(
                    "SELECT asset_tag FROM equipment WHERE company_id = ? AND asset_tag LIKE 'ASSET-%' ORDER BY id DESC LIMIT 1",
                    [$companyId]
                )->getRow();
                $num = 1000;
                if ($lastTag && preg_match('/ASSET-(\d+)/', $lastTag->asset_tag, $m)) {
                    $num = (int)$m[1] + 1;
                }
                $assetTag = 'ASSET-' . $num;
            }

            // Duplicate check
            $dup = $db->query(
                "SELECT id FROM equipment WHERE company_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
                [$companyId, $assetTag]
            )->getRow();

            if ($dup) {
                $skipped++;
                continue;
            }

            try {
                $db->query(
                    "INSERT IGNORE INTO equipment
                 (company_id, site_id, asset_tag, make, model, serial_number,
                  device_type, department, location, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'ready', NOW(), NOW())",
                    [
                        $companyId,
                        $siteId,
                        $assetTag,
                        $make,
                        $model,
                        $serial,
                        $deviceType,
                        $department,
                        $location
                    ]
                );
                if ($db->affectedRows() > 0) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                log_message('warning', '[CustomerBulkImport] Row skipped: ' . $e->getMessage());
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