<?php

namespace App\Models;

use CodeIgniter\Model;

class LaborCodeModel extends Model
{
    protected $table         = 'labor_codes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'company_id', 'code', 'description', 'amount',
        'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $returnType     = 'array';
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
