<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
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
        $dbName = 'tscameri_medquip';
        $dbUser = 'tscameri_medquip';
        $dbPass = 'T01g?^9usyS+';
        $dbHost = 'localhost';

        $fileName = 'tscameri_medquip_' . date('Y-m-d_H-i-s') . '.sql.gz';

        $backupPath = WRITEPATH . 'backups/';
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath . $fileName;

        $mysqldump = '/usr/bin/mysqldump';

        $command = "$mysqldump -h $dbHost -u$dbUser -p$dbPass $dbName | gzip > $fullPath";

        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Live database backup failed'
            ]);
        }

        \Config\Database::connect()
            ->table('system_backups')
            ->insert([
                'backup_type' => 'manual',
                'created_at'  => date('Y-m-d H:i:s')
            ]);

        $formattedTime = date('d M Y, h:i A');

        return $this->response->setJSON([
            'status' => 'success',
            'file'   => base_url('admin/data-operations/download-backup/' . $fileName),
            'time'   => $formattedTime
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


    // public function importEquipment()
    // {
    //     $file = $this->request->getFile('csv_file');

    //     if (!$file || !$file->isValid()) {
    //         return redirect()->back()->with('error', 'Invalid file');
    //     }

    //     if ($file->getExtension() !== 'csv') {
    //         return redirect()->back()->with('error', 'Only CSV allowed');
    //     }

    //     $equipmentModel = new EquipmentModel();

    //     $handle = fopen($file->getTempName(), 'r');

    //     // First row = header
    //     $header = fgetcsv($handle);

    //     while (($row = fgetcsv($handle)) !== false) {

    //         $data = array_combine($header, $row);

    //         $make        = trim($data['Manufacture'] ?? '');
    //         $model       = trim($data['Model'] ?? '');
    //         $deviceType  = trim($data['Equipment Description'] ?? '');

    //         if (!$make || !$model) {
    //             continue; // skip invalid row
    //         }

    //         // check existing record
    //         $existing = $equipmentModel
    //             ->where('make', $make)
    //             ->where('model', $model)
    //             ->first();

    //         if ($existing) {
    //             // UPDATE
    //             $equipmentModel->update($existing['id'], [
    //                 'device_type' => $deviceType,
    //                 'updated_at'  => date('Y-m-d H:i:s')
    //             ]);
    //         } else {
    //             // INSERT
    //             $equipmentModel->insert([
    //                 'make'        => $make,
    //                 'model'       => $model,
    //                 'device_type' => $deviceType,
    //                 'created_at'  => date('Y-m-d H:i:s')
    //             ]);
    //         }
    //     }

    //     fclose($handle);

    //     return redirect()->back()->with('success', 'CSV Imported Successfully');
    // }

    public function importEquipment()
    {
        $companyId = session()->get('company_id');

        if (!$companyId) {
            return redirect()->back()->with('error', 'Company not found');
        }

        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file');
        }

        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV allowed');
        }

        $equipmentModel = new EquipmentModel();
        $handle = fopen($file->getTempName(), 'r');

        // HEADER CLEAN
        $header = array_map(function ($h) {
            return trim(str_replace("\xEF\xBB\xBF", '', $h));
        }, fgetcsv($handle));

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_combine($header, $row);

            $make = trim(
                $data['Manufacture']
                    ?? $data['Manufacturer']
                    ?? ''
            );

            $model = trim($data['Model'] ?? '');

            $deviceType = trim(
                $data['Equipment Description']
                    ?? $data['Description']
                    ?? ''
            );

            if (!$make || !$model) {
                continue;
            }

            $existing = $equipmentModel
                ->where('company_id', $companyId)
                ->where('site_id', 1)
                ->where('make', $make)
                ->where('model', $model)
                ->where('deleted_at', null)
                ->first();

            if ($existing) {
                $equipmentModel->update($existing['id'], [
                    'device_type' => $deviceType,
                    'updated_at'  => date('Y-m-d H:i:s')
                ]);
            } else {
                $equipmentModel->insert([
                    'company_id'  => $companyId,
                    'site_id'     => 1,
                    'make'        => $make,
                    'model'       => $model,
                    'device_type' => $deviceType,
                ]);
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', 'CSV Imported Successfully');
    }
}
