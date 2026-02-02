<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\SiteModel;
use App\Models\EquipmentModel;
use App\Models\TechnicianModel;

class SchedulingController extends BaseController
{
    protected $workOrderModel;

    public function __construct()
    {
        $this->workOrderModel = new WorkOrderModel();
    }

    public function index()
    {
        $data['sites'] = (new SiteModel())->where('company_id', session()->get('company_id'))->findAll();
        $data['equipment'] = (new EquipmentModel())->where('company_id', session()->get('company_id'))->findAll();
        $data['technicians'] = (new TechnicianModel())->where('company_id', session()->get('company_id'))->findAll();
        
        return view('admin/scheduling/index', $data);
    }

    public function store()
    {
        $this->workOrderModel->save([
            'company_id' => session()->get('company_id'),
            'site_id' => $this->request->getPost('site_id'),
            'equipment_id' => $this->request->getPost('equipment_id') ?: 0,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'open',
            'assigned_to' => $this->request->getPost('assigned_to'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'created_by' => session()->get('id')
        ]);

        return redirect()->to('/admin/scheduling')->with('success', 'Work order created');
    }

    public function events()
    {
        $workOrders = $this->workOrderModel
            ->select('work_orders.*, sites.name as site_name')
            ->join('sites', 'sites.id = work_orders.site_id')
            ->where('work_orders.company_id', session()->get('company_id'))
            ->findAll();

        $events = [];
        foreach ($workOrders as $wo) {
            $events[] = [
                'id' => $wo['id'],
                'title' => $wo['title'] . ' (' . $wo['site_name'] . ')',
                'start' => $wo['start_date'],
                'end' => $wo['end_date'],
                'color' => $this->getPriorityColor($wo['priority']),
                'extendedProps' => [
                    'status' => $wo['status'],
                    'priority' => $wo['priority']
                ]
            ];
        }

        return $this->response->setJSON($events);
    }

    private function getPriorityColor($priority)
    {
        switch ($priority) {
            case 'high': return '#ef4444';
            case 'normal': return '#3b82f6';
            case 'low': return '#10b981';
            default: return '#6b7280';
        }
    }
}
