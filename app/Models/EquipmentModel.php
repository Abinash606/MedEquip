<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table      = 'equipment';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id',
        'site_id',
        'asset_tag',
        'make',
        'model',
        'serial_number',
        'device_type',
        'department',
        'location',
        'status',
        'pm_kit',
        'pm_manual_path',
        'service_manual_path',
        'photo_path',
        'fast_notes',
        'installation_date',
        'warranty_expires',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
}