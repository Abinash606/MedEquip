<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table      = 'companies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'address', 'city', 'state', 'country', 'postal_code', 'phone', 'email',
        'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}