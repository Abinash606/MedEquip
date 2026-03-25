<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use App\Models\SiteModel;
use App\Models\UsStateModel;
use CodeIgniter\Email\Email;


class CustomersController extends BaseController
{
    public function index()
    {
        $model = new CustomerModel();
        $siteModel     = new SiteModel();
        $stateModel = new UsStateModel();

        $companyId = $this->session->get('company_id');
        $customers = $model->where('company_id', $companyId)->findAll();
        // Get the number of sites for each customer
        foreach ($customers as &$customer) {
            $customer['site_count'] = $siteModel->where('customer_id', $customer['id'])->countAllResults();
        }

        unset($customer);
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

        $states = $stateModel->getAllStates();

        return view('admin/customers/index', ['customers' => $customers, 'states' => $states]);
    }

    /**
     * Store a new customer.
     * Validates input and creates a new customer along with credentials
     */
    // public function add()
    // {
    //     $model = new CustomerModel();
    //     $companyId = $this->session->get('company_id');

    //     $rules = [
    //         'name'             => 'required|max_length[255]',
    //         'contact_name'     => 'permit_empty|max_length[255]',
    //         'email'            => 'permit_empty|valid_email|max_length[255]',
    //         'phone'            => 'permit_empty|max_length[50]',
    //         'billing_address'  => 'permit_empty|max_length[255]',
    //         'billing_city'     => 'permit_empty|max_length[255]',
    //         'billing_state'    => 'permit_empty|max_length[255]',
    //         'billing_zip'      => 'permit_empty|max_length[20]',
    //         'fax'              => 'permit_empty|max_length[50]',
    //         'website'          => 'permit_empty|max_length[255]',
    //         'logo'             => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    //     }

    //     // Handle logo upload
    //     $logoPath = null;
    //     $logoFile = $this->request->getFile('logo');
    //     if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
    //         $newName = $logoFile->getRandomName();
    //         $logoFile->move(WRITEPATH . '../public/uploads/logos', $newName);
    //         $logoPath = $newName;
    //     }

    //     // Insert customer
    //     $customerData = [
    //         'company_id'      => $companyId,
    //         'name'            => $this->request->getPost('name'),
    //         'contact_name'    => $this->request->getPost('contact_name'),
    //         'email'           => $this->request->getPost('email'),
    //         'phone'           => $this->request->getPost('phone'),
    //         'billing_address' => $this->request->getPost('billing_address'),
    //         'billing_city'    => $this->request->getPost('billing_city'),
    //         'billing_state' => strtoupper(trim((string)$this->request->getPost('billing_state'))),

    //         'billing_zip'     => $this->request->getPost('billing_zip'),
    //         'fax'             => $this->request->getPost('fax'),
    //         'website'         => $this->request->getPost('website'),
    //         'logo_path'       => $logoPath,
    //         'created_by'      => $this->session->get('user_id'),
    //     ];

    //     $customerId = $model->insert($customerData);

    //     if (!$customerId) {
    //         return redirect()->back()->withInput()->with('error', 'Failed to create customer');
    //     }

    //     // Save credentials
    //     $portalUsernames = $this->request->getPost('portal_username');
    //     $portalEmails = $this->request->getPost('portal_email');
    //     $portalPasswords = $this->request->getPost('portal_password');

    //     if ($portalUsernames && is_array($portalUsernames)) {
    //         $credentials = [];
    //         foreach ($portalUsernames as $index => $username) {
    //             if (!empty($username) && !empty($portalEmails[$index]) && !empty($portalPasswords[$index])) {
    //                 $credentials[] = [
    //                     'username' => $username,
    //                     'email' => $portalEmails[$index],
    //                     'password' => $portalPasswords[$index]
    //                 ];
    //             }
    //         }

    //         if (!empty($credentials)) {
    //             $model->saveCredentials($customerId, $credentials);
    //         }
    //     }

    //     return redirect()->to('admin/customers')->with('success', 'Customer created successfully');
    // }


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

        // Get customer details
        $customerName = $this->request->getPost('name');
        $customerEmail = $this->request->getPost('email');
        $companyName = $customerName; // Assuming company name is the same as the customer's name

