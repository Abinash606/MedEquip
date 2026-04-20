<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Work Orders</h3>
        <p class="text-muted small mb-0">All work orders across all sites.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="btnOpen"   class="btn btn-primary active"       onclick="filterStatus('open')">Open</button>
            <button type="button" id="btnAll"    class="btn btn-outline-secondary"    onclick="filterStatus('all')">All</button>
            <button type="button" id="btnClosed" class="btn btn-outline-secondary"    onclick="filterStatus('closed')">Closed</button>
        </div>
    </div>
</div>

<div class="content">
<div class="glass-card p-3">
    <div class="table-responsive">
        <table id="workOrdersTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Site / Customer</th>
                    <th>Technician</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workOrders as $wo):
                    $priClass = match(strtolower($wo['priority'] ?? '')) {
                        'critical' => 'bg-danger', 'high' => 'bg-warning',
                        'medium'   => 'bg-info',   'low'  => 'bg-success',
                        default    => 'bg-secondary',
                    };
                    $stClass = match(strtolower($wo['status'] ?? '')) {
                        'closed','completed' => 'bg-success',
                        'in_progress'        => 'bg-warning',
                        'open'               => 'bg-primary',
                        default              => 'bg-secondary',
                    };
                    $woId = (int) $wo['id'];
                ?>
                <tr data-status="<?= esc(strtolower($wo['status'] ?? 'open')) ?>">
                    <td><span class="t-pill">#WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                    <td class="fw-medium"><?= esc($wo['title']) ?></td>
                    <td>
                        <div><?= esc($wo['site_name'] ?? '—') ?></div>
                        <div class="text-muted small"><?= esc($wo['customer_name'] ?? '') ?></div>
                    </td>
                    <td><?= esc($wo['tech_name'] ?? '— Unassigned —') ?></td>
                    <td><span class="badge <?= $priClass ?>"><?= esc(ucfirst($wo['priority'] ?? '—')) ?></span></td>
                    <td><span class="badge <?= $stClass ?>"><?= esc(ucwords(str_replace('_',' ',$wo['status'] ?? '—'))) ?></span></td>
                    <td class="text-muted small"><?= !empty($wo['start_date']) ? date('M j, Y', strtotime($wo['start_date'])) : '—' ?></td>
                    <td class="text-muted small"><?= !empty($wo['end_date'])   ? date('M j, Y', strtotime($wo['end_date']))   : '—' ?></td>
                    <td>
                        <!-- Actions Dropdown -->
                        <div class="d-flex align-items-center gap-1">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        title="Download Documents">
                                    <i class="fas fa-download me-1"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="fas fa-file-invoice me-1"></i> Documents
                                        </h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item btn-open-invoice"
                                           href="#"
                                           data-wo-id="<?= $woId ?>"
                                           data-wo-title="<?= esc($wo['title']) ?>">
                                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                            Download Invoice
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="<?= site_url('admin/work-orders/' . $woId . '/packing-slip/download') ?>"
                                           target="_blank">
                                            <i class="fas fa-box me-2 text-success"></i>
                                            Download Packing Slip
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger btn-delete-wo"
                                           href="#"
                                           data-wo-id="<?= $woId ?>">
                                            <i class="fas fa-trash me-2"></i>
                                            Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     INVOICE MODAL
══════════════════════════════════════════════════════════════ -->

<style>
  #invoiceModal #invoiceItemsTable thead th {
    background: #f8f9fa !important;
    color: #111827 !important;
    font-weight: 700 !important;
    vertical-align: middle;
    white-space: normal;
  }

  #invoiceModal #invoiceItemsTable tbody td,
  #invoiceModal #invoiceItemsTable tfoot td {
    background: #ffffff !important;
    color: #111827 !important;
    vertical-align: middle;
  }

  #invoiceModal #invoiceItemsTable .form-control,
  #invoiceModal #invoiceItemsTable .form-select {
    background: #ffffff !important;
    color: #111827 !important;
    border-color: #ced4da !important;
  }

  #invoiceModal #invoiceItemsTable .form-control::placeholder {
    color: #6b7280 !important;
  }

  #invoiceModal #invoiceItemsTable .inv-total-cost[readonly] {
    background: #f8f9fa !important;
    color: #111827 !important;
  }

  #invoiceModal #invoiceGrandTotal,
  #invoiceModal #woGrandTotal {
    color: #111827 !important;
  }
</style>

