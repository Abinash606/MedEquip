<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderInvoiceModel;
use App\Models\WorkOrderInvoiceItemModel;
use App\Models\WorkOrderModel;
use App\Models\LaborCodeModel;
use Config\Database;

// mPDF loaded at runtime via class_exists() to avoid crash if not installed

/**
 * WorkOrderInvoiceController
 * Handles invoice & packing slip for the AssetIQ work order system (CI4 + mPDF).
 *
 * ROUTES (inside admin group):
 *   GET  admin/work-orders/labor-codes-list
 *   GET  admin/work-orders/(:num)/invoice/data
 *   POST admin/work-orders/(:num)/invoice/save
 *   GET  admin/work-orders/(:num)/invoice/download
 *   GET  admin/work-orders/(:num)/packing-slip/download
 */
class WorkOrderInvoiceController extends BaseController
{
    protected WorkOrderInvoiceModel     $invoiceModel;
    protected WorkOrderInvoiceItemModel $itemModel;
    protected WorkOrderModel            $woModel;
    protected LaborCodeModel            $laborModel;

    public function __construct()
    {
        $this->invoiceModel = new WorkOrderInvoiceModel();
        $this->itemModel    = new WorkOrderInvoiceItemModel();
        $this->woModel      = new WorkOrderModel();
        $this->laborModel   = new LaborCodeModel();
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function fetchWorkOrder(int $companyId, int $woId): ?array
    {
        $db = Database::connect();
        return $db->query("
            SELECT
                wo.*,
                s.name        AS site_name,
                s.address     AS site_address,
                s.city        AS site_city,
                s.state       AS site_state,
                s.zip         AS site_zip,
                c.id          AS customer_db_id,
                c.name        AS customer_name,
                c.internal_labor_rate_notes,
                u.full_name   AS tech_name
            FROM work_orders wo
            LEFT JOIN sites       s ON s.id = wo.site_id
            LEFT JOIN customers   c ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = wo.assigned_to
            LEFT JOIN users       u ON u.id = t.user_id
            WHERE wo.company_id = ?
              AND wo.id         = ?
              AND wo.deleted_at IS NULL
            LIMIT 1
        ", [$companyId, $woId])->getRowArray();
    }

    // =========================================================================
    // PUBLIC ENDPOINTS
    // =========================================================================

    /** GET /admin/work-orders/labor-codes-list */
    public function laborCodesList(): \CodeIgniter\HTTP\Response
    {
        $companyId = (int) $this->session->get('company_id');
        $codes = $this->laborModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->orderBy('code', 'ASC')
            ->findAll();
        return $this->response->setJSON(['success' => true, 'data' => $codes]);
    }

    /** GET /admin/work-orders/{id}/invoice/data */
    public function getData(int $woId): \CodeIgniter\HTTP\Response
    {
        $companyId = (int) $this->session->get('company_id');
        $wo        = $this->fetchWorkOrder($companyId, $woId);

        if (! $wo) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Work order not found.']);
        }

        $invoice = $this->invoiceModel
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->first();

        $items = $this->itemModel
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'wo'      => $wo,
            'invoice' => $invoice,
            'items'   => $items,
        ]);
    }

    /**
     * POST /admin/work-orders/{id}/invoice/save
     * Accepts JSON body with items + notes + signature data.
     */
    public function save(int $woId): \CodeIgniter\HTTP\Response
    {
        $companyId = (int) $this->session->get('company_id');
        $wo        = $this->fetchWorkOrder($companyId, $woId);

        if (! $wo) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Work order not found.']);
        }

        $body = $this->request->getJSON(true) ?? $this->request->getPost();

        $problemNotes     = trim((string)($body['problem_notes']     ?? ''));
        $invoiceNote      = trim((string)($body['invoice_note']      ?? ''));
        $serviceNotes     = trim((string)($body['service_notes']     ?? ''));
        $customerSigName  = trim((string)($body['customer_sig_name'] ?? ''));
        $customerSigImage = trim((string)($body['customer_sig_image']?? ''));
        $techSigName      = trim((string)($body['tech_sig_name']     ?? ''));
        $techSigImage     = trim((string)($body['tech_sig_image']    ?? ''));
        $items            = $body['items'] ?? [];

        // Upsert invoice meta row
        $existing = $this->invoiceModel
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->first();

        $metaData = [
            'work_order_id'   => $woId,
            'company_id'      => $companyId,
            'problem_notes'   => $problemNotes    ?: null,
            'invoice_note'    => $invoiceNote     ?: null,
            'service_notes'   => $serviceNotes    ?: null,
            'signed_by'       => $customerSigName ?: null,
            'signature_image' => $customerSigImage?: null,
            'tech_signed_by'  => $techSigName     ?: null,
            'tech_sig_image'  => $techSigImage    ?: null,
            'signed_at'       => ($customerSigName && ! $existing)
                                    ? date('Y-m-d H:i:s')
                                    : ($existing['signed_at'] ?? null),
        ];

        if ($existing) {
            $this->invoiceModel->update((int)$existing['id'], $metaData);
        } else {
            $this->invoiceModel->insert($metaData);
        }

        // Replace line items
        $db = Database::connect();
        $db->table('work_order_invoice_items')
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->delete();

        foreach ($items as $i => $item) {
            $qty      = (float)($item['qty']       ?? 1);
            $unitCost = (float)($item['unit_cost'] ?? 0);
            $this->itemModel->insert([
                'work_order_id'   => $woId,
                'company_id'      => $companyId,
                'document_type'   => 'invoice',
                'item_type'       => $item['item_type']       ?? 'labor',
                'part_number'     => $item['part_number']     ?? null,
                'labor_code_id'   => ! empty($item['labor_code_id']) ? (int)$item['labor_code_id'] : null,
                'part_labor_code' => $item['part_labor_code'] ?? null,
                'description'     => $item['description']     ?? null,
                'qty'             => $qty,
                'unit_cost'       => $unitCost,
                'total_cost'      => round($qty * $unitCost, 2),
                'sort_order'      => $i,
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Invoice saved successfully.',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    /** GET /admin/work-orders/{id}/invoice/download */
    public function downloadInvoice(int $woId)
    {
        return $this->streamPdf((int)$this->session->get('company_id'), $woId, false);
    }

    /** GET /admin/work-orders/{id}/packing-slip/download */
    public function downloadPackingSlip(int $woId)
    {
        return $this->streamPdf((int)$this->session->get('company_id'), $woId, true);
    }

    // =========================================================================
    // PDF GENERATION
    // =========================================================================

    private function streamPdf(int $companyId, int $woId, bool $packingSlip)
    {
        $wo = $this->fetchWorkOrder($companyId, $woId);
        if (! $wo) {
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $invoice = $this->invoiceModel
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->first() ?? [];

        $items = $this->itemModel
            ->where('work_order_id', $woId)
            ->where('company_id', $companyId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $html     = $this->buildHtml($wo, $invoice, $items, $packingSlip);
        $docLabel = $packingSlip ? 'packing_slip' : 'invoice';
        $filename = $docLabel . '_WO-' . str_pad($woId, 4, '0', STR_PAD_LEFT) . '.pdf';

        $tempDir = WRITEPATH . 'mpdf_temp';
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        if (! class_exists('\Mpdf\Mpdf')) {
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=utf-8')
                ->setBody($this->buildFullHtmlPage($html, $packingSlip));
        }

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_top'    => 10,
                'margin_bottom' => 10,
                'margin_left'   => 10,
                'margin_right'  => 10,
                'tempDir'       => $tempDir,
            ]);
            $mpdf->SetTitle($packingSlip ? 'Work Order Packing Slip' : 'Work Order Invoice');
            $mpdf->SetAuthor('AssetIQ');
            $mpdf->WriteHTML($this->getPdfCss(), \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($html,              \Mpdf\HTMLParserMode::HTML_BODY);
            $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Throwable $e) {
            log_message('error', '[WorkOrderInvoiceController] PDF error WO#' . $woId . ': ' . $e->getMessage());
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=utf-8')
                ->setBody($this->buildFullHtmlPage($html, $packingSlip));
        }
    }

    // =========================================================================
    // HTML BODY BUILDER
    // =========================================================================

    private function buildHtml(array $wo, array $invoice, array $items, bool $packingSlip): string
    {
        $woNumber   = str_pad((int)$wo['id'], 4, '0', STR_PAD_LEFT);
        $docTitle   = $packingSlip ? 'WORK ORDER PACKING SLIP' : 'WORK ORDER INVOICE';
        $woDate      = ! empty($wo['start_date']) ? date('m/d/Y', strtotime($wo['start_date'])) : '';
        $woCompDate  = ! empty($wo['end_date'])   ? date('m/d/Y', strtotime($wo['end_date']))   : '';
        $invoiceDate = ! empty($invoice['updated_at'])
            ? date('m/d/Y', strtotime($invoice['updated_at']))
            : (! empty($invoice['created_at'])
                ? date('m/d/Y', strtotime($invoice['created_at']))
                : date('m/d/Y'));
        $siteAddr   = trim(
            ($wo['site_address'] ?? '') . ' ' .
            ($wo['site_city']    ?? '') . ', ' .
            ($wo['site_state']   ?? '') . ' ' .
            ($wo['site_zip']     ?? '')
        );

        $customerName = htmlspecialchars($wo['customer_name'] ?? '',        ENT_QUOTES);
        $siteName     = htmlspecialchars($wo['site_name']     ?? '',        ENT_QUOTES);
        $techName     = htmlspecialchars($wo['tech_name']     ?? '',        ENT_QUOTES);
        $poNumber     = htmlspecialchars($wo['group_id']      ?? '',        ENT_QUOTES);
        $description  = htmlspecialchars($wo['description']   ?? 'Repair', ENT_QUOTES);
        $customerId   = htmlspecialchars((string)($wo['customer_db_id'] ?? $wo['customer_id'] ?? ''), ENT_QUOTES);
        $siteId       = htmlspecialchars((string)($wo['site_id']         ?? ''), ENT_QUOTES);
        $siteAddrEsc  = htmlspecialchars($siteAddr, ENT_QUOTES);

        // ── Company logo ──────────────────────────────────────────────────────
        $logoUrl  = base_url('logo/assetiq logo.png');
        $logoHtml = "<img src=\"{$logoUrl}\" alt=\"AssetIQ Logo\" style=\"max-height:70px;max-width:160px;display:block;margin:0 auto;\">";

        // ── Line items ────────────────────────────────────────────────────────
        $itemRows   = '';
        $grandTotal = 0.0;

        foreach ($items as $item) {
            $qty       = number_format((float)$item['qty'],        0);
            $unitCost  = number_format((float)$item['unit_cost'],  2);
            $totalCost = number_format((float)$item['total_cost'], 2);
            $grandTotal += (float)$item['total_cost'];

            $partNum = htmlspecialchars($item['part_number']     ?? '', ENT_QUOTES);
            $plCode  = htmlspecialchars($item['part_labor_code'] ?? '', ENT_QUOTES);
            $desc    = htmlspecialchars($item['description']     ?? '', ENT_QUOTES);

            if ($packingSlip) {
                $itemRows .= "<tr>
                    <td class='td-left'>{$partNum}</td>
                    <td class='td-center'>{$plCode}</td>
                    <td class='td-center'>{$desc}</td>
                    <td class='td-center'>{$qty} hrs</td>
                </tr>";
            } else {
                $itemRows .= "<tr>
                    <td class='td-left'>{$partNum}</td>
                    <td class='td-center'>{$plCode}</td>
                    <td class='td-center'>{$desc}</td>
                    <td class='td-center'>{$qty} hrs</td>
                    <td class='td-right'>\${$unitCost}</td>
                    <td class='td-right'>\${$totalCost}</td>
                </tr>";
            }
        }

        $totalRow = '';
        if (! $packingSlip) {
            $grandFmt = '$' . number_format($grandTotal, 2);
            $totalRow = "<tr>
                <td colspan='4'>&nbsp;</td>
                <td class='td-right total-label'>Total</td>
                <td class='td-right total-amount'>{$grandFmt}</td>
            </tr>";
        }

        $thead = $packingSlip
            ? '<th>Part #</th><th>Part/Labor Code</th><th>Description</th><th>QTY</th>'
            : '<th>Part #</th><th>Part/Labor Code</th><th>Description</th><th>QTY</th><th>Unit Cost</th><th>Total Cost</th>';

        // ── Notes (invoice only) ──────────────────────────────────────────────
        $notesHtml = '';
        if (! $packingSlip) {
            $problemNotes = nl2br(htmlspecialchars($invoice['problem_notes'] ?? '', ENT_QUOTES));
            $invoiceNote  = nl2br(htmlspecialchars($invoice['invoice_note']  ?? '', ENT_QUOTES));
            $serviceNotes = nl2br(htmlspecialchars($invoice['service_notes'] ?? '', ENT_QUOTES));

            $notesHtml = "
            <table class='notes-table'>
                <tr><td class='notes-cell'><strong>Problem Notes:</strong><br>{$problemNotes}</td></tr>
                <tr><td class='notes-cell invoice-note-cell'><strong class='invoice-note-label'>Invoice Note:</strong><br>{$invoiceNote}</td></tr>
                <tr><td class='notes-cell'><strong>Service Notes:</strong><br>{$serviceNotes}</td></tr>
            </table>";
        }

        // ── Signatures (invoice only) — on a new page ─────────────────────────
        $signatureHtml = '';
        if (! $packingSlip) {
            // Customer signature
            $custSigName = htmlspecialchars($invoice['signed_by'] ?? '', ENT_QUOTES);
            $custSigAt   = htmlspecialchars($invoice['signed_at'] ?? '', ENT_QUOTES);
            $custSigImg  = ! empty($invoice['signature_image'])
                ? "<img src=\"{$invoice['signature_image']}\" style=\"max-width:260px;max-height:80px;display:block;margin:6px 0;\" alt=\"Customer Signature\">"
                : '';

            // Technician signature
            $techSigName = htmlspecialchars($invoice['tech_signed_by'] ?? $techName, ENT_QUOTES);
            $techSigImg  = ! empty($invoice['tech_sig_image'])
                ? "<img src=\"{$invoice['tech_sig_image']}\" style=\"max-width:260px;max-height:80px;display:block;margin:6px 0;\" alt=\"Tech Signature\">"
                : '';

            $signatureHtml = "
            <pagebreak />
            <table class='sig-table'>
                <tr>
                    <td class='sig-cell'>
                        <div class='sig-title'>Customer Acceptance Signature</div>
                        <div class='sig-pad'>{$custSigImg}</div>
                        <div class='sig-name-line'><strong>Name:</strong> {$custSigName}</div>
                        " . ($custSigAt ? "<div class='sig-name-line'><strong>Date:</strong> {$custSigAt}</div>" : '') . "
                    </td>
                    <td class='sig-cell'>
                        <div class='sig-title'>Technician Signature</div>
                        <div class='sig-pad'>{$techSigImg}</div>
                        <div class='sig-name-line'><strong>Name:</strong> {$techSigName}</div>
                    </td>
                </tr>
            </table>";
        }

        return "
        <!-- ═══ HEADER ═══ -->
        <table class='header-table'>
          <tr>
            <td class='logo-cell'>{$logoHtml}</td>
            <td class='info-cell'>
              <div class='doc-title'>{$docTitle}</div>
              <table class='info-inner'>
                <tr>
                  <td class='info-col'>
                    <table class='meta-table'>
                      <tr><td class='meta-label'>Work Order #</td><td class='meta-value'>{$woNumber}</td></tr>
                      <tr><td class='meta-label'>Customer #</td><td class='meta-value'>{$customerId}</td></tr>
                      <tr><td class='meta-label'>Customer</td><td class='meta-value'>{$customerName}</td></tr>
                      <tr><td class='meta-label'>PO #</td><td class='meta-value'>{$poNumber}</td></tr>
                    </table>
                  </td>
                  <td class='info-col info-col-right'>
                    <table class='meta-table'>
                      <tr><td class='meta-label'>Invoice Date</td><td class='meta-value'>{$invoiceDate}</td></tr>
                      <tr><td class='meta-label'>Work Order Date</td><td class='meta-value'>{$woDate}</td></tr>
                      <tr><td class='meta-label'>Completed Date</td><td class='meta-value'>{$woCompDate}</td></tr>
                      <tr><td class='meta-label'>Site #</td><td class='meta-value'>{$siteId}</td></tr>
                      <tr><td class='meta-label'>Site</td><td class='meta-value'>{$siteName}</td></tr>
                      <tr><td class='meta-label'>Service Tech</td><td class='meta-value'>{$techName}</td></tr>
                      <tr><td class='meta-label'>Territory</td><td class='meta-value'>&nbsp;</td></tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td colspan='2' class='section-row'>
                    <span class='section-label'>Site Address</span>
                    <div class='section-value'>{$siteAddrEsc}</div>
                  </td>
                </tr>
                <tr>
                  <td colspan='2' class='section-row'>
                    <span class='section-label'>Work Order Description</span>
                    <div class='section-value'>{$description}</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <!-- ═══ LINE ITEMS ═══ -->
        <table class='items-table'>
          <thead><tr>{$thead}</tr></thead>
          <tbody>{$itemRows}{$totalRow}</tbody>
        </table>

        <!-- ═══ NOTES ═══ -->
        {$notesHtml}

        <!-- ═══ SIGNATURES ═══ -->
        {$signatureHtml}
        ";
    }

    // =========================================================================
    // CSS FOR PDF
    // =========================================================================

    private function getPdfCss(): string
    {
        return "
        @page { margin: 10mm; }

        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; }

        /* ─ Header ─ */
        .header-table { width:100%; border-collapse:collapse; margin-bottom:10px; table-layout:fixed; }
        .logo-cell     { width:165px; border:1px solid #bbb; padding:10px; text-align:center; vertical-align:middle; }
        .info-cell     { border:1px solid #bbb; padding:0; vertical-align:top; }
        .doc-title     { font-size:16px; font-weight:bold; text-align:center; margin:0; padding:10px 12px; border-bottom:1px solid #bbb; letter-spacing:0.3px; }
        .info-inner    { width:100%; border-collapse:collapse; font-size:10.5px; table-layout:fixed; }
        .info-inner td { padding:0; vertical-align:top; }
        .info-col      { width:50%; padding:10px 12px; }
        .info-col-right{ border-left:1px solid #ddd; }
        .meta-table    { width:100%; border-collapse:collapse; }
        .meta-table td { padding:3px 0; vertical-align:top; }
        .meta-label    { width:105px; font-weight:bold; color:#222; white-space:nowrap; }
        .meta-label:after { content: ':'; }
        .meta-value    { padding-left:8px; text-align:left; word-wrap:break-word; }
        .section-row   { border-top:1px solid #ddd; padding:8px 12px !important; }
        .section-label { display:block; font-weight:bold; margin-bottom:3px; }
        .section-value { display:block; line-height:1.45; }

        /* ─ Line items ─ */
        .items-table    { width:100%; border-collapse:collapse; margin-bottom:8px; }
        .items-table th { background:#eee; border:1px solid #aaa; padding:6px 7px; font-size:10.5px; font-weight:bold; text-align:center; }
        .items-table td { border:1px solid #ccc; padding:6px 7px; font-size:10.5px; }

        /* ─ Notes ─ */
        .notes-table        { width:100%; border-collapse:collapse; margin-top:6px; }
        .notes-cell         { border:1px solid #ccc; padding:7px 9px; font-size:10.5px; vertical-align:top; }
        .invoice-note-cell  { background:#fff8f0; }
        .invoice-note-label { color:#cc0000; }

        /* ─ Totals ─ */
        .total-label  { font-weight:bold; font-size:11px; }
        .total-amount { font-weight:bold; font-size:11px; }

        /* ─ Alignment ─ */
        .td-left   { text-align:left;   }
        .td-center { text-align:center; }
        .td-right  { text-align:right;  }

        /* ─ Signatures ─ */
        .sig-table      { width:100%; border-collapse:collapse; margin-top:24px; }
        .sig-cell       { width:50%; border:1.5px solid #333; padding:16px 18px; vertical-align:top; }
        .sig-title      { font-size:12px; font-weight:bold; margin-bottom:10px; border-bottom:1px solid #ddd; padding-bottom:6px; }
        .sig-pad        { min-height:90px; border:1px dashed #aaa; background:#fafafa; margin-bottom:10px; border-radius:4px; padding:4px; }
        .sig-name-line  { font-size:10.5px; margin-top:6px; border-top:1px solid #ccc; padding-top:4px; }
        ";
    }

    // =========================================================================
    // HTML PAGE WRAPPER (fallback when mPDF not installed)
    // =========================================================================

    private function buildFullHtmlPage(string $bodyHtml, bool $packingSlip): string
    {
        $title = $packingSlip ? 'Work Order Packing Slip' : 'Work Order Invoice';
        $css   = $this->getPdfCss();
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; margin: 0; }
        .page { max-width: 920px; margin: 0 auto; background: #fff; padding: 32px 36px; box-shadow: 0 2px 14px rgba(0,0,0,0.15); }
        .print-btn { display: block; margin: 24px auto; padding: 10px 32px; font-size: 15px; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: 600; }
        @media print {
            body { background:#fff; padding:0; }
            .page { box-shadow:none; padding:0; }
            .print-btn { display:none; }
        }
        {$css}
    </style>
</head>
<body>
    <div class="page">
        {$bodyHtml}
    </div>
    <button class="print-btn" onclick="window.print()">&#128438; Print / Save as PDF</button>
</body>
</html>
HTML;
    }
}
