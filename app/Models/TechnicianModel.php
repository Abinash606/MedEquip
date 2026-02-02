<?php

namespace App\Models;

use CodeIgniter\Model;

class TechnicianModel extends Model
{
    protected $table      = 'technicians';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'user_id', 'specialization', 'phone', 'email',
        'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
}