        // Send welcome email to the customer
        $this->sendCustomerWelcomeEmail($customerEmail, $companyName, $logoPath);
        // Insert customer data
        $customerData = [
            'company_id'      => $companyId,
            'name'            => $this->request->getPost('name'),
            'contact_name'    => $this->request->getPost('contact_name'),
            'email'           => $this->request->getPost('email'),
            'phone'           => $this->request->getPost('phone'),
            'billing_address' => $this->request->getPost('billing_address'),
            'billing_city'    => $this->request->getPost('billing_city'),
            'billing_state' => strtoupper(trim((string)$this->request->getPost('billing_state'))),
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

        // Save credentials and send email for each
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
            // Save all credentials at once (outside the loop)
            if (!empty($credentials)) {
                $model->saveCredentials($customerId, $credentials);

                // Now, for each credential, generate the reset token, update the token and expiry, and send the reset email
                foreach ($credentials as $credential) {
                    $resetToken = bin2hex(random_bytes(16)); // Random reset token
                    // Create reset link for customers - NEW URL
                    $resetLink = site_url('customer/reset-password/' . $resetToken);                    // Save the reset token in the credentials table
                    $this->savePasswordResetToken($credential['email'], $resetToken);

                    // Send email with the reset password link
                    $this->sendUserWelcomeEmail($credential['email'], $credential['username'], $resetLink, $companyName, $logoPath);
                }
            }
        }

        return redirect()->to('admin/customers')->with('success', 'Customer created successfully');
    }

    // Function to save the password reset token in the credentials table
    public function savePasswordResetToken($email, $resetToken)
    {
        // Use the database service to get a connection to the DB
        $db = \Config\Database::connect();


        // Update the user's reset token and expiration date
        $data = [
            'reset_token' => $resetToken,
            'reset_token_expiration' => date('Y-m-d H:i:s', strtotime('+1 hour'))  // Token expires in 1 hour
        ];

        // Perform the update query for each credential by email (or other identifier like username)
        $db->table('credentials')
            ->where('email', $email)  // Match by email or other unique identifier
            ->update($data);
    }

    // Function to send welcome email to the customer
    public function sendCustomerWelcomeEmail($customerEmail, $companyName, $companyLogo,)
    {
        // Prepare the subject and from details
        $fromEmail = '1easyecommerce@gmail.com';
        $fromName = esc($companyName);
        $subject = 'Welcome to Asset IQ';

        // Data for customer email
        $data = [
            'customer_name' => esc($companyName),
            'company_name' => esc($companyName),
            'company_logo' => $companyLogo  // Company logo path
        ];

        // Generate the email content by rendering the view template
        $message = view('emails/welcome_email', $data);

        // Headers for the email
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

        // Send the email
        if (mail($customerEmail, $subject, $message, $headers)) {
            echo 'email sent';
        } else {
            log_message('error', 'Failed to send customer welcome email to: ' . $customerEmail);
        }
    }



    // Function to send password reset email
    public function sendUserWelcomeEmail($userEmail, $username, $resetLink, $companyName, $companyLogo)
    {
        // Prepare the subject and from details
        $fromEmail = '1easyecommerce@gmail.com';
        $fromName = esc($companyName);
        $subject = 'Welcome to Asset IQ - Set Your Password';

        // Data for user email
        $data = [
            'username' => esc($username),
            'reset_link' => $resetLink,
            'company_name' => esc($companyName),
            'company_logo' => $companyLogo  // Company logo path
        ];

        // Generate the email content by rendering the view template
        $message = view('emails/welcome_user_email', $data);

        // Headers for the email
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

        // Send the email
        if (!mail($userEmail, $subject, $message, $headers)) {
            log_message('error', 'Failed to send user welcome email to: ' . $userEmail);
        }
    }



    // Function to send password reset email
    public function sendPasswordResetEmail($email, $username, $resetLink)
    {
        $emailService = \Config\Services::email();
        $emailService->setFrom('no-reply@company.com', 'Company Name');
        $emailService->setTo($email);
        $emailService->setSubject('Welcome to Asset IQ Set Your Password');

        // Data to be passed to the email template
        $data = [
            'customer_name' => $username,
            'reset_link' => $resetLink
        ];

        // Load the email template view
        $message = view('emails/welcome_email', $data);

        $emailService->setMessage($message);

        if (!$emailService->send()) {
            log_message('error', 'Failed to send welcome email to: ' . $email);
        }
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
            // 'billing_state'   => $this->request->getPost('billing_state'),
            'billing_state' => strtoupper(trim((string)$this->request->getPost('billing_state'))),

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
    public function checkEmail()
    {
        $email = $this->request->getPost('email');
        $customerId = $this->request->getPost('id');  // Get the customer ID being edited (if any)

        $model = new CustomerModel();

        // Check if there's any other customer with the same email, but not the current one
        $customer = $model->where('email', $email)
            ->where('id !=', $customerId)  // Ignore the current customer's ID
            ->first();

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

    public function filterSites($customerId)
    {
        session()->set('admin_site_customer_filter', (int)$customerId);
        return redirect()->to('admin/sites');
    }

}