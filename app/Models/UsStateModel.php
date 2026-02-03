<?php

namespace App\Models;

use CodeIgniter\Model;

class UsStateModel extends Model
{
    protected $table      = 'us_states';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    public function getAllStates(): array
    {
        return $this->select('code, name')->orderBy('name', 'ASC')->findAll();
    }
}
