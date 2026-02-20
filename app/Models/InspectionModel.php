<?php

namespace App\Models;

use CodeIgniter\Model;

class InspectionModel extends Model
{
    protected $table      = 'inspections';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'site_id', 'equipment_id', 'group_id', 'scheduled_at', 'completed_at',
        'status', 'technician_id', 'findings', 'notes', 'next_due_date',
        'inspection_type', 'pm_frequency', 'device_complete',
        'action_performed', 'est', 'cal', 'result',
        'serial_number', 'department', 'location',
        'created_by', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';

    /**
     * Compose an array of report rows for the specified company and group ID.
     * Each row in the returned array represents one inspection record and
     * includes a flattened set of fields from the inspections table as well
     * as related equipment, site and user information. Results are ordered
     * from newest to oldest by the inspection creation timestamp.
     *
     * NOTE: If your database schema uses different table or column names for
     * sites, customers or technicians, adjust the join clauses and selected
     * columns accordingly. This method assumes the following relationships:
     *
     *   inspections.site_id  -> sites.id
     *   sites.customer_id    -> customers.id
     *   inspections.technician_id -> users.id
     *
     * @param int    $companyId The ID of the company to scope the query
     * @param string $groupId   The group identifier for the inspection session
     * @return array An array of associative arrays containing the report rows
     */
    public function getReportRowsByGroup(int $companyId, $groupId): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inspections i');

        $builder->select([
            'i.id',
            'i.group_id',
            // Use 'status' column as the inspection result (Pass/Fail/Repair)
            'i.status AS result',
            // action_performed may not exist in all schemas — use inspection_type as fallback
            'COALESCE(i.inspection_type, \'PM\') AS action_performed',
            'i.notes',
            // Note: est/cal are optional columns added in later schema versions.
            // They are intentionally excluded from this SELECT to avoid SQL errors
            // on databases where the columns have not yet been added.
            // The report JS treats missing/empty values as 'No' automatically.
            'i.created_at AS inspection_date',

            // Equipment
            'e.asset_tag',
            'COALESCE(e.model, e.make, \'—\') AS model',
            'e.device_type',
            'e.serial_number',
            'e.department AS dept',
            'e.location AS room',

            // Site and customer information
            's.name AS site_name',
            'c.name AS customer_name',

            // Customer logo path – used for report previews and PDFs
            'c.logo_path AS logo_path',

            // Technician
            'u.full_name AS technician_name',
        ]);

        $builder->join('equipment e', 'e.id = i.equipment_id', 'left');
        $builder->join('sites s', 's.id = i.site_id', 'left');
        $builder->join('customers c', 'c.id = s.customer_id', 'left');
        $builder->join('users u', 'u.id = i.technician_id', 'left');

        $builder->where('i.company_id', $companyId);
        $builder->where('i.group_id', $groupId);
        $builder->orderBy('i.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}