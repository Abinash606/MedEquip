<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use Config\Database;

class WorkOrdersController extends BaseController
{
    protected WorkOrderModel $workOrders;

    public function __construct()
    {
        $this->workOrders = new WorkOrderModel();
    }

    private function wantsJson(): bool
    {
        return $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
    }

    private function normalizeStatus(?string $status): string
    {
        $key = strtolower(trim((string) $status));
        $key = str_replace(['-', ' '], '_', $key);

        return match ($key) {
            'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            'in_progress', 'inprogress' => 'in_progress',
            default => 'open',
        };
    }

    private function normalizePriority(?string $priority): string
    {
        $key = strtolower(trim((string) $priority));

        return match ($key) {
            'low' => 'low',
            'high', 'critical' => 'high',
            default => 'normal',
        };
    }

    /**
     * Resolve site_equipment.id from a posted value.
     * work_orders.equipment_id references site_equipment.id directly
     * after the DB migration removed the FK to the master equipment table.
     */
    private function extractAssetTagFromText(?string $text): string
    {
        $text = (string) $text;

        if (preg_match('/^Asset tag:\s*(.+)$/mi', $text, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    private function resolveEquipmentReference(
        int $companyId,
        int $siteId,
        $postedEquipmentId,
        string $fallbackAssetTag = ''
    ): ?array {
        $postedEquipmentId = (int) $postedEquipmentId;
        $db = Database::connect();

        if ($postedEquipmentId > 0) {
            // 1) direct site_equipment.id
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('id', $postedEquipmentId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }

            // 2) legacy master_equipment_id
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('master_equipment_id', $postedEquipmentId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }
        }

        if ($fallbackAssetTag !== '') {
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $fallbackAssetTag)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }
        }

        return null;
    }

    private function fetchWorkOrderRow(int $companyId, int $id): ?array
    {
        $db = Database::connect();

        $row = $db->table('work_orders')
            ->select("
                work_orders.*,
                tech_user.full_name AS assigned_to_name
            ")
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users AS tech_user', 'tech_user.id = technicians.user_id', 'left')
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.id', $id)
            ->where('work_orders.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $siteId = (int) ($row['site_id'] ?? 0);
        $eqRef  = null;

        if (!empty($row['equipment_id'])) {
            $eqRef = $this->resolveEquipmentReference($companyId, $siteId, (int) $row['equipment_id']);
        }

        if (!$eqRef && !empty($row['group_id'])) {
            $insp = $db->table('inspections')
                ->select('asset_tag')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('group_id', $row['group_id'])
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if (!empty($insp['asset_tag'])) {
                $eqRef = $this->resolveEquipmentReference($companyId, $siteId, 0, trim((string) $insp['asset_tag']));
            }
        }

        if (!$eqRef) {
            $descAsset = $this->extractAssetTagFromText($row['description'] ?? '');
            if ($descAsset !== '') {
                $eqRef = $this->resolveEquipmentReference($companyId, $siteId, 0, $descAsset);
            }
        }

        $row['site_equipment_id'] = $eqRef['site_equipment_id'] ?? null;
        $row['asset_tag']         = $eqRef['asset_tag'] ?? '';
        $row['serial_number']     = $eqRef['serial_number'] ?? '';
        $row['make']              = $eqRef['make'] ?? '';
        $row['model']             = $eqRef['model'] ?? '';
        $row['device_type']       = $eqRef['device_type'] ?? '';

        return $row;
    }

    public function index()
    {
        $companyId = (int) $this->session->get('company_id');
        $db = Database::connect();

        $workOrders = $db->query("
            SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date,
                   s.name AS site_name, c.name AS customer_name, u.full_name AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id = wo.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = wo.assigned_to
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE wo.company_id = ? AND wo.deleted_at IS NULL
            ORDER BY wo.created_at DESC
        ", [$companyId])->getResultArray();

        return view('admin/work_orders/index', ['workOrders' => $workOrders]);
    }

    public function show($id)
    {
        $companyId = (int) $this->session->get('company_id');
        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        return $this->response->setJSON([
            'success' => (bool) $row,
            'data'    => $row,
            'message' => $row ? '' : 'Work order not found.',
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/sites');
        }

        $companyId = (int) $this->session->get('company_id');
        $siteId    = (int) $this->request->getPost('site_id');
        $title     = trim((string) $this->request->getPost('title'));
        $groupId   = trim((string) $this->request->getPost('group_id'));

        if ($siteId <= 0 || $title === '') {
            $msg = 'Site and title are required.';
            return $this->wantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->with('error', $msg);
        }

        $postedEquipId  = (int) $this->request->getPost('equipment_id');
        $postedAssetTag = trim((string) $this->request->getPost('asset_tag'));

        // Resolve site_equipment.id — nullable, no master equipment lookups
        if ($postedEquipId <= 0 && $postedAssetTag !== '') {
            $dbC = Database::connect();
            $r = $dbC->table('site_equipment')->select('id')
                ->where('company_id', $companyId)->where('site_id', $siteId)
                ->where('asset_tag', $postedAssetTag)->where('deleted_at', null)
                ->get()->getRowArray();
            if ($r) $postedEquipId = (int) $r['id'];
        }

        $resolvedEquipId = null;
        if ($postedEquipId > 0) {
            $eqRef = $this->resolveEquipmentReference($companyId, $siteId, $postedEquipId);
            if ($eqRef) $resolvedEquipId = (int) $eqRef['equipment_id'];
        }

        $data = [
            'company_id'   => $companyId,
            'site_id'      => $siteId,
            'equipment_id' => $resolvedEquipId, // site_equipment.id or null
            'title'        => $title,
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->normalizeStatus($this->request->getPost('status')),
            'priority'     => $this->normalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'group_id'     => $groupId !== '' ? $groupId : null,
        ];

        $existing = null;
        if ($groupId !== '') {
            $b = $this->workOrders
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('group_id', $groupId)
                ->where('deleted_at', null);
            if ($resolvedEquipId) $b->where('equipment_id', $resolvedEquipId);
            $existing = $b->first();
        }

        if ($existing) {
            $this->workOrders->update((int) $existing['id'], array_merge($data, ['updated_at' => date('Y-m-d H:i:s')]));
            $id = (int) $existing['id'];
        } else {
            $this->workOrders->insert($data);
            $id = (int) $this->workOrders->getInsertID();
        }

        $row = $this->fetchWorkOrderRow($companyId, $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => $existing ? 'Work order updated successfully' : 'Work order created successfully',
                'work_order_id'    => $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/sites/' . $siteId)->with('success', $existing ? 'Work order updated.' : 'Work order created.');
    }

    public function update($id)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }

        $companyId = (int) $this->session->get('company_id');
        $existingRow = $this->fetchWorkOrderRow($companyId, (int) $id);

        if (!$existingRow) {
            $msg = 'Work order not found.';
            return $this->wantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->with('error', $msg);
        }

        $siteId = (int) ($this->request->getPost('site_id') ?: $existingRow['site_id']);
        $postedEquipmentId = (int) $this->request->getPost('equipment_id');
        $postedAssetTagUpd = trim((string) $this->request->getPost('asset_tag'));

        // Resolve site_equipment.id — keep existing if nothing valid posted
        $equipmentId = !empty($existingRow['equipment_id']) ? (int) $existingRow['equipment_id'] : null;
        $lookupId = $postedEquipmentId;

        if ($lookupId <= 0 && $postedAssetTagUpd !== '') {
            $dbB = Database::connect();
            $r = $dbB->table('site_equipment')->select('id')
                ->where('company_id', $companyId)->where('site_id', $siteId)
                ->where('asset_tag', $postedAssetTagUpd)->where('deleted_at', null)
                ->get()->getRowArray();
            if ($r) $lookupId = (int) $r['id'];
        }
        if ($lookupId > 0) {
            $eqRef = $this->resolveEquipmentReference($companyId, $siteId, $lookupId);
            if ($eqRef) $equipmentId = (int) $eqRef['equipment_id'];
        }

        $data = [
            'title'        => trim((string) $this->request->getPost('title')),
            'equipment_id' => $equipmentId,
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->normalizeStatus($this->request->getPost('status')),
            'priority'     => $this->normalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $groupId = trim((string) $this->request->getPost('group_id'));
        if ($groupId !== '') {
            $data['group_id'] = $groupId;
        }

        $this->workOrders->update((int) $id, $data);

        // If serial_number was submitted, update it on the linked site_equipment row
        $postedSerial = trim((string) $this->request->getPost('serial_number'));
        if ($postedSerial !== '' && $equipmentId) {
            Database::connect()->table('site_equipment')
                ->where('id', $equipmentId)->where('company_id', $companyId)
                ->update(['serial_number' => $postedSerial, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => 'Work order updated successfully',
                'work_order_id'    => (int) $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/sites/' . $siteId)->with('success', 'Work order updated.');
    }

    /**
     * GET admin/work-orders/findByGroup
     * Finds the auto-created WO for a given inspection group + asset_tag so the
     * Fail+WO modal can do UPDATE instead of INSERT (prevents duplicates).
     */
    public function findByGroup()
    {
        $companyId = (int) $this->session->get('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');

        if ($groupId === '' || $assetTag === '') {
            return $this->response->setJSON(['success' => false]);
        }

        $db = Database::connect();

        // Look for WO that matches this inspection group
        $wo = $db->table('work_orders wo')
            ->select('wo.id, wo.title, wo.description, wo.status, wo.priority, wo.assigned_to, wo.start_date, wo.end_date')
            ->join('site_equipment se', 'se.id = wo.equipment_id AND se.deleted_at IS NULL', 'left')
            ->where('wo.company_id', $companyId)
            ->where('wo.group_id', $groupId)
            ->where('wo.deleted_at', null)
            ->groupStart()
                ->where('se.asset_tag', $assetTag)
                ->orWhere('wo.site_id', $siteId)
            ->groupEnd()
            ->orderBy('wo.id', 'DESC')
            ->get()->getRowArray();

        if (!$wo) {
            // Simpler fallback: just match by group_id and site_id
            $wo = $db->table('work_orders')
                ->where('company_id', $companyId)
                ->where('group_id', $groupId)
                ->where('site_id', $siteId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
        }

        if (!$wo) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON([
            'success'        => true,
            'work_order_id'  => $wo['id'],
            'title'          => $wo['title'] ?? '',
            'description'    => $wo['description'] ?? '',
            'status'         => $wo['status'] ?? 'open',
            'priority'       => $wo['priority'] ?? 'normal',
            'assigned_to'    => $wo['assigned_to'] ?? null,
            'start_date'     => $wo['start_date'] ?? '',
            'end_date'       => $wo['end_date'] ?? '',
        ]);
    }

    public function delete($id)
    {
        $companyId = (int) $this->session->get('company_id');

        $exists = $this->workOrders
            ->where('company_id', $companyId)
            ->where('id', (int) $id)
            ->where('deleted_at', null)
            ->first();

        if (!$exists) {
            if ($this->wantsJson()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Work order not found.']);
            }
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $this->workOrders->delete((int) $id);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'success'       => true,
                'message'       => 'Work order deleted successfully',
                'work_order_id' => (int) $id,
                'csrf_hash'     => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', 'Work order deleted.');
    }
}