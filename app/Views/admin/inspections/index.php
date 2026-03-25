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
            <div class="d-flex gap-2">
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
    var reportsTable = $('#reportsTable').DataTable({
        ajax: {
            url: '<?= site_url('admin/inspection-reports/list') ?>',
            dataSrc: 'data'
        },
        pageLength: 25,
        order: [[12, 'desc']],
        columns: [
            {
                data: 'result',
                render: function(d) {
                    var cls = d && d.toLowerCase() === 'pass' ? 'bg-success' : (d && d.toLowerCase() === 'fail' ? 'bg-danger' : 'bg-warning text-dark');
                    return '<span class="badge ' + cls + '">' + (d||'—') + '</span>';
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
                    return '<a href="<?= site_url('admin/inspections/reportPdf') ?>/' + row.group_id + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Download PDF"><i class="fa-solid fa-file-pdf"></i></a>';
                }
            }
        ]
    });

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

    // Export CSV
    $('#btnExportCsv').on('click', function () {
        reportsTable.button('.buttons-csv').trigger();
    });
});
</script>
<?= $this->endSection() ?>
