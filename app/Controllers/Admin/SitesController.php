<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiteModel;
use App\Models\CustomerModel;
use App\Models\EquipmentModel;
use App\Models\InspectionModel;
use App\Models\WorkOrderModel;
use App\Models\TechnicianModel;


class SitesController extends BaseController
{
    /**
     * Display list of sites
     */
    public function index()
    {
        $siteModel = new SiteModel();
        $customerModel = new CustomerModel();
        $companyId = $this->session->get('company_id');
        
        $data['sites'] = $siteModel->where('company_id', $companyId)->findAll();
        $data['customers'] = $customerModel->where('company_id', $companyId)->findAll();
        
        return view('admin/sites/index', $data);
    }

    /**
     * Add a new site
     */
    public function add()
    {
        $siteModel = new SiteModel();
        $companyId = $this->session->get('company_id');
        
        $rules = [
            'name'        => 'required|max_length[255]',
            'customer_id' => 'required|integer',
            'address'     => 'permit_empty|max_length[255]',
            'city'        => 'permit_empty|max_length[100]',
            'state'       => 'permit_empty|max_length[50]',
            'zip'         => 'permit_empty|max_length[20]',
            'contact_name'=> 'permit_empty|max_length[255]',
            'email'       => 'permit_empty|valid_email|max_length[255]',
            'phone'       => 'permit_empty|max_length[50]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $siteData = [
            'company_id'   => $companyId,
            'customer_id'  => $this->request->getPost('customer_id'),
            'name'         => $this->request->getPost('name'),
            'address'      => $this->request->getPost('address'),
            'city'         => $this->request->getPost('city'),
            'state'        => $this->request->getPost('state'),
            'zip'          => $this->request->getPost('zip'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
        ];
        
        $siteModel->insert($siteData);
        
        return redirect()->to('admin/sites')->with('success', 'Site added successfully');
    }

    /**
     * Update an existing site
     * @param int $id
     */
    public function update($id)
    {
        $model = new SiteModel();
        
        // Validate input
        $rules = [
            'name' => 'required|max_length[255]',
            'customer_id' => 'required',
            'address' => 'permit_empty|max_length[255]',
            'city' => 'permit_empty|max_length[255]',
            'state' => 'permit_empty|max_length[255]',
            'zip' => 'permit_empty|max_length[20]',
            'contact_name' => 'permit_empty|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[50]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get the existing site data
        $siteData = [
            'name' => $this->request->getPost('name'),
            'customer_id' => $this->request->getPost('customer_id'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'zip' => $this->request->getPost('zip'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];
        
        // Update the site
        $model->update($id, $siteData);
        
        return redirect()->to('admin/sites')->with('success', 'Site updated successfully');
    }


    /**
     * Delete a site
     * @param int $id
     */
    public function delete($id)
    {
        $siteModel = new SiteModel();
        $companyId = $this->session->get('company_id');
        
        $site = $siteModel->where('company_id', $companyId)->find($id);
        if (!$site) {
            return redirect()->to('admin/sites')->with('error', 'Site not found');
        }
        
        $siteModel->delete($id);
        
        return redirect()->to('admin/sites')->with('success', 'Site deleted successfully');
    }

    /**
     * View site details with equipment, inspections, and work orders
     * @param int $id
     */
    public function view($id)
    {
        $siteModel = new SiteModel();
        $customerModel = new CustomerModel();
        $equipmentModel = new EquipmentModel();
        $inspectionModel = new InspectionModel();
        $workOrderModel = new WorkOrderModel();
        $technicianModel = new TechnicianModel(); // Add TechnicianModel

        
        $companyId = $this->session->get('company_id');
        
        // Get site details
        $site = $siteModel->where('company_id', $companyId)->find($id);
        if (!$site) {
            return redirect()->to('admin/sites')->with('error', 'Site not found');
        }
        
        // Get customer details
        $customer = $customerModel->find($site['customer_id']);
        
        // Get equipment for this site
        $equipment = $equipmentModel->where('site_id', $id)
                                     ->where('company_id', $companyId)
                                     ->findAll();
        
        // Get inspections for this site
        $inspections = $inspectionModel->select('inspections.*, users.full_name as technician_name, equipment.asset_tag')
            ->join('users', 'users.id = inspections.technician_id', 'left')
            ->join('equipment', 'equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id', $id)
            ->where('inspections.company_id', $companyId)
            ->groupBy('inspections.group_id')  // Group by group_id
            ->findAll();
 

        
        // Get work orders for this site
        $workOrders = $workOrderModel->select('work_orders.*, users.full_name as assigned_to_name, equipment.asset_tag')
                                     ->join('users', 'users.id = work_orders.assigned_to', 'left')
                                     ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
                                     ->where('work_orders.site_id', $id)
                                     ->where('work_orders.company_id', $companyId)
                                     ->findAll();

                                     // Fetch all technicians
        $technicians = $technicianModel
        ->select('technicians.*, users.full_name as full_name')  // Select technician fields and join user full name
        ->join('users', 'users.id = technicians.user_id', 'left')  // Adjust 'user_id' based on your actual schema
        ->where('technicians.company_id', $companyId)  // Filter by company ID
        ->findAll();  // Fetch all technicians

        
        $data = [
            'site'        => $site,
            'customer'    => $customer,
            'equipment'   => $equipment,
            'inspections' => $inspections,
            'workOrders'  => $workOrders,
            'technicians' => $technicians, // Add technicians to the data array
            'users' => $technicians, // Add technicians to the data array

        ];
        
        return view('admin/sites/details', $data);
    }
}   