<div class="modal fade modal-xl" id="invoiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i> Work Order Invoice</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="invoiceModalBody">
        <div class="text-center py-5" id="invoiceLoadingSpinner">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Loading invoice data…</p>
        </div>

        <!-- Invoice content (shown after load) -->
        <div id="invoiceContent" style="display:none;">

          <!-- Work Order header info (read-only) -->
          <div class="row g-3 mb-3" id="invoiceWoInfo"></div>

          <!-- Line Items table -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Line Items</h6>
            <button class="btn btn-sm btn-primary" id="btnAddInvoiceItem">
              <i class="fas fa-plus me-1"></i> Add Line Item
            </button>
          </div>

          <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered invoice-editor-table" id="invoiceItemsTable">
              <thead class="table-light">
                <tr>
                  <th style="width:90px">Type</th>
                  <th style="width:90px">Part #</th>
                  <th style="width:130px">Labor/Part Code</th>
                  <th>Description</th>
                  <th style="width:80px">QTY/Hrs</th>
                  <th style="width:100px">Unit Cost ($)</th>
                  <th style="width:100px">Total Cost ($)</th>
                  <th style="width:60px"></th>
                </tr>
              </thead>
              <tbody id="invoiceItemsTbody">
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6" class="text-end fw-bold">Grand Total</td>
                  <td class="fw-bold" id="invoiceGrandTotal">$0.00</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Notes -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Problem Notes</label>
              <textarea id="invoiceProblemNotes" class="form-control" rows="3"
                placeholder="e.g. Weekly Repair/PM week of 3/30 - 4/1"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-danger">Invoice Note</label>
              <textarea id="invoiceInvoiceNote" class="form-control" rows="3"
                placeholder="Billing rates, terms, etc."></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Service Notes</label>
              <textarea id="invoiceServiceNotes" class="form-control" rows="3"
                placeholder="Tech observations..."></textarea>
            </div>
          </div>

          <!-- Signatures -->
          <hr class="my-3">
          <div class="row g-4">
            <!-- Customer Signature -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Acceptance Signature</label>
              <canvas id="custSigCanvas" class="d-block w-100 border rounded"
                style="height:130px;cursor:crosshair;background:#fafafa;border-style:dashed !important;"></canvas>
              <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearCustSig">
                  <i class="fas fa-eraser me-1"></i>Clear
                </button>
              </div>
              <div class="mt-2">
                <label class="form-label small fw-semibold mb-1">Customer Name</label>
                <input type="text" id="custSigName" class="form-control form-control-sm"
                  placeholder="e.g. CHAR MORALES">
              </div>
            </div>
            <!-- Technician Signature -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Technician Signature</label>
              <canvas id="techSigCanvas" class="d-block w-100 border rounded"
                style="height:130px;cursor:crosshair;background:#fafafa;border-style:dashed !important;"></canvas>
              <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearTechSig">
                  <i class="fas fa-eraser me-1"></i>Clear
                </button>
              </div>
              <div class="mt-2">
                <label class="form-label small fw-semibold mb-1">Technician Name</label>
                <input type="text" id="techSigName" class="form-control form-control-sm"
                  placeholder="Technician name">
              </div>
            </div>
          </div>

        </div><!-- /invoiceContent -->
      </div><!-- /modal-body -->

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success" id="btnSaveInvoice">
          <i class="fas fa-save me-1"></i> Save Invoice
        </button>
        <a class="btn btn-primary" id="btnDownloadInvoice" href="#" target="_blank">
          <i class="fas fa-download me-1"></i> Download PDF
        </a>
      </div>

    </div>
  </div>
</div>

<script>
var _woTable = null;
var _currentWoId = null;
var _laborCodes  = [];

