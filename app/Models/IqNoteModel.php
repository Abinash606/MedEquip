<?php

namespace App\Models;

use CodeIgniter\Model;

class IqNoteModel extends Model
{
    protected $table      = 'iq_notes';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'note'
    ];

    protected $useTimestamps = true;
}
