<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\SiteModel;

class WorkOrdersController extends BaseController
{
    public function index()
    {
        $model = new WorkOrderModel();
        $companyId = $this->session->get('company_id');
        $data['workOrders'] = $model->where('company_id', $companyId)->findAll();
        return view('admin/work_orders/index', $data);
    }

     public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $companyId = $this->session->get('company_id');
            $workOrderModel = new WorkOrderModel();
            $data = [
                'company_id' => $companyId,
                'title' => $this->request->getPost('title'),
                'equipment_id' => $this->request->getPost('equipment_id'),
                'status' => $this->request->getPost('status'),
                'priority' => $this->request->getPost('priority'),
                'assigned_to' => $this->request->getPost('assigned_to'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'description' => $this->request->getPost('description'),
                'site_id' => $this->request->getPost('site_id')
            ];
            $workOrderModel->insert($data);
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $companyId = $this->session->get('company_id');
            $workOrderModel = new WorkOrderModel();
            $data = [
                'company_id' => $companyId,
                'title' => $this->request->getPost('title'),
                'equipment_id' => $this->request->getPost('equipment_id'),
                'status' => $this->request->getPost('status'),
                'priority' => $this->request->getPost('priority'),
                'assigned_to' => $this->request->getPost('assigned_to'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'description' => $this->request->getPost('description'),
            ];
            $workOrderModel->update($id, $data);
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $workOrderModel = new WorkOrderModel();
        $workOrderModel->delete($id);
        return redirect()->back();
    }

}