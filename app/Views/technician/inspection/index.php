<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<style>
/* ===== DARK THEME: Modals & DataTables ===== */
.modal-content {
    background: #0E1630 !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    color: #E9EDFF !important;
    border-radius: 16px !important;
}
.modal-header {
    background: linear-gradient(135deg, rgba(124,58,237,.9), rgba(34,211,238,.8)) !important;
    border-bottom: none !important;
    border-radius: 16px 16px 0 0 !important;
}
.modal-footer {
    border-top: 1px solid rgba(255,255,255,.08) !important;
    background: rgba(7,10,18,.4) !important;
    border-radius: 0 0 16px 16px !important;
}
.modal-body { background: rgba(14,22,48,.6) !important; }
.modal-title, .modal-body .form-label, .modal-body label,
.modal-body h5, .modal-body p { color: #E9EDFF !important; }
.modal-body .form-control, .modal-body .form-select {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.14) !important;
    color: #E9EDFF !important;
    border-radius: 10px !important;
}
.modal-body .form-control::placeholder { color: rgba(233,237,255,.35) !important; }
.modal-body .form-select option { color: #000 !important; background: #fff !important; }
.modal-body .form-control[readonly] { background: rgba(255,255,255,.03) !important; color: rgba(233,237,255,.5) !important; }
.modal-body .alert { border-radius: 10px !important; }
/* DataTables */
table.dataTable thead th {
    background: rgba(7,10,18,.6) !important;
    color: rgba(233,237,255,.55) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
}
table.dataTable tbody tr { background: transparent !important; }
table.dataTable tbody tr:hover { background: rgba(255,255,255,.04) !important; }
table.dataTable tbody td {
    border-bottom: 1px solid rgba(255,255,255,.05) !important;
    color: #E9EDFF !important;
}
table.dataTable.stripe tbody tr.odd,
table.dataTable.stripe tbody tr.even { background: transparent !important; }
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    color: #E9EDFF !important; border-radius: 8px !important; padding: 4px 8px;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter { color: rgba(233,237,255,.6) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button { color: #E9EDFF !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg,rgba(124,58,237,.8),rgba(34,211,238,.6)) !important;
    border-color: transparent !important; color: #fff !important;
}
/* Buttons in modals */
.modal .btn-primary { background: linear-gradient(90deg,rgba(34,211,238,.9),rgba(124,58,237,.8)) !important; border: none !important; color: #fff !important; }
.modal .btn-danger  { background: rgba(239,68,68,.85) !important; border: none !important; }
.modal .btn-outline-secondary { color: rgba(233,237,255,.7) !important; border-color: rgba(255,255,255,.2) !important; }
/* ===== END DARK THEME ===== */
</style>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Inspection Reports</h3>
        <p class="text-muted small mb-0">View and manage all your inspection records.</p>
    </div>
</div>

<div class="content">

    <!-- Filters Row -->
    <div class="glass-card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" id="filterSite" class="form-control form-control-sm" placeholder="Filter by site...">
            </div>
            <div class="col-md-3">
                <input type="text" id="filterCustomer" class="form-control form-control-sm" placeholder="Filter by customer...">
            </div>
            <div class="col-md-2">
                <select id="filterResult" class="form-select form-select-sm">
                    <option value="">All Results</option>
                    <option value="pass">Pass</option>
                    <option value="fail">Fail</option>
                    <option value="repair">Repair</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateFrom" class="form-control form-control-sm" title="From date">
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateTo" class="form-control form-control-sm" title="To date">
            </div>
        </div>
    </div>

    <!-- Inspection Reports Table -->
    <div class="glass-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Inspection Report Overview</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="exportTableCSV()">
                    <i class="fa-solid fa-file-csv me-1"></i> Export
                </button>
                <button class="btn btn-sm btn-primary" onclick="exportTablePDF()">
                    <i class="fa-solid fa-file-pdf me-1"></i> Download as PDF
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="techReportsTable" class="table service-table align-middle" style="width:100%">
                <thead>
                    <tr>
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
    var h = ['Pass/Fail','Customer','Site','Model','Type','SN','Action','Asset','Dept','Room','EST','CAL','Tech','Date','Notes'];
    var csv = h.join(',') + '\n';
    rows.forEach(function(r) {
        var l = [r.result,r.customer_name,r.site_name,r.model,r.device_type,r.serial_number,
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
    html += '<h3>Inspection Report</h3><table><thead><tr><th>Pass/Fail</th><th>Customer/Site</th><th>Model</th><th>S/N</th><th>Asset#</th><th>Action</th><th>Dept</th><th>Room</th><th>EST</th><th>CAL</th><th>Tech</th><th>Date</th><th>Notes</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        html += '<tr><td><b>'+(r.result||'')+'</b></td><td>'+(r.customer_name||'')+' / '+(r.site_name||'')+'</td><td>'+(r.model||'')+'</td><td>'+(r.serial_number||'')+'</td><td>'+(r.asset_tag||'')+'</td><td>'+(r.action_performed||'')+'</td><td>'+(r.department||'')+'</td><td>'+(r.room||'')+'</td><td>'+(r.est||'')+'</td><td>'+(r.cal||'')+'</td><td>'+(r.technician_name||'')+'</td><td>'+((r.inspection_date||'').substring(0,10))+'</td><td>'+(r.notes||'')+'</td></tr>';
    });
    html += '</tbody></table></body></html>';
    var w = window.open('','_blank'); w.document.write(html); w.document.close(); w.print();
}
$(function() {
    var techTable = $('#techReportsTable').DataTable({
        ajax: {
            url: '<?= site_url('technician/inspections/listData') ?>',
            dataSrc: 'data'
        },
        pageLength: 25,
        order: [[12, 'desc']],
        columns: [
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
                return '<a href="<?= site_url('technician/inspections/reportPdf') ?>/' + row.group_id + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Download PDF"><i class="fa-solid fa-file-pdf"></i></a>';
            }}
        ]
    });

    // Custom filter logic
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
            if (site     && !(row.site_name     || '').toLowerCase().includes(site))     return false;
            if (customer && !(row.customer_name || '').toLowerCase().includes(customer)) return false;
            if (result   && (row.result || '').toLowerCase() !== result)                 return false;
            var dt = (row.inspection_date || '').substring(0, 10);
            if (from && dt && dt < from) return false;
            if (to   && dt && dt > to)   return false;
            return true;
        });
        techTable.draw();
    }

    $('#filterSite, #filterCustomer, #filterResult, #filterDateFrom, #filterDateTo').on('input change', applyFilters);
});
</script>
<?= $this->endSection() ?>
