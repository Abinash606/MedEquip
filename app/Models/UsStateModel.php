<?php

namespace App\Models;

use CodeIgniter\Model;

class UsStateModel extends Model
{
    protected $table            = 'us_states';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'code',
        'name',
    ];

    // Optional timestamps (enable only if columns exist)
    protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
}
