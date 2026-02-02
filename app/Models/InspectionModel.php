<?php

namespace App\Models;

use CodeIgniter\Model;

class InspectionModel extends Model
{
    protected $table      = 'inspections';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'site_id', 'equipment_id', 'scheduled_at', 'completed_at',
        'status', 'technician_id', 'findings', 'notes', 'next_due_date',
        'created_by', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
}