$(function () {
    // ── DataTable ─────────────────────────────────────────────
    _woTable = $('#workOrdersTable').DataTable({ pageLength: 25, order: [[6, 'asc']] });
    filterStatus('open');

    // ── Load labor codes once ─────────────────────────────────
    $.getJSON('<?= site_url('admin/work-orders/labor-codes-list') ?>', function (res) {
        if (res.success) _laborCodes = res.data || [];
    });

    // ── Delete WO ─────────────────────────────────────────────
    $(document).on('click', '.btn-delete-wo', function (e) {
        e.preventDefault();
        var woId = $(this).data('wo-id');
        Swal.fire({
            icon: 'warning', title: 'Delete work order?',
            text: 'This cannot be undone.',
            showCancelButton: true, confirmButtonText: 'Yes, delete'
        }).then(function (r) {
            if (!r.isConfirmed) return;
            $.post('<?= site_url('admin/work-orders/delete') ?>/' + woId, {
                '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val()
            }, function (res) {
                if (res.success) {
                    Swal.fire('Deleted', res.message, 'success');
                    _woTable.ajax ? _woTable.ajax.reload(null, false) : location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }, 'json');
        });
    });

    // ── Open Invoice Modal ────────────────────────────────────
    $(document).on('click', '.btn-open-invoice', function (e) {
        e.preventDefault();
        _currentWoId = $(this).data('wo-id');

        $('#invoiceContent').hide();
        $('#invoiceLoadingSpinner').show();
        $('#invoiceModal').modal('show');

        // Set download link
        $('#btnDownloadInvoice').attr('href', '<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/download');

        // Fetch invoice data
        $.getJSON('<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/data', function (res) {
            $('#invoiceLoadingSpinner').hide();

            if (!res.success) {
                $('#invoiceContent').html('<div class="alert alert-danger">Failed to load invoice data.</div>').show();
                return;
            }

            var wo = res.wo || {};
            var inv = res.invoice || {};

            // Header info
            $('#invoiceWoInfo').html(
                '<div class="col-md-3"><strong>WO #</strong><br>#WO-' + String(wo.id).padStart(4,'0') + '</div>' +
                '<div class="col-md-3"><strong>Customer</strong><br>' + (wo.customer_name || '—') + '</div>' +
                '<div class="col-md-3"><strong>Site</strong><br>' + (wo.site_name || '—') + '</div>' +
                '<div class="col-md-3"><strong>Technician</strong><br>' + (wo.tech_name || '—') + '</div>'
            );

            // Notes
            $('#invoiceProblemNotes').val(inv.problem_notes || '');
            $('#invoiceInvoiceNote').val(inv.invoice_note   || '');
            $('#invoiceServiceNotes').val(inv.service_notes || '');

            // Line items
            $('#invoiceItemsTbody').empty();
            (res.items || []).forEach(function (item) { addInvoiceRow(item); });
            recalcTotal();

            // Restore saved signatures if any
            if (inv.signature_image) {
                restoreSig(window._custSigCtx, inv.signature_image);
            } else { clearSig(window._custSigCtx); }
            $('#custSigName').val(inv.signed_by || wo.customer_name || '');
            if (inv.tech_sig_image) {
                restoreSig(window._techSigCtx, inv.tech_sig_image);
            } else { clearSig(window._techSigCtx); }
            $('#techSigName').val(inv.tech_signed_by || wo.tech_name || '');
            $('#invoiceContent').show();
        }).fail(function () {
            $('#invoiceLoadingSpinner').hide();
            $('#invoiceContent').html('<div class="alert alert-danger">Error loading data.</div>').show();
        });
    });

    // ── Add blank row ─────────────────────────────────────────
    $('#btnAddInvoiceItem').on('click', function () { addInvoiceRow({}); });

    // ── Build labor code select options ───────────────────────
    // Inventory parts for Part-type rows
    var _inventoryParts = [];
    $.getJSON('<?= site_url('admin/inventory/data') ?>', function(res) {
        _inventoryParts = res.data || [];
    });

    function laborCodeOptions(selected) {
        var opts = '<option value="">-- select labor code --</option>';
        _laborCodes.forEach(function(lc) {
            opts += '<option value="' + lc.id + '"'
                + ' data-code="' + lc.code + '"'
                + ' data-amount="' + lc.amount + '"'
                + (String(selected) === String(lc.id) ? ' selected' : '') + '>'
                + lc.code + (lc.description ? ' — ' + lc.description : '')
                + ' ($' + parseFloat(lc.amount).toFixed(2) + ')'
                + '</option>';
        });
        return opts;
    }

    function partOptions(selectedId) {
        var opts = '<option value="">-- select part --</option>';
        _inventoryParts.forEach(function(p) {
            opts += '<option value="' + p.id + '"'
                + ' data-part-num="' + (p.part_number || '') + '"'
                + ' data-desc="' + (p.part_description || '').replace(/"/g,'&quot;') + '"'
                + ' data-cost="' + (p.total_value || 0) + '"'
                + (String(selectedId) === String(p.id) ? ' selected' : '') + '>'
                + (p.part_number || '') + (p.part_description ? ' — ' + p.part_description : '')
                + '</option>';
        });
        return opts;
    }

    function buildMidCell(item) {
        if ((item.item_type || 'labor') === 'part') {
            return '<td><select class="form-select form-select-sm inv-part-select">'
                + partOptions(item.inventory_id || '') + '</select></td>';
        }
        return '<td><select class="form-select form-select-sm inv-labor-code">'
            + laborCodeOptions(item.labor_code_id || '') + '</select></td>';
    }

    function addInvoiceRow(item) {
        item = item || {};
        var typeOpts = ['labor','travel','part'].map(function(t) {
            return '<option value="' + t + '"' + ((item.item_type || 'labor') === t ? ' selected' : '') + '>'
                + t.charAt(0).toUpperCase() + t.slice(1) + '</option>';
        }).join('');

        var row = $('<tr>').html(
            '<td><select class="form-select form-select-sm inv-type">' + typeOpts + '</select></td>'
            + '<td><input class="form-control form-control-sm inv-part-num"'
                + ' value="' + (item.part_number || '') + '" placeholder="Part # / Code"></td>'
            + buildMidCell(item)
            + '<td><input class="form-control form-control-sm inv-desc"'
                + ' value="' + (item.description || '') + '" placeholder="Description"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-qty"'
                + ' value="' + (item.qty || 1) + '" min="0" step="0.5"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-unit-cost"'
                + ' value="' + (item.unit_cost || '') + '" min="0" step="0.01" placeholder="0.00"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-total-cost"'
                + ' value="' + (item.total_cost || '') + '" readonly style="background:#f8f9fa"></td>'
            + '<td class="text-center"><button class="btn btn-sm btn-outline-danger btn-remove-inv-row">'
                + '<i class="fas fa-times"></i></button></td>'
        );
        $('#invoiceItemsTbody').append(row);
        recalcRow(row);
    }

    // Type changed: swap middle cell between labor-code and part-select
    $(document).on('change', '.inv-type', function() {
        var $row = $(this).closest('tr');
        var type = $(this).val();
        var $mid = $row.find('td').eq(2);
        if (type === 'part') {
            $mid.html('<select class="form-select form-select-sm inv-part-select">' + partOptions('') + '</select>');
        } else {
            $mid.html('<select class="form-select form-select-sm inv-labor-code">' + laborCodeOptions('') + '</select>');
        }
        $row.find('.inv-part-num, .inv-desc, .inv-unit-cost, .inv-total-cost').val('');
        recalcRow($row);
    });

    // Part selected from inventory: auto-fill part#, description, unit cost
    $(document).on('change', '.inv-part-select', function() {
        var $opt = $(this).find(':selected');
        var $row = $(this).closest('tr');
        $row.find('.inv-part-num').val($opt.data('part-num') || '');
        $row.find('.inv-desc').val($opt.data('desc') || '');
        $row.find('.inv-unit-cost').val(parseFloat($opt.data('cost') || 0).toFixed(2));
        recalcRow($row);
    });

    // Labor code selected: auto-fill code and rate
    $(document).on('change', '.inv-labor-code', function() {
        var $opt = $(this).find(':selected');
        var $row = $(this).closest('tr');
        if ($opt.data('amount') !== undefined && $opt.data('amount') !== '') {
            $row.find('.inv-unit-cost').val(parseFloat($opt.data('amount')).toFixed(2));
        }
        if ($opt.data('code')) $row.find('.inv-part-num').val($opt.data('code'));
        recalcRow($row);
    });

    // ── Recalc row total on qty/unit change ───────────────────
    $(document).on('input', '.inv-qty, .inv-unit-cost', function () {
        recalcRow($(this).closest('tr'));
    });

    function recalcRow($row) {
        var qty  = parseFloat($row.find('.inv-qty').val())       || 0;
        var unit = parseFloat($row.find('.inv-unit-cost').val()) || 0;
        $row.find('.inv-total-cost').val((qty * unit).toFixed(2));
        recalcTotal();
    }

    function recalcTotal() {
        var total = 0;
        $('#invoiceItemsTbody .inv-total-cost').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#invoiceGrandTotal').text('$' + total.toFixed(2));
    }

    // ── Remove row ────────────────────────────────────────────
    $(document).on('click', '.btn-remove-inv-row', function () {
        $(this).closest('tr').remove();
        recalcTotal();
    });

    // ── Save invoice ──────────────────────────────────────────
    $('#btnSaveInvoice').on('click', function () {
        if (!_currentWoId) return;
        var $btn = $(this).prop('disabled', true).text('Saving…');

        // Collect items
        var items = [];
        $('#invoiceItemsTbody tr').each(function () {
            var $r = $(this);
            var $lcSel = $r.find('.inv-labor-code');
            items.push({
                item_type:      $r.find('.inv-type').val(),
                part_number:    $r.find('.inv-part-num').val(),
                labor_code_id:  $lcSel.val() || null,
                part_labor_code: $lcSel.find(':selected').data('code') || '',
                description:    $r.find('.inv-desc').val(),
                qty:            parseFloat($r.find('.inv-qty').val())       || 1,
                unit_cost:      parseFloat($r.find('.inv-unit-cost').val()) || 0,
                total_cost:     parseFloat($r.find('.inv-total-cost').val())|| 0,
            });
        });

        $.ajax({
            url: '<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/save',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                problem_notes:      $('#invoiceProblemNotes').val(),
                invoice_note:       $('#invoiceInvoiceNote').val(),
                service_notes:      $('#invoiceServiceNotes').val(),
                customer_sig_name:  $('#custSigName').val(),
                customer_sig_image: getSigDataUrl(window._custSigCanvas),
                tech_sig_name:      $('#techSigName').val(),
                tech_sig_image:     getSigDataUrl(window._techSigCanvas),
                items: items,
                '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val(),
            }),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire('Saved', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message || 'Save failed', 'error');
                }
            },
            error: function () { Swal.fire('Error', 'Save failed', 'error'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Invoice'); }
        });
    });
});

