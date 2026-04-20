<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderInvoiceItemModel extends Model
{
    protected $table         = 'work_order_invoice_items';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'work_order_id', 'company_id', 'document_type', 'item_type',
        'part_number', 'labor_code_id', 'part_labor_code', 'description',
        'qty', 'unit_cost', 'total_cost', 'sort_order',
        'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
