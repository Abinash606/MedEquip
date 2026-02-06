<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Dashboard controller for the Super Admin (company owner).
 */
class InventoryController extends BaseController
{
    protected $inventoryModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
    }

    public function index()
    {
        $companyId = $this->session->get('company_id');
        // Load models to gather statistics
        $customerModel   = new \App\Models\CustomerModel();
        $siteModel       = new \App\Models\SiteModel();
        $equipmentModel  = new \App\Models\EquipmentModel();
        $inspectionModel = new \App\Models\InspectionModel();
        $workModel       = new \App\Models\WorkOrderModel();

        $data = [
            'customersCount'   => $customerModel->where('company_id', $companyId)->countAllResults(),
            'sitesCount'       => $siteModel->where('company_id', $companyId)->countAllResults(),
            'equipmentCount'   => $equipmentModel->where('company_id', $companyId)->countAllResults(),
            'inspectionsCount' => $inspectionModel->where('company_id', $companyId)->countAllResults(),
            'workOrdersCount'  => $workModel->where('company_id', $companyId)->countAllResults(),
        ];
        return view('admin/inventory/index', $data);
    }

    /**
     * Get all inventory data for DataTables
     */
    public function data()
    {
        try {
            $items = $this->inventoryModel->findAll();

            $data = [];
            foreach ($items as $item) {
                $data[] = [
                    'id'               => $item['id'],
                    'part_number'      => $item['part_number'],
                    'part_description' => $item['part_description'],
                    'bin'              => $item['bin'],
                    'qty'              => $item['qty'],
                    'total_value'      => $item['total_value'],
                    'image'            => $item['image'] ? base_url('uploads/inventory/' . $item['image']) : null
                ];
            }

            return $this->response->setJSON(['data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['data' => []]);
        }
    }

    /**
     * Get single inventory item by ID
     */
    public function show($id)
    {
        $item = $this->inventoryModel->find($id);

        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Inventory item not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * Store new inventory item
     */
    /**
     * Store new inventory item - FIXED VERSION
     */
    /**
     * Store new inventory item - CORRECTED VERSION
     */
    public function store()
    {
        try {

            // Get POST data
            $postData = $this->request->getPost();

            // Prepare data array
            $data = [
                'part_number'      => $postData['part_number'] ?? null,
                'part_description' => $postData['part_description'] ?? null,
                'bin'              => $postData['bin'] ?? null,
                'row_aisle'        => $postData['row_aisle'] ?? null,
                'shelf'            => $postData['shelf'] ?? null,
                'qty'              => $postData['qty'] ?? null,
                'total_value'      => $postData['total_value'] ?? null,
            ];


            // Handle image upload if exists
            $file = $this->request->getFile('image');

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Create directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/inventory';

                if (!is_dir($uploadPath)) {
                    log_message('info', 'Creating upload directory');
                    if (!mkdir($uploadPath, 0755, true)) {
                        return $this->response->setStatusCode(500)->setJSON([
                            'success' => false,
                            'message' => 'Failed to create upload directory'
                        ]);
                    }
                }

                // Check if directory is writable
                if (!is_writable($uploadPath)) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => 'Upload directory is not writable. Please check permissions.'
                    ]);
                }

                $newName = $file->getRandomName();

                if ($file->move($uploadPath, $newName)) {
                    $data['image'] = $newName;
                } else {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => 'Failed to upload image: ' . $file->getErrorString()
                    ]);
                }
            }


            // Insert data - skip validation to avoid the temp file issue
            if (!$this->inventoryModel->insert($data, false)) {

                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to save inventory item'
                ]);
            }

            $insertId = $this->inventoryModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inventory item added successfully',
                'id'      => $insertId
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'debug'   => ENVIRONMENT === 'development' ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ] : null
            ]);
        }
    }

    /**
     * Update existing inventory item
     */
    public function update($id)
    {
        try {
            $item = $this->inventoryModel->find($id);

            if (!$item) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Inventory item not found'
                ]);
            }

            // Get POST data
            $postData = $this->request->getPost();

            $data = [
                'part_number'      => $postData['part_number'] ?? $item['part_number'],
                'part_description' => $postData['part_description'] ?? $item['part_description'],
                'bin'              => $postData['bin'] ?? $item['bin'],
                'row_aisle'        => $postData['row_aisle'] ?? $item['row_aisle'],
                'shelf'            => $postData['shelf'] ?? $item['shelf'],
                'qty'              => $postData['qty'] ?? $item['qty'],
                'total_value'      => $postData['total_value'] ?? $item['total_value'],
            ];

            // Handle image update
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && !$file->hasMoved()) {

                // Create directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/inventory';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newName = $file->getRandomName();
                if ($file->move($uploadPath, $newName)) {
                    $data['image'] = $newName;

                    // Delete old image if exists
                    if ($item['image'] && file_exists(FCPATH . 'uploads/inventory/' . $item['image'])) {
                        @unlink(FCPATH . 'uploads/inventory/' . $item['image']);
                    }
                } else {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => 'Failed to upload new image: ' . $file->getErrorString()
                    ]);
                }
            }


            if (!$this->inventoryModel->update($id, $data)) {
                $errors = $this->inventoryModel->errors();

                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Failed to update inventory item',
                        'errors'  => $errors
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inventory item updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete inventory item
     */
    public function delete($id)
    {
        try {
            $item = $this->inventoryModel->find($id);

            if (!$item) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Inventory item not found'
                ]);
            }

            // Delete image file if exists
            if ($item['image'] && file_exists(FCPATH . 'uploads/inventory/' . $item['image'])) {
                @unlink(FCPATH . 'uploads/inventory/' . $item['image']);
            }

            if (!$this->inventoryModel->delete($id)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete inventory item'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inventory item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
