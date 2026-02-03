<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingsModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'company_id',
        'company_name',
        'time_zone',
        'maintenance_mode',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
    ];

    public function getOrCreateByCompany(int $companyId): array
    {
        $row = $this->where('company_id', $companyId)->first();
        if ($row) return $row;

        $this->insert([
            'company_id'          => $companyId,
            'company_name'        => '',
            'time_zone'           => 'Asia/Kolkata',
            'maintenance_mode'    => 0,
            'email_notifications' => 1,
            'sms_notifications'   => 0,
            'push_notifications'  => 0,
        ]);

        return $this->where('company_id', $companyId)->first();
    }
}
