<?php

namespace App\Libraries;

use App\Models\WorkOrderModel;
use Config\Database;

class OperationalWorkOrderService
{
    private WorkOrderModel $workOrders;

    public function __construct()
    {
        $this->workOrders = new WorkOrderModel();
    }

    private function normalizeStatus(string $status): string
    {
        $key = strtolower(trim($status));
        $key = str_replace(['-', ' '], '_', $key);

        return match ($key) {
            'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            'in_progress', 'inprogress' => 'in_progress',
            default => 'open',
        };
    }

    private function normalizePriority(string $priority): string
    {
        $key = strtolower(trim($priority));
        return match ($key) {
            'low' => 'low',
            'high', 'critical' => 'high',
            default => 'normal',
        };
    }

    /**
     * Resolve site_equipment.id from:
     * 1) direct site_equipment.id
     * 2) legacy master_equipment_id
     * 3) asset_tag fallback
     */
    private function resolveSiteEquipmentId(
        int $companyId,
        int $siteId,
        int $inputId = 0,
        string $assetTag = ''
    ): ?int {
        if ($companyId <= 0 || $siteId <= 0) {
            return null;
        }

        $db = Database::connect();

        if ($inputId > 0) {
            // A) direct site_equipment.id
            $siteEq = $db->table('site_equipment')
                ->select('id')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('id', $inputId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return (int) $siteEq['id'];
            }

            // B) legacy master_equipment_id -> current site_equipment.id
            $siteEq = $db->table('site_equipment')
                ->select('id')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('master_equipment_id', $inputId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return (int) $siteEq['id'];
            }
        }

        if ($assetTag !== '') {
            $siteEq = $db->table('site_equipment')
                ->select('id')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return (int) $siteEq['id'];
            }
        }

        return null;
    }

    public function syncFollowUpFromInspection(array $context): ?int
    {
        $companyId = (int) ($context['company_id'] ?? 0);
        $siteId    = (int) ($context['site_id'] ?? 0);
        $rawEqId   = (int) ($context['equipment_id'] ?? 0);
        $groupId   = trim((string) ($context['group_id'] ?? ''));
        $status    = ucfirst(strtolower(trim((string) ($context['status'] ?? ''))));
        $assetTag  = trim((string) ($context['asset_tag'] ?? ''));

        if ($companyId <= 0 || $siteId <= 0 || $groupId === '') {
            return null;
        }

        if (!in_array($status, ['Fail', 'Repair'], true)) {
            return null;
        }

        $siteEquipmentId = $this->resolveSiteEquipmentId($companyId, $siteId, $rawEqId, $assetTag);

        $inspectionType = trim((string) ($context['inspection_type'] ?? ''));
        $notes          = trim((string) ($context['notes'] ?? ''));
        $technicianId   = !empty($context['technician_id']) ? (int) $context['technician_id'] : null;
        $createdBy      = !empty($context['created_by']) ? (int) $context['created_by'] : null;
        $startDate      = !empty($context['start_date']) ? $context['start_date'] : date('Y-m-d');
        $priority       = $status === 'Fail' ? 'high' : 'normal';
        $title          = $inspectionType !== ''
            ? 'Inspection Follow-up: ' . $inspectionType
            : 'Inspection Follow-up Required';

        $descriptionLines = [
            'Auto-created from inspection workflow.',
            'Inspection result: ' . $status,
            'Inspection group: ' . $groupId,
        ];

        if ($assetTag !== '') {
            $descriptionLines[] = 'Asset tag: ' . $assetTag;
        }

        if ($inspectionType !== '') {
            $descriptionLines[] = 'Inspection type: ' . $inspectionType;
        }

        if ($notes !== '') {
            $descriptionLines[] = 'Inspection notes: ' . $notes;
        }

        $description = implode(PHP_EOL, $descriptionLines);

        // Find existing WO for this inspection group + site + equipment
        $woBuilder = $this->workOrders
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('group_id', $groupId)
            ->where('deleted_at', null);

        if ($siteEquipmentId) {
            $woBuilder->where('equipment_id', $siteEquipmentId);
        } elseif ($assetTag !== '') {
            // Fallback: match by asset_tag in description when no equipment id
            $woBuilder->like('description', 'Asset tag: ' . $assetTag, 'after');
        }

        $existing = $woBuilder->first();

        if ($existing) {
            $update = [
                'title'       => $title,
                'description' => $description,
                'priority'    => $this->normalizePriority($priority),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            if ($technicianId) {
                $update['assigned_to'] = $technicianId;
            }

            $currentStatus = $this->normalizeStatus((string) ($existing['status'] ?? 'open'));
            if (in_array($currentStatus, ['completed', 'cancelled'], true)) {
                $update['status'] = 'open';
            }

            if ($siteEquipmentId && empty($existing['equipment_id'])) {
                $update['equipment_id'] = $siteEquipmentId;
            }

            $this->workOrders->update((int) $existing['id'], $update);
            return (int) $existing['id'];
        }

        $this->workOrders->insert([
            'company_id'   => $companyId,
            'site_id'      => $siteId,
            'equipment_id' => $siteEquipmentId, // site_equipment.id
            'group_id'     => $groupId,
            'title'        => $title,
            'description'  => $description,
            'status'       => 'open',
            'priority'     => $this->normalizePriority($priority),
            'assigned_to'  => $technicianId,
            'created_by'   => $createdBy,
            'start_date'   => $startDate,
            'end_date'     => null,
            'completed_at' => null,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->workOrders->getInsertID() ? (int) $this->workOrders->getInsertID() : null;
    }
}