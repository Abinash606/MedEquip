<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LaborCodeModel;

/**
 * LaborCodesController
 * Handles CRUD for labor codes under Settings > Labor Codes.
 * All routes are prefixed: /admin/settings/labor-codes/...
 */
class LaborCodesController extends BaseController
{
    protected LaborCodeModel $model;

    public function __construct()
    {
        $this->model = new LaborCodeModel();
    }

    // ─── List (DataTables AJAX) ───────────────────────────────────────────────
    /**
     * GET /admin/settings/labor-codes
     * Returns JSON array of labor codes for this company.
     */
    public function index(): string
    {
        $companyId = (int) $this->session->get('company_id');

        $rows = $this->model
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->orderBy('code', 'ASC')
            ->findAll();

        return $this->response->setJSON(['data' => $rows])->getBody();
    }

    // ─── Get single (for edit modal) ─────────────────────────────────────────
    /**
     * GET /admin/settings/labor-codes/{id}
     */
    public function get(int $id): string
    {
        $companyId = (int) $this->session->get('company_id');

        $row = $this->model
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        if (!$row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Labor code not found.'])
                ->getBody();
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $row])->getBody();
    }

    // ─── Save (Add / Edit) ────────────────────────────────────────────────────
    /**
     * POST /admin/settings/labor-codes/save
     * Body: id (optional), code, description, amount
     */
    public function save(): string
    {
        $companyId = (int) $this->session->get('company_id');
        $id        = (int) $this->request->getPost('id');
        $code      = trim((string) $this->request->getPost('code'));
        $desc      = trim((string) $this->request->getPost('description'));
        $amount    = (float) $this->request->getPost('amount');

        if ($code === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Labor code is required.',
            ])->getBody();
        }

        $data = [
            'company_id'  => $companyId,
            'code'        => $code,
            'description' => $desc ?: null,
            'amount'      => $amount,
        ];

        if ($id > 0) {
            // Verify ownership
            $exists = $this->model
                ->where('company_id', $companyId)
                ->where('id', $id)
                ->where('deleted_at', null)
                ->first();

            if (!$exists) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Labor code not found.',
                ])->getBody();
            }

            $this->model->update($id, $data);
            $msg = 'Labor code updated successfully.';
        } else {
            $this->model->insert($data);
            $msg = 'Labor code added successfully.';
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => $msg,
            'csrf_hash' => csrf_hash(),
        ])->getBody();
    }

    // ─── Delete (soft) ────────────────────────────────────────────────────────
    /**
     * DELETE /admin/settings/labor-codes/delete/{id}
     */
    public function delete(int $id): string
    {
        $companyId = (int) $this->session->get('company_id');

        $exists = $this->model
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        if (!$exists) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Labor code not found.'])
                ->getBody();
        }

        $this->model->delete($id);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => 'Labor code deleted.',
            'csrf_hash' => csrf_hash(),
        ])->getBody();
    }
}
