<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use App\Models\SiteModel;

class CustomersController extends BaseController
{
    public function index()
    {
        $model = new CustomerModel();
		$siteModel     = new SiteModel();

        $companyId = $this->session->get('company_id');
        $customers = $model->where('company_id', $companyId)->findAll();
        
        // Get credentials for each customer to display in modal
        foreach ($customers as &$customer) {
            $customer['credentials'] = $model->getCredentialsByCustomerId($customer['id']);
        }
		unset($customer);

		// attach first site id (if any)
		$customerIds = array_column($customers, 'id');
		$firstSiteMap = $siteModel->getFirstSiteIdByCustomerIds($customerIds, $companyId);

		foreach ($customers as &$customer) {
			$cid = (int) $customer['id'];
			$customer['first_site_id'] = $firstSiteMap[$cid] ?? null; // null means no site
		}
		unset($customer);
        
        return view('admin/customers/index', ['customers' => $customers]);
    }

    /**
     * Store a new customer.
     * Validates input and creates a new customer along with credentials
     */
    public function add()
    {
        $model = new CustomerModel();
        $companyId = $this->session->get('company_id');
        
        $rules = [
            'name'             => 'required|max_length[255]',
            'contact_name'     => 'permit_empty|max_length[255]',
            'email'            => 'permit_empty|valid_email|max_length[255]',
            'phone'            => 'permit_empty|max_length[50]',
            'billing_address'  => 'permit_empty|max_length[255]',
            'billing_city'     => 'permit_empty|max_length[255]',
            'billing_state'    => 'permit_empty|max_length[255]',
            'billing_zip'      => 'permit_empty|max_length[20]',
            'fax'              => 'permit_empty|max_length[50]',
            'website'          => 'permit_empty|max_length[255]',
            'logo'             => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Handle logo upload
        $logoPath = null;
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $newName = $logoFile->getRandomName();
            $logoFile->move(WRITEPATH . '../public/uploads/logos', $newName);
            $logoPath = $newName;
        }
        
        // Insert customer
        $customerData = [
            'company_id'      => $companyId,
            'name'            => $this->request->getPost('name'),
            'contact_name'    => $this->request->getPost('contact_name'),
            'email'           => $this->request->getPost('email'),
            'phone'           => $this->request->getPost('phone'),
            'billing_address' => $this->request->getPost('billing_address'),
            'billing_city'    => $this->request->getPost('billing_city'),
            'billing_state'   => $this->request->getPost('billing_state'),
            'billing_zip'     => $this->request->getPost('billing_zip'),
            'fax'             => $this->request->getPost('fax'),
            'website'         => $this->request->getPost('website'),
            'logo_path'       => $logoPath,
            'created_by'      => $this->session->get('user_id'),
        ];
        
        $customerId = $model->insert($customerData);
        
        if (!$customerId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create customer');
        }
        
        // Save credentials
        $portalUsernames = $this->request->getPost('portal_username');
        $portalEmails = $this->request->getPost('portal_email');
        $portalPasswords = $this->request->getPost('portal_password');
        
        if ($portalUsernames && is_array($portalUsernames)) {
            $credentials = [];
            foreach ($portalUsernames as $index => $username) {
                if (!empty($username) && !empty($portalEmails[$index]) && !empty($portalPasswords[$index])) {
                    $credentials[] = [
                        'username' => $username,
                        'email' => $portalEmails[$index],
                        'password' => $portalPasswords[$index]
                    ];
                }
            }
            
            if (!empty($credentials)) {
                $model->saveCredentials($customerId, $credentials);
            }
        }
        
        return redirect()->to('admin/customers')->with('success', 'Customer created successfully');
    }

