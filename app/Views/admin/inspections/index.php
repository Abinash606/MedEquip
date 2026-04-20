<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Inspection Reports</h3>
</div>

<div class="content">

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-1">Total Inspections</div>
                <h3 class="fw-bold mb-0"><?= esc($inspectionsCount ?? 0) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-1">Sites</div>
                <h3 class="fw-bold mb-0"><?= esc($sitesCount ?? 0) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-1">Customers</div>
                <h3 class="fw-bold mb-0"><?= esc($customersCount ?? 0) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-1">Equipment</div>
                <h3 class="fw-bold mb-0"><?= esc($equipmentCount ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <!-- Inspection Report Table -->
    <div class="glass-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Inspection Report Overview</h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" id="btnInspOpen"   class="btn btn-primary active"        onclick="filterInspStatus('open')">Open</button>
                    <button type="button" id="btnInspAll"    class="btn btn-outline-secondary"     onclick="filterInspStatus('all')">All</button>
                    <button type="button" id="btnInspClosed" class="btn btn-outline-secondary"     onclick="filterInspStatus('closed')">Closed</button>
                </div>
                <button class="btn btn-sm btn-outline-secondary" id="btnExportCsv">
                    <i class="fa-solid fa-file-csv me-1"></i> Export CSV
                </button>
                <button class="btn btn-sm btn-primary" id="btnExportPdf">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" id="filterSite" class="form-control form-control-sm" placeholder="Filter by site...">
            </div>
            <div class="col-md-3">
                <input type="text" id="filterCustomer" class="form-control form-control-sm" placeholder="Filter by customer...">
            </div>
            <div class="col-md-2">
                <select id="filterResult" class="form-select form-select-sm">
                    <option value="">All Results</option>
                    <option value="Pass">Pass</option>
                    <option value="Fail">Fail</option>
                    <option value="Repair">Repair</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateFrom" class="form-control form-control-sm" title="From date">
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateTo" class="form-control form-control-sm" title="To date">
            </div>
        </div>

        <div class="table-responsive">
            <table id="reportsTable" class="table table-hover service-table align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>Inspection ID</th>
                        <th>Pass/Fail</th>
                        <th>Customer / Site</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>S/N</th>
                        <th>Action Performed</th>
                        <th>Asset #</th>
                        <th>Department</th>
                        <th>Room</th>
                        <th>EST</th>
                        <th>CAL</th>
                        <th>Tech</th>
                        <th>Inspection Date</th>
                        <th>Notes</th>
                        <th>Report</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(function () {
    var _inspStatus = 'open';

    var reportsTable = $('#reportsTable').DataTable({
        ajax: {
            url: '<?= site_url('admin/inspection-reports/list') ?>',
            dataSrc: 'data'
        },
        pageLength: 25,
        order: [[13, 'desc']],
        columns: [
            {
                data: null,
                render: function(row) {
                    if (!row.group_id) return '—';
                    // Link to the site detail page (inspections tab) if site_id is available
                    if (row.site_id) {
                        // Use ?open_tab=inspections which the site detail page already handles
                        var url = '<?= site_url('admin/sites/') ?>' + row.site_id + '?open_tab=inspections';
                        return '<a href="' + url + '" class="t-pill" style="font-size:11px;text-decoration:none;cursor:pointer;" '
                            + 'title="Open inspection on site detail page">' + row.group_id + '</a>';
                    }
                    return '<span class="t-pill" style="font-size:11px;">' + row.group_id + '</span>';
                }
            },
            {
                data: 'result',
                render: function(d) {
                    var s = (d || '').toLowerCase();
                    var cls = s === 'pass' ? 'bg-success'
                            : s === 'fail' ? 'bg-danger'
                            : s === 'repair' ? 'bg-warning text-dark'
                            : s === 'in progress' ? 'bg-info text-dark'
                            : 'bg-secondary';
                    return '<span class="badge ' + cls + '">' + (d || 'In Progress') + '</span>';
                }
            },
            { data: null, render: function(row) { return (row.customer_name||'—') + '<br><small class="text-muted">' + (row.site_name||'—') + '</small>'; } },
            { data: 'model',  defaultContent: '—' },
            { data: 'device_type', defaultContent: '—' },
            { data: 'serial_number', defaultContent: '—' },
            { data: 'action_performed', defaultContent: '—' },
            { data: 'asset_tag', defaultContent: '—' },
            { data: 'department', defaultContent: '—' },
            { data: 'room', defaultContent: '—' },
            { data: 'est', render: function(d){ return d=='1'||d=='Yes'?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-secondary">No</span>'; } },
            { data: 'cal', render: function(d){ return d=='1'||d=='Yes'?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-secondary">No</span>'; } },
            { data: 'technician_name', defaultContent: '—' },
            { data: 'inspection_date', render: function(d){ return d ? d.substring(0,10) : '—'; } },
            { data: 'notes', render: function(d){ return d ? '<span title="'+$('<div/>').text(d).html()+'">'+d.substring(0,40)+(d.length>40?'…':'')+'</span>' : '—'; } },
            {
                data: null, orderable: false,
                render: function(row) {
                    if (!row.group_id) return '—';
                    return '<button class="btn btn-sm btn-outline-primary" onclick="previewAdminReport(\''+row.group_id+'\')" title="Preview Report"><i class="fa-solid fa-file-pdf"></i></button>';
                }
            }
        ]
    });

    // Open/closed toggle
    window.filterInspStatus = function(mode) {
        _inspStatus = mode;
        ['btnInspOpen','btnInspAll','btnInspClosed'].forEach(function(id){
            document.getElementById(id).className = 'btn btn-sm btn-outline-secondary';
        });
        var activeMap = {open:'btnInspOpen', all:'btnInspAll', closed:'btnInspClosed'};
        document.getElementById(activeMap[mode]).className = 'btn btn-sm btn-primary active';
        applyFilters();
    };

    // Custom filters
    function applyFilters() {
        $.fn.dataTable.ext.search = [];
        var site     = $('#filterSite').val().toLowerCase();
        var customer = $('#filterCustomer').val().toLowerCase();
        var result   = $('#filterResult').val().toLowerCase();
        var from     = $('#filterDateFrom').val();
        var to       = $('#filterDateTo').val();

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = reportsTable.row(dataIndex).data();
            if (!row) return true;
            // Open/closed status filter
            var res = (row.result || '').toLowerCase();
            var isClosed = ['pass','fail','repair','completed'].includes(res);
            // 'in progress' / '' / any non-result status = open
            if (_inspStatus === 'open'   && isClosed) return false;
            if (_inspStatus === 'closed' && !isClosed) return false;
            // Text filters
            if (site     && !(row.site_name||'').toLowerCase().includes(site))         return false;
            if (customer && !(row.customer_name||'').toLowerCase().includes(customer)) return false;
            if (result   && (row.result||'').toLowerCase() !== result)                 return false;
            if (from && row.inspection_date && row.inspection_date.substring(0,10) < from) return false;
            if (to   && row.inspection_date && row.inspection_date.substring(0,10) > to)   return false;
            return true;
        });
        reportsTable.draw();
    }

    $('#filterSite, #filterCustomer, #filterResult, #filterDateFrom, #filterDateTo').on('input change', applyFilters);
    reportsTable.on('init', function() { filterInspStatus('open'); });

    // Export CSV — build from DataTable rows
    $('#btnExportCsv').on('click', function () {
        var rows = reportsTable.rows({search:'applied'}).data().toArray();
        if (!rows.length) { alert('No data to export.'); return; }
        var h = ['Pass/Fail','Customer','Site','Model','Type','SN','Action','Asset','Dept','Room','EST','CAL','Tech','Date','Notes'];
        var csv = h.join(',') + '\n';
        rows.forEach(function(r) {
            var line = [r.result,r.customer_name,r.site_name,r.model,r.device_type,r.serial_number,
                r.action_performed,r.asset_tag,r.department,r.room,r.est,r.cal,
                r.technician_name,(r.inspection_date||'').substring(0,10),(r.notes||'').replace(/,/g,' ')]
                .map(function(v){ return '"'+(v||'').toString().replace(/"/g,'""')+'"'; }).join(',');
            csv += line + '\n';
        });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv],{type:'text/csv'}));
        a.download = 'inspection_report_' + new Date().toISOString().substring(0,10) + '.csv';
        a.click();
    });

    // Export PDF — open print window
    $('#btnExportPdf').on('click', function () {
        var rows = reportsTable.rows({search:'applied'}).data().toArray();
        if (!rows.length) { alert('No data to export.'); return; }
        var html = '<html><head><title>Inspection Report</title><style>body{font-family:Arial,sans-serif;font-size:10px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:3px 5px;}th{background:#eee;}</style></head><body>';
        html += '<h3>Inspection Report</h3><table><thead><tr><th>Pass/Fail</th><th>Customer/Site</th><th>Model</th><th>S/N</th><th>Asset#</th><th>Action</th><th>Dept</th><th>Room</th><th>EST</th><th>CAL</th><th>Tech</th><th>Date</th><th>Notes</th></tr></thead><tbody>';
        rows.forEach(function(r) {
            html += '<tr><td><b>'+(r.result||'')+'</b></td><td>'+(r.customer_name||'')+' / '+(r.site_name||'')+'</td><td>'+(r.model||'')+'</td><td>'+(r.serial_number||'')+'</td><td>'+(r.asset_tag||'')+'</td><td>'+(r.action_performed||'')+'</td><td>'+(r.department||'')+'</td><td>'+(r.room||'')+'</td><td>'+(r.est||'')+'</td><td>'+(r.cal||'')+'</td><td>'+(r.technician_name||'')+'</td><td>'+((r.inspection_date||'').substring(0,10))+'</td><td>'+(r.notes||'')+'</td></tr>';
        });
        html += '</tbody></table></body></html>';
        var w = window.open('','_blank'); w.document.write(html); w.document.close(); w.print();
    });
});

// ── Report Preview Modal ────────────────────────────────────────────────
function previewAdminReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('adminReportPreviewModal'));
    document.getElementById('adminReportPreviewBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();
    fetch('<?= site_url('admin/inspections/reportPreview') ?>/' + groupId, {
        headers: { 'Accept': 'text/html' }
    }).then(function(r) { return r.text(); })
    .then(function(html) {
        // Show in iframe inside modal
        var iframe = document.createElement('iframe');
        iframe.style.width = '100%';
        iframe.style.height = '70vh';
        iframe.style.border = 'none';
        document.getElementById('adminReportPreviewBody').innerHTML = '';
        document.getElementById('adminReportPreviewBody').appendChild(iframe);
        iframe.contentDocument.open();
        iframe.contentDocument.write(html);
        iframe.contentDocument.close();
    }).catch(function() {
        document.getElementById('adminReportPreviewBody').innerHTML = '<div class="alert alert-danger">Failed to load report.</div>';
    });
    document.getElementById('adminReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('admin/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}
</script>

<!-- Admin Report Preview Modal -->
<div class="modal fade" id="adminReportPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="adminReportPreviewBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="adminReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
