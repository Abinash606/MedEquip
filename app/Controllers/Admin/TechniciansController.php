<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TechnicianModel;

class TechniciansController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        // Join technicians with users to display technician name and email
        $builder = \Config\Database::connect()->table('technicians');
        $technicians = $builder
            ->select('technicians.*, users.full_name as name, users.email as user_email')
            ->join('users', 'users.id = technicians.user_id')
            ->where('technicians.company_id', $companyId)
            ->where('users.deleted_at', null)
            ->get()
            ->getResultArray();
        $data['technicians'] = $technicians;
        return view('admin/technicians/index', $data);
    }
}