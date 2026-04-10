<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SiteEquipmentModel
 *
 * Represents a piece of equipment assigned to a specific site.
 * This is the working copy used by all inspection flows.
 *
 * The master `equipment` table (Equipment DB page) is a read-only
 * catalogue. When a technician picks a device from the master catalogue,
 * a site_equipment record is created linked via master_equipment_id.
 */
class SiteEquipmentModel extends Model
{
    protected $table      = 'site_equipment';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'site_id',
        'master_equipment_id',
        'asset_tag',
        'make',
        'model',
        'serial_number',
        'device_type',
        'department',
        'location',
        'est',
        'cal',
        'pm_kit',
        'fast_notes',
        'installation_date',
        'warranty_expires',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $returnType     = 'array';

    // ── Convenience: find by asset_tag within a site ──────────────────────
    public function findByAssetTag(int $companyId, int $siteId, string $assetTag): ?array
    {
        return $this
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('asset_tag', $assetTag)
            ->where('deleted_at', null)
            ->first();
    }

    // ── Convenience: get all active equipment for a site ─────────────────
    public function forSite(int $companyId, int $siteId): array
    {
        return $this
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('deleted_at', null)
            ->orderBy('asset_tag', 'ASC')
            ->findAll();
    }

    // ── Create site_equipment from master catalogue row ───────────────────
    public function createFromMaster(array $master, int $siteId): int
    {
        return $this->safeInsert([
            'company_id'          => $master['company_id'],
            'site_id'             => $siteId,
            'master_equipment_id' => $master['id'],
            'asset_tag'           => $master['asset_tag'],
            'make'                => $master['make']          ?? '',
            'model'               => $master['model']         ?? '',
            'serial_number'       => $master['serial_number'] ?? '',
            'device_type'         => $master['device_type']   ?? '',
            'department'          => $master['department']    ?? '',
            'location'            => $master['location']      ?? '',
            'est'                 => $master['est']           ?? 0,
            'cal'                 => $master['cal']           ?? 0,
            'status'              => 'ready',
        ]);
    }

    // ── Safe insert: works even without AUTO_INCREMENT ────────────────────
    // Calculates the next id manually so inserts never fail due to a missing
    // AUTO_INCREMENT (which happens if the migration ALTER TABLE was skipped).
    // Returns the id of the newly inserted (or already-existing) row.
    public function safeInsert(array $data): int
    {
        $db = \Config\Database::connect();

        $maxRow     = $db->query("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM site_equipment")->getRow();
        $data['id'] = $maxRow ? (int) $maxRow->next_id : 1;

        $fields       = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $db->query(
            "INSERT IGNORE INTO site_equipment ($fields) VALUES ($placeholders)",
            array_values($data)
        );

        if ($db->affectedRows() > 0) {
            return $data['id'];
        }

        $existing = $db->query(
            "SELECT id FROM site_equipment WHERE company_id = ? AND site_id = ? AND asset_tag = ? AND deleted_at IS NULL LIMIT 1",
            [$data['company_id'], $data['site_id'], $data['asset_tag']]
        )->getRow();

        return $existing ? (int) $existing->id : $data['id'];
    }
}
