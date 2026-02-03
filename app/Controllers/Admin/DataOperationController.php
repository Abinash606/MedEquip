<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

/**
 * Dashboard controller for the Super Admin (company owner).
 */
class DataOperationController extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        // Load models to gather statistics
        $customerModel   = new \App\Models\CustomerModel();
        $siteModel       = new \App\Models\SiteModel();
        $equipmentModel  = new \App\Models\EquipmentModel();
        $inspectionModel = new \App\Models\InspectionModel();
        $workModel       = new \App\Models\WorkOrderModel();

        // DB connection
        $db = Database::connect();

        // Get last backup date & time
        $lastBackup = $db->table('system_backups')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        $data = [
            'customersCount'   => $customerModel->where('company_id', $companyId)->countAllResults(),
            'sitesCount'       => $siteModel->where('company_id', $companyId)->countAllResults(),
            'equipmentCount'   => $equipmentModel->where('company_id', $companyId)->countAllResults(),
            'inspectionsCount' => $inspectionModel->where('company_id', $companyId)->countAllResults(),
            'workOrdersCount'  => $workModel->where('company_id', $companyId)->countAllResults(),
            'lastBackup'       => $lastBackup,
        ];
        return view('admin/dataops/index', $data);
    }

    // 🔹 STEP 1: Generate Backup
    // public function generateBackup()
    // {
    //     $dbName = 'medequip_db';

    //     // 👉 required file name
    //     $fileName = 'tscameri_medequip.sql';

    //     $backupPath = WRITEPATH . 'backups/';

    //     if (!is_dir($backupPath)) {
    //         mkdir($backupPath, 0777, true);
    //     }

    //     $fullPath = $backupPath . $fileName;

    //     // ✅ Windows / XAMPP compatible mysqldump path
    //     $mysqldump = 'C:/xampp/mysql/bin/mysqldump.exe';

    //     // ⚠️ if your MySQL has password, add -pPASSWORD
    //     $command = "\"$mysqldump\" -u root $dbName > \"$fullPath\"";

    //     // $command = "\"$mysqldump\" -u root -pYOUR_PASSWORD $dbName > \"$fullPath\"";

    //     $output = [];
    //     $resultCode = null;

    //     exec($command, $output, $resultCode);

    //     if ($resultCode !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'Database backup failed'
    //         ]);
    //     }

    //     /* ✅ INSERT backup date & time */
    //     $db = Database::connect();
    //     $db->table('system_backups')->insert([
    //         'backup_type' => 'manual',
    //         'created_at'  => date('Y-m-d H:i:s')
    //     ]);

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'file'   => base_url('admin/data-operations/download-backup/' . $fileName),
    //         'time'   => date('d M Y, h:i A')
    //     ]);
    // }

    public function generateBackup()
    {
        // 🔹 LIVE DATABASE DETAILS
        $dbName = 'tscameri_medquip';
        $dbUser = 'tscameri_medquip';   // hosting username
        $dbPass = '1234';
        $dbHost = 'localhost';

        // 🔹 Backup file name
        $fileName = 'tscameri_medquip_' . date('Y-m-d_H-i-s') . '.sql';

        $backupPath = WRITEPATH . 'backups/';

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath . $fileName;

        // 🔹 LIVE SERVER mysqldump (Linux hosting)
        $mysqldump = '/usr/bin/mysqldump';

        $command = "$mysqldump -h $dbHost -u$dbUser -p$dbPass $dbName > $fullPath";

        $output = [];
        $resultCode = null;

        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Live database backup failed'
            ]);
        }

        // ✅ Save backup time
        $db = \Config\Database::connect();
        $db->table('system_backups')->insert([
            'backup_type' => 'manual',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'file'   => base_url('admin/data-operations/download-backup/' . $fileName)
        ]);
    }


    public function downloadBackup($file)
    {
        $path = WRITEPATH . 'backups/' . $file;

        if (!file_exists($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Backup file not found');
        }

        return $this->response->download($path, null)->setFileName($file);
    }
}
