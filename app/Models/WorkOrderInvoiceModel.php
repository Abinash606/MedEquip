<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderInvoiceModel extends Model
{
    protected $table         = 'work_order_invoices';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'work_order_id', 'company_id', 'problem_notes', 'invoice_note',
        'service_notes', 'status', 'signed_by', 'signature_image', 'signed_at',
        'tech_signed_by', 'tech_sig_image',
        'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
