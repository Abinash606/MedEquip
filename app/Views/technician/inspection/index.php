<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Inspection Reports</h3>
        <p class="text-muted small mb-0">View and manage all your inspection records.</p>
    </div>
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
                <button type="button" id="btnTechInspOpen"   class="btn btn-primary active"    onclick="techInspFilter('open')">Open</button>
                <button type="button" id="btnTechInspAll"    class="btn btn-outline-secondary" onclick="techInspFilter('all')">All</button>
                <button type="button" id="btnTechInspClosed" class="btn btn-outline-secondary" onclick="techInspFilter('closed')">Closed</button>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportTableCSV()">
                <i class="fa-solid fa-file-csv me-1"></i> Export CSV
            </button>
            <button class="btn btn-sm btn-primary" onclick="exportTablePDF()">
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
        <table id="techReportsTable" class="table table-hover service-table align-middle" style="width:100%">
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
function exportTableCSV() {
    var dt = $('#techReportsTable').DataTable();
    var rows = dt.rows({search:'applied'}).data().toArray();
    if (!rows.length) { alert('No data to export.'); return; }
    var h = ['Inspection ID','Pass/Fail','Customer','Site','Model','Type','SN','Action','Asset','Dept','Room','EST','CAL','Tech','Date','Notes'];
    var csv = h.join(',') + '\n';
    rows.forEach(function(r) {
        var l = [r.group_id,r.result,r.customer_name,r.site_name,r.model,r.device_type,r.serial_number,
                 r.action_performed,r.asset_tag,r.department,r.room,r.est,r.cal,
                 r.technician_name,(r.inspection_date||'').substring(0,10),(r.notes||'').replace(/,/g,' ')]
            .map(function(v){ return '"'+(v||'').toString().replace(/"/g,'""')+'"'; }).join(',');
        csv += l + '\n';
    });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv],{type:'text/csv'}));
    a.download = 'inspection_report_' + new Date().toISOString().substring(0,10) + '.csv';
    a.click();
}

function exportTablePDF() {
    var dt = $('#techReportsTable').DataTable();
    var rows = dt.rows({search:'applied'}).data().toArray();
    if (!rows.length) { alert('No data to export.'); return; }
    var html = '<html><head><title>Inspection Report</title><style>body{font-family:Arial,sans-serif;font-size:10px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:3px 5px;}th{background:#eee;}</style></head><body>';
    html += '<h3>Inspection Report</h3><table><thead><tr><th>ID</th><th>Pass/Fail</th><th>Customer/Site</th><th>Model</th><th>S/N</th><th>Asset#</th><th>Action</th><th>Dept</th><th>Room</th><th>EST</th><th>CAL</th><th>Tech</th><th>Date</th><th>Notes</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        html += '<tr><td>'+(r.group_id||'').substring(0,20)+'</td><td><b>'+(r.result||'')+'</b></td><td>'+(r.customer_name||'')+' / '+(r.site_name||'')+'</td><td>'+(r.model||'')+'</td><td>'+(r.serial_number||'')+'</td><td>'+(r.asset_tag||'')+'</td><td>'+(r.action_performed||'')+'</td><td>'+(r.department||'')+'</td><td>'+(r.room||'')+'</td><td>'+(r.est||'')+'</td><td>'+(r.cal||'')+'</td><td>'+(r.technician_name||'')+'</td><td>'+((r.inspection_date||'').substring(0,10))+'</td><td>'+(r.notes||'')+'</td></tr>';
    });
    html += '</tbody></table></body></html>';
    var w = window.open('','_blank'); w.document.write(html); w.document.close(); w.print();
}