    /**
     * Show edit form - This is used if you want a separate edit page
     * @param int $id
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('admin/customers')->with('error', 'Customer ID is required');
        }
        
        $model = new CustomerModel();
        $companyId = $this->session->get('company_id');
        $customer = $model->where('company_id', $companyId)->find($id);
        
        if (!$customer) {
            return redirect()->to('admin/customers')->with('error', 'Customer not found');
        }
        
        // Get credentials
        $credentials = $model->getCredentialsByCustomerId($id);
        
        $data = [
            'customer' => $customer,
            'credentials' => $credentials
        ];
        
        return view('admin/customers/index', $data);
    }

    /**
     * Update customer and credentials.
     * @param int $id
     */
    public function update($id)
    {
        $model = new CustomerModel();
        $companyId = $this->session->get('company_id');
        $customer = $model->where('company_id', $companyId)->find($id);
        
        if (!$customer) {
            return redirect()->to('admin/customers')->with('error', 'Customer not found');
        }
        
        $rules = [
            'name'            => 'required|max_length[255]',
            'contact_name'    => 'permit_empty|max_length[255]',
            'email'           => 'permit_empty|valid_email|max_length[255]',
            'phone'           => 'permit_empty|max_length[50]',
            'billing_address' => 'permit_empty|max_length[255]',
            'billing_city'    => 'permit_empty|max_length[255]',
            'billing_state'   => 'permit_empty|max_length[255]',
            'billing_zip'     => 'permit_empty|max_length[20]',
            'fax'             => 'permit_empty|max_length[50]',
            'website'         => 'permit_empty|max_length[255]',
            'logo'            => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Handle logo upload
        $logoPath = $customer['logo_path']; // Keep existing logo by default
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Delete old logo if exists
            if ($customer['logo_path'] && file_exists(WRITEPATH . '../public/uploads/logos/' . $customer['logo_path'])) {
                unlink(WRITEPATH . '../public/uploads/logos/' . $customer['logo_path']);
            }
            
            $newName = $logoFile->getRandomName();
            $logoFile->move(WRITEPATH . '../public/uploads/logos', $newName);
            $logoPath = $newName;
        }
        
        $customerData = [
            'name'            => $this->request->getPost('name'),
            'contact_name'    => $this->request->getPost('contact_name'),
            'email'           => $this->request->getPost('email'),
            'phone'           => $this->request->getPost('phone'),
            'billing_address' => $this->request->getPost('billing_address'),
            'billing_city'    => $this->request->getPost('billing_city'),
            'billing_state'   => $this->request->getPost('billing_state'),
            'billing_zip'     => $this->request->getPost('billing_zip'),
            'fax'             => $this->request->getPost('fax'),
            'website'         => $this->request->getPost('website'),
            'logo_path'       => $logoPath,
        ];
        
        $model->update($id, $customerData);
        
        // Update credentials - delete old ones and insert new
        $model->deleteCredentials($id);
        
        $portalUsernames = $this->request->getPost('portal_username');
        $portalEmails = $this->request->getPost('portal_email');
        $portalPasswords = $this->request->getPost('portal_password');
        
        if ($portalUsernames && is_array($portalUsernames)) {
            $credentials = [];
            foreach ($portalUsernames as $index => $username) {
                if (!empty($username) && !empty($portalEmails[$index]) && !empty($portalPasswords[$index])) {
                    $credentials[] = [
                        'username' => $username,
                        'email' => $portalEmails[$index],
                        'password' => $portalPasswords[$index]
                    ];
                }
            }
            
            if (!empty($credentials)) {
                $model->saveCredentials($id, $credentials);
            }
        }
        
        return redirect()->to('admin/customers')->with('success', 'Customer updated successfully');
    }

    /**
     * Delete customer and associated credentials.
     * @param int $id
     */
    public function delete($id)
    {
        $model = new CustomerModel();
        $companyId = $this->session->get('company_id');
        $customer = $model->where('company_id', $companyId)->find($id);
        
        if (!$customer) {
            return redirect()->to('admin/customers')->with('error', 'Customer not found');
        }
        
        // Delete logo if exists
        if ($customer['logo_path'] && file_exists(WRITEPATH . '../public/uploads/logos/' . $customer['logo_path'])) {
            unlink(WRITEPATH . '../public/uploads/logos/' . $customer['logo_path']);
        }
        
        // Delete credentials
        $model->deleteCredentials($id);
        
        // Delete customer (soft delete if enabled)
        $model->delete($id);
        
        return redirect()->to('admin/customers')->with('success', 'Customer deleted successfully');
    }
	
	// Example in CodeIgniter controller
public function checkEmail() {
    $email = $this->request->getPost('email');
	$model = new CustomerModel();
    $customer = $model->where('email', $email)->first();
    if ($customer) {
        return $this->response->setJSON(['unique' => 'false']);
    } else {
        return $this->response->setJSON(['unique' => 'true']);
    }
}

// Method to fetch customer data for editing
    public function getCustomerData($id)
    {
        // Load the Customer model
        $customerModel = new CustomerModel();

        // Find customer by ID
        $customer = $customerModel->find($id);
		
		// Get credentials
        $credentials = $customerModel->getCredentialsByCustomerId($id);
        

        // Check if customer data exists
        if ($customer) {
            // Send response with customer data
            return $this->response->setJSON([
                'success' => true,
                'data' => $customer,
				'credentials' => $credentials,
				'image_url' => base_url('uploads/logos/' . $customer['logo_path']) 
            ]);
        } else {
            // If no customer found, send error response
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Customer not found.'
            ]);
        }
    }
	
	public function search()
    {
        $searchTerm = $this->request->getGet('search'); // Get the search term from the request

        // Load the Customer model
        $customerModel = new CustomerModel();

        // Search for customers by name or address
        $customers = $customerModel->like('name', $searchTerm)
                                   ->orLike('billing_address', $searchTerm)
                                   ->findAll();

        // Return response as JSON
        return $this->response->setJSON([
            'success' => true,
            'customers' => $customers
        ]);
    }

}