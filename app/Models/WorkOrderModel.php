<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderModel extends Model
{
    protected $table      = 'work_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'site_id', 'equipment_id', 'title', 'description', 'status',
        'priority', 'assigned_to', 'created_by', 'start_date', 'end_date',
        'completed_at', 'group_id', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
}