$(function() {
    var _techInspMode = 'open';

    var techTable = $('#techReportsTable').DataTable({
        ajax: {
            url: '<?= site_url('technician/inspections/listData') ?>',
            dataSrc: 'data'
        },
        pageLength: 25,
        order: [[13, 'desc']],
        columns: [
            { data: null, render: function(row) {
                if (!row.group_id) return '—';
                return '<span class="t-pill" style="font-size:11px;">' + row.group_id.substring(0, 22) + '</span>';
            }},
            {
                data: 'result',
                render: function(d) {
                    var s = (d || '').toLowerCase();
                    var cls = s === 'pass' ? 'bg-success' : (s === 'fail' ? 'bg-danger' : 'bg-warning text-dark');
                    return '<span class="badge ' + cls + '">' + (d || '—') + '</span>';
                }
            },
            { data: null, render: function(row) {
                return '<div class="fw-medium">' + (row.customer_name || '—') + '</div>' +
                       '<div class="text-muted small">' + (row.site_name || '—') + '</div>';
            }},
            { data: 'model', defaultContent: '—' },
            { data: 'device_type', defaultContent: '—' },
            { data: 'serial_number', defaultContent: '—' },
            { data: 'action_performed', defaultContent: '—' },
            { data: 'asset_tag', defaultContent: '—' },
            { data: 'department', defaultContent: '—' },
            { data: 'room', defaultContent: '—' },
            { data: 'est', render: function(d) {
                return (d == '1' || d == 'Yes') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
            }},
            { data: 'cal', render: function(d) {
                return (d == '1' || d == 'Yes') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
            }},
            { data: 'technician_name', defaultContent: '—' },
            { data: 'inspection_date', render: function(d) { return d ? d.substring(0, 10) : '—'; } },
            { data: 'notes', render: function(d) {
                if (!d) return '—';
                var safe = $('<div/>').text(d).html();
                return d.length > 40 ? '<span title="' + safe + '">' + d.substring(0, 40) + '…</span>' : d;
            }},
            { data: null, orderable: false, render: function(row) {
                if (!row.group_id) return '—';
                return '<button class="btn btn-sm btn-outline-primary" onclick="previewTechInspReport(\'' + row.group_id + '\')" title="Preview Report"><i class="fa-solid fa-file-pdf"></i></button>';
            }}
        ]
    });

    // Open/All/Closed toggle
    window.techInspFilter = function(mode) {
        _techInspMode = mode;
        ['btnTechInspOpen','btnTechInspAll','btnTechInspClosed'].forEach(function(id) {
            document.getElementById(id).className = 'btn btn-sm btn-outline-secondary';
        });
        var map = {open:'btnTechInspOpen', all:'btnTechInspAll', closed:'btnTechInspClosed'};
        if (map[mode]) document.getElementById(map[mode]).className = 'btn btn-sm btn-primary active';
        applyFilters();
    };

    function applyFilters() {
        $.fn.dataTable.ext.search = [];
        var site     = $('#filterSite').val().toLowerCase();
        var customer = $('#filterCustomer').val().toLowerCase();
        var result   = $('#filterResult').val().toLowerCase();
        var from     = $('#filterDateFrom').val();
        var to       = $('#filterDateTo').val();

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = techTable.row(dataIndex).data();
            if (!row) return true;
            var res = (row.result || '').toLowerCase();
            var isClosed = ['pass','fail','repair','completed'].includes(res);
            if (_techInspMode === 'open'   && isClosed)  return false;
            if (_techInspMode === 'closed' && !isClosed) return false;
            if (site     && !(row.site_name     || '').toLowerCase().includes(site))     return false;
            if (customer && !(row.customer_name || '').toLowerCase().includes(customer)) return false;
            if (result   && (row.result || '').toLowerCase() !== result.toLowerCase())   return false;
            var dt = (row.inspection_date || '').substring(0, 10);
            if (from && dt && dt < from) return false;
            if (to   && dt && dt > to)   return false;
            return true;
        });
        techTable.draw();
    }

    $('#filterSite, #filterCustomer, #filterResult, #filterDateFrom, #filterDateTo').on('input change', applyFilters);
    techTable.on('init', function() { techInspFilter('open'); });
});

// ── Inline Report Preview ─────────────────────────────────────────────
function previewTechInspReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('techInspReportModal'));
    document.getElementById('techInspReportBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();
    fetch('<?= site_url('technician/inspections/reportPdf') ?>/' + groupId)
        .then(function(r){ return r.text(); })
        .then(function(html) {
            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'width:100%;height:72vh;border:none;';
            document.getElementById('techInspReportBody').innerHTML = '';
            document.getElementById('techInspReportBody').appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
        }).catch(function() {
            document.getElementById('techInspReportBody').innerHTML =
                '<div class="alert alert-danger m-3">Failed to load report. Please try again.</div>';
        });
    document.getElementById('techInspReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('technician/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}
</script>

<!-- Technician Inspection Report Preview Modal -->
<div class="modal fade" id="techInspReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="techInspReportBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="techInspReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
