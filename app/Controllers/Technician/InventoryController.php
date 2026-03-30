<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class InventoryController extends BaseController
{
    public function index()
    {
        $companyId = (int) session('company_id');
        $db        = \Config\Database::connect();

        $inventory = $db->query("
            SELECT i.id, i.part_number, i.part_description, i.bin,
                   i.row_aisle, i.shelf, i.qty, i.total_value
            FROM inventory i
            ORDER BY i.part_description ASC
        ")->getResultArray();

        return view('technician/inventory/index', [
            'inventory' => $inventory,
        ]);
    }
}
