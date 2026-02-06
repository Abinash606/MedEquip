<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\InspectionModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        $inspModel   = new InspectionModel();
        $companyId   = $this->session->get('company_id');

        // Get upcoming inspections (NOT completed, regardless of scheduled date)
        $upcomingInspections = $inspModel
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('completed_at IS NULL')
            ->orWhere('completed_at', '0000-00-00')
            ->orWhere('completed_at', '')
            ->groupEnd()
            ->orderBy('scheduled_at', 'ASC')
            ->findAll();


        // Join equipment and site data manually
        $equipmentModel = new \App\Models\EquipmentModel();
        $siteModel = new \App\Models\SiteModel();

        $data['upcomingInspections'] = [];
        foreach ($upcomingInspections as $inspection) {
            $equipment = $equipmentModel->find($inspection['equipment_id']);
            $site = $siteModel->find($inspection['site_id']);

            $inspection['make'] = $equipment['make'] ?? '';
            $inspection['model'] = $equipment['model'] ?? '';
            $inspection['device_type'] = $equipment['device_type'] ?? '';
            $inspection['site_name'] = $site['name'] ?? '';

            // Set a default status for display purposes
            if (empty($inspection['status'])) {
                $inspection['status'] = 'Scheduled';
            }

            $data['upcomingInspections'][] = $inspection;
        }


        $historyInspections = $inspModel
            ->where('company_id', $companyId)
            ->where('completed_at IS NOT NULL')
            ->where('completed_at !=', '0000-00-00')
            ->where('completed_at !=', '')
            ->orderBy('completed_at', 'DESC')
            ->limit(10)
            ->findAll();
        $data['inspectionHistory'] = [];
        foreach ($historyInspections as $inspection) {
            $equipment = $equipmentModel->find($inspection['equipment_id']);
            $site = $siteModel->find($inspection['site_id']);

            $inspection['make'] = $equipment['make'] ?? '';
            $inspection['model'] = $equipment['model'] ?? '';
            $inspection['device_type'] = $equipment['device_type'] ?? '';
            $inspection['site_name'] = $site['name'] ?? '';

            if (empty($inspection['status'])) {
                $inspection['status'] = 'Pass'; 
            }

            $data['inspectionHistory'][] = $inspection;
        }

        return view('customer/inspections/index', $data);
    }
}