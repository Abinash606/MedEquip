<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteModel extends Model
{
    protected $table      = 'sites';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'customer_id', 'name', 'site_identifier', 'address', 'city',
        'state', 'zip', 'contact_name', 'email', 'phone', 'created_at',
        'updated_at', 'deleted_at',
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
	
	 /**
     * Returns a map: [customer_id => first_site_id]
     */
    public function getFirstSiteIdByCustomerIds(array $customerIds, int $companyId): array
    {
        if (empty($customerIds)) {
            return [];
        }

        $rows = $this->select('customer_id, MIN(id) AS first_site_id')
            ->where('company_id', $companyId)
            ->whereIn('customer_id', $customerIds)
            ->where('deleted_at', null) // only active sites (soft delete)
            ->groupBy('customer_id')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['customer_id']] = (int)$r['first_site_id'];
        }

        return $map;
    }
}