// ── Status filter ─────────────────────────────────────────────
// =====================================================================
// SIGNATURE PAD HELPERS
// =====================================================================
window._custSigCanvas = null;
window._techSigCanvas = null;
window._custSigCtx    = null;
window._techSigCtx    = null;

function initSigPad(canvasId) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    var ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
    var drawing = false;
    function getPos(e) {
        var r = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length) {
            return { x: e.touches[0].clientX - r.left, y: e.touches[0].clientY - r.top };
        }
        return { x: e.clientX - r.left, y: e.clientY - r.top };
    }
    canvas.addEventListener('mousedown',  function(e) { drawing=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  function(e) { if(!drawing) return; var p=getPos(e); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#000'; ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function()  { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseleave', function()  { drawing=false; });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e) { e.preventDefault(); if(!drawing) return; var p=getPos(e); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#000'; ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend',   function()  { drawing=false; ctx.beginPath(); });
    return ctx;
}

function clearSig(ctx) {
    if (!ctx) return;
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
}

function getSigDataUrl(canvas) {
    if (!canvas) return '';
    var blank = document.createElement('canvas');
    blank.width  = canvas.width;
    blank.height = canvas.height;
    if (canvas.toDataURL() === blank.toDataURL()) return '';
    return canvas.toDataURL('image/png');
}

function restoreSig(ctx, dataUrl) {
    if (!ctx || !dataUrl) return;
    var img = new Image();
    img.onload = function() {
        var r = window.devicePixelRatio || 1;
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.drawImage(img, 0, 0, ctx.canvas.width / r, ctx.canvas.height / r);
    };
    img.src = dataUrl;
}

// Re-init sig pads every time modal opens (canvas sizing requires visible DOM)
$('#invoiceModal').on('shown.bs.modal', function() {
    window._custSigCanvas = document.getElementById('custSigCanvas');
    window._techSigCanvas = document.getElementById('techSigCanvas');
    window._custSigCtx    = initSigPad('custSigCanvas');
    window._techSigCtx    = initSigPad('techSigCanvas');
});

$(document).on('click', '#btnClearCustSig', function() { clearSig(window._custSigCtx); });
$(document).on('click', '#btnClearTechSig', function() { clearSig(window._techSigCtx); });


function filterStatus(mode) {
    ['btnOpen', 'btnAll', 'btnClosed'].forEach(function (id) {
        document.getElementById(id).className = 'btn btn-outline-secondary';
    });
    var activeBtn = { open: 'btnOpen', all: 'btnAll', closed: 'btnClosed' }[mode];
    if (activeBtn) document.getElementById(activeBtn).className = 'btn btn-primary active';

    $.fn.dataTable.ext.search = [];
    if (mode !== 'all') {
        $.fn.dataTable.ext.search.push(function (settings, data, idx) {
            var row = _woTable.row(idx).node();
            var st  = $(row).data('status') || '';
            if (mode === 'open')   return st === 'open';
            if (mode === 'closed') return st === 'closed' || st === 'completed';
            return true;
        });
    }
    _woTable.draw();
}
</script>
<?= $this->endSection() ?>
