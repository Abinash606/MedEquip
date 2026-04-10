<?php

namespace App\Models;

use CodeIgniter\Model;

class InspectionModel extends Model
{
    protected $table      = 'inspections';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'site_id', 'equipment_id', 'group_id', 'title', 'scheduled_at', 'completed_at',
        'status', 'technician_id', 'findings', 'notes', 'next_due_date',
        'inspection_type', 'pm_frequency', 'device_complete',
        'action_performed', 'est', 'cal', 'result',
        'asset_tag', 'make', 'model', 'device_type', 'serial_number', 'department', 'location',
        'created_by', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $returnType     = 'array';

    public function getReportRowsByGroup(int $companyId, $groupId): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i');

        $builder->select([
            'i.id',
            'i.group_id',
            'i.title',
            'i.est',
            'i.cal',
            'COALESCE(i.result, i.status) AS result',
            'COALESCE(i.action_performed, i.inspection_type, \'PM\') AS action_performed',
            'i.notes',
            'i.created_at AS inspection_date',
            'COALESCE(i.asset_tag, e.asset_tag) AS asset_tag',
            'COALESCE(i.model, e.model, i.make, e.make, \'—\') AS model',
            'COALESCE(i.device_type, e.device_type) AS device_type',
            'COALESCE(i.serial_number, e.serial_number) AS serial_number',
            'COALESCE(i.department, e.department) AS dept',
            'COALESCE(i.location, e.location) AS room',
            's.name AS site_name',
            'c.name AS customer_name',
            'c.logo_path AS logo_path',
            'COALESCE(u_tech.full_name, u_direct.full_name) AS technician_name',
        ]);

        $builder->join('site_equipment e', 'e.id = i.equipment_id', 'left');
        $builder->join('sites s', 's.id = i.site_id', 'left');
        $builder->join('customers c', 'c.id = s.customer_id', 'left');
        $builder->join('technicians t', 't.id = i.technician_id', 'left');
        $builder->join('users u_tech', 'u_tech.id = t.user_id', 'left');
        $builder->join('users u_direct', 'u_direct.id = i.technician_id', 'left');

        $builder->where('i.company_id', $companyId);
        $builder->where('i.group_id', $groupId);
        $builder->where('i.deleted_at', null);
        $builder->orderBy('i.created_at', 'DESC');
        $builder->orderBy('i.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
