<?= $this->extend('layouts/customer-header') ?>
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
        <h4 class="fw-bold mb-0">Assets</h4>
        <div class="text-muted small">Search, filter, and manage your equipment inventory.</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
            <i class="fa-solid fa-file-excel me-1"></i> Bulk Import
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="fa-solid fa-plus me-2"></i> Add Asset
        </button>
    </div>
</div>

<div class="content">

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Site Filter -->
    <div class="glass-card mb-3 p-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Filter by Site</label>
                <select id="siteFilter" class="form-select form-select-sm">
                    <option value="">All Sites</option>
                    <?php foreach ($sites as $site): ?>
                        <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Search</label>
                <input type="text" id="assetSearch" class="form-control form-control-sm" placeholder="Asset tag, model, serial...">
            </div>
            <div class="col-md-4 text-end">
                <span class="text-muted small" id="assetCountLabel"></span>
            </div>
        </div>
    </div>

    <!-- Site Stats Cards -->
    <div class="row g-3 mb-3" id="siteStatsRow">
        <?php
        // Count equipment per site
        $equipBySite = [];
        foreach ($equipment as $eq) {
            $sid = $eq['site_id'] ?? 0;
            $equipBySite[$sid] = ($equipBySite[$sid] ?? 0) + 1;
        }
        foreach ($sites as $site):
            $cnt = $equipBySite[$site['id']] ?? 0;
        ?>
        <div class="col-md-3">
            <div class="glass-card p-3 site-stat-card h-100" data-site-id="<?= $site['id'] ?>" style="cursor:pointer;">
                <div class="text-muted small text-uppercase fw-bold mb-1"><?= esc($site['name']) ?></div>
                <h3 class="fw-bold mb-0"><?= $cnt ?></h3>
                <div class="small text-muted mt-1">Assets</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Assets Table -->
    <div class="glass-card p-3">
        <div class="table-responsive">
            <table id="assetsTable" class="table custom-table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Device Type</th>
                        <th>Department</th>
                        <th>Site</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php
                        // Build site name lookup
                        $siteMap = [];
                        foreach ($sites as $s) { $siteMap[$s['id']] = $s['name']; }
                        foreach ($equipment as $eq):
                        ?>
                        <tr data-site-id="<?= esc($eq['site_id'] ?? '') ?>">
                            <td class="fw-medium"><?= esc($eq['asset_tag']) ?></td>
                            <td><?= esc($eq['serial_number'] ?? '—') ?></td>
                            <td><?= esc($eq['make'] ?? '—') ?></td>
                            <td><?= esc($eq['model'] ?? '—') ?></td>
                            <td><?= esc($eq['device_type'] ?? '—') ?></td>
                            <td><?= esc($eq['department'] ?? '—') ?></td>
                            <td><?= esc($siteMap[$eq['site_id'] ?? 0] ?? '—') ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger report-issue-btn d-flex gap-2 align-items-center"
                                    data-asset-tag="<?= esc($eq['asset_tag'], 'attr') ?>"
                                    data-make="<?= esc($eq['make'] ?? '', 'attr') ?>"
                                    data-model="<?= esc($eq['model'] ?? '', 'attr') ?>"
                                    data-serial="<?= esc($eq['serial_number'] ?? '', 'attr') ?>"
                                    data-type="<?= esc($eq['device_type'] ?? '', 'attr') ?>"
                                    data-equipment-id="<?= esc($eq['id']) ?>">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Report Issue
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No assets found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reportIssueForm" action="<?= site_url('customer/assets/report-issue') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="equipment_id" id="issue-equipment-id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Report Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asset Tag</label>
                            <input type="text" class="form-control" id="issue-asset-tag" name="asset_tag" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Device Type</label>
                            <input type="text" class="form-control" id="issue-type" name="device_type" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Make</label>
                            <input type="text" class="form-control" id="issue-make" name="make" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Model</label>
                            <input type="text" class="form-control" id="issue-model" name="model" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Serial Number</label>
                            <input type="text" class="form-control" id="issue-serial" name="serial_number" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Issue Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="issue_description" rows="4" required placeholder="Describe the issue in detail..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Priority</label>
                            <select class="form-select" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-paper-plane me-1"></i>Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Import Modal -->


<!-- Add Equipment Modal -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('customer/assets/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Site <span class="text-danger">*</span></label>
                            <select class="form-select" name="site_id" required>
                                <option value="">-- Select Site --</option>
                                <?php foreach ($sites as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asset Tag</label>
                            <input type="text" class="form-control" name="asset_tag" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="serial_number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Make <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="make" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="model" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Device Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="device_type" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Department</label>
                            <input type="text" class="form-control" name="department">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                <option value="ready">Operational</option>
                                <option value="need_attention">Needs Attention</option>
                                <option value="out_of_service">Out of Service</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
    // Initialize DataTable
    var table = $('#assetsTable').DataTable({
        pageLength: 25,
        dom: 'tip',
        order: [[0,'asc']]
    });

    // Site filter via stat cards
    $(document).on('click', '.site-stat-card', function() {
        var siteId = $(this).data('site-id');
        $('#siteFilter').val(siteId).trigger('change');
        $('.site-stat-card').css('border-color','');
        $(this).css('border-color','rgba(34,211,238,.6)');
    });

    // Site dropdown filter
    $('#siteFilter').on('change', function() {
        var val = $(this).val();
        if (!val) {
            table.rows().every(function(){ this.nodes()[0] && (this.nodes()[0].style.display=''); });
            table.draw();
            $.fn.dataTable.ext.search = [];
        } else {
            $.fn.dataTable.ext.search = [];
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var row = table.row(dataIndex).node();
                return $(row).data('site-id') == val;
            });
        }
        table.draw();
        var showing = table.rows({search:'applied'}).count();
        $('#assetCountLabel').text(showing + ' asset(s)');
    });

    // Text search
    $('#assetSearch').on('input', function() {
        table.search(this.value).draw();
        $('#assetCountLabel').text(table.rows({search:'applied'}).count() + ' asset(s)');
    });

    // Update count label initially
    $('#assetCountLabel').text(table.rows().count() + ' asset(s)');

    // Report Issue button - auto-fill modal fields
    $(document).on('click', '.report-issue-btn', function() {
        var btn = $(this);
        $('#issue-equipment-id').val(btn.data('equipment-id'));
        $('#issue-asset-tag').val(btn.data('asset-tag'));
        $('#issue-make').val(btn.data('make'));
        $('#issue-model').val(btn.data('model'));
        $('#issue-serial').val(btn.data('serial'));
        $('#issue-type').val(btn.data('type'));
        $('#reportIssueModal').modal('show');
    });

    // File preview for bulk import
    $('input[name="excel_file"]').on('change', function() {
        if (this.files && this.files[0]) {
            var name = this.files[0].name;
            var size = (this.files[0].size / 1024).toFixed(1) + ' KB';
            $('#importPreviewText').text('Selected: ' + name + ' (' + size + ')');
            $('#importPreview').removeClass('d-none');
        }
    });
});
</script>

<!-- Bulk Import Modal - Customer Portal -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:540px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-csv me-2 text-success"></i>Import Equipment from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert" style="background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);border-radius:10px;">
                    <p class="small fw-semibold mb-1">Required CSV column headers (Row 1):</p>
                    <code style="font-size:11px;display:block;padding:6px 8px;border-radius:6px;background:rgba(0,0,0,.08);line-height:1.8;">Make, Model, Device Type, Asset Tag, Serial Number, Department, Location Or Room</code>
                    <p class="small mb-0 mt-2 text-muted">Headers are case-insensitive. Asset Tag optional (auto-generated). "N/A" serial = blank.</p>
                </div>
                <div id="custImportAlert" class="d-none alert" style="border-radius:10px;"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
                    <select class="form-select" id="custImportSiteId">
                        <option value="">-- Choose a site --</option>
                        <?php foreach ($sites as $site): ?>
                            <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">CSV File <span class="text-danger">*</span></label>
                    <div id="custDropZone" style="border:2px dashed #ccc;border-radius:10px;padding:24px;text-align:center;cursor:pointer;" onclick="document.getElementById('custImportFile').click()">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                        <p class="mb-1 fw-semibold">Click to choose file or drag &amp; drop</p>
                        <p class="small mb-0 text-muted" id="custImportFileName">CSV files only (.csv)</p>
                    </div>
                    <input type="file" id="custImportFile" accept=".csv" style="display:none;">
                </div>
                <div id="custImportProgressWrap" class="d-none">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted" id="custImportProgressLabel">Uploading...</span>
                        <span class="small fw-bold" id="custImportProgressPct">0%</span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:8px;">
                        <div id="custImportProgressBar" class="progress-bar bg-success" role="progressbar" style="width:0%;transition:width .3s;border-radius:8px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="custImportBtn" disabled><i class="fa-solid fa-upload me-1"></i> Import Equipment</button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var fi=document.getElementById('custImportFile'),dz=document.getElementById('custDropZone'),
        btn=document.getElementById('custImportBtn'),al=document.getElementById('custImportAlert'),
        nm=document.getElementById('custImportFileName'),sl=document.getElementById('custImportSiteId'),
        pw=document.getElementById('custImportProgressWrap'),pb=document.getElementById('custImportProgressBar'),
        pp=document.getElementById('custImportProgressPct'),pl=document.getElementById('custImportProgressLabel');
    var sf=null;
    function showAl(t,h){al.className='alert alert-'+t;al.innerHTML=h;al.classList.remove('d-none');}
    function hideAl(){al.classList.add('d-none');}
    function setP(p,l){pw.classList.remove('d-none');pb.style.width=p+'%';pp.textContent=p+'%';if(l)pl.textContent=l;}
    function onFile(f){
        if(!f)return;
        if(f.name.split('.').pop().toLowerCase()!=='csv'){showAl('danger','<i class="fa-solid fa-triangle-exclamation me-2"></i>Only <strong>.csv</strong> files accepted. Save your Excel as CSV (comma-delimited) first.');sf=null;btn.disabled=true;return;}
        sf=f;hideAl();nm.textContent=f.name+' ('+(f.size/1024).toFixed(1)+' KB)';dz.style.borderColor='#198754';btn.disabled=false;
    }
    fi.addEventListener('change',function(){onFile(this.files[0]);});
    dz.addEventListener('dragover',function(e){e.preventDefault();this.style.borderColor='#0d6efd';});
    dz.addEventListener('dragleave',function(){this.style.borderColor='#ccc';});
    dz.addEventListener('drop',function(e){e.preventDefault();this.style.borderColor='#ccc';onFile(e.dataTransfer.files[0]);});
    document.getElementById('bulkImportModal').addEventListener('hidden.bs.modal',function(){
        sf=null;fi.value='';btn.disabled=true;hideAl();pw.classList.add('d-none');pb.style.width='0%';nm.textContent='CSV files only (.csv)';dz.style.borderColor='#ccc';
    });
    btn.addEventListener('click',function(){
        if(!sf)return;
        var siteId=sl.value;
        if(!siteId){showAl('warning','Please select a site first.');return;}
        btn.disabled=true;btn.innerHTML='<i class="fa-solid fa-spinner fa-spin me-1"></i> Importing...';
        hideAl();setP(5,'Preparing...');
        var fd=new FormData();
        fd.append('excel_file',sf);fd.append('site_id',siteId);
        var cm=document.cookie.match(/csrf_cookie_name=([^;]+)/);
        if(cm)fd.append('csrf_test_name',decodeURIComponent(cm[1]));
        var xhr=new XMLHttpRequest();
        xhr.upload.addEventListener('progress',function(e){if(e.lengthComputable)setP(Math.round((e.loaded/e.total)*70),'Uploading...');});
        xhr.addEventListener('load',function(){
            setP(100,'Done!');
            btn.innerHTML='<i class="fa-solid fa-upload me-1"></i> Import Equipment';btn.disabled=false;
            try{
                var r=JSON.parse(xhr.responseText);
                if(r.success){showAl('success','<i class="fa-solid fa-check-circle me-2"></i><strong>'+r.imported+' records imported</strong>'+(r.skipped>0?' &middot; '+r.skipped+' skipped':'')+' <br><small>Reloading page...</small>');setTimeout(function(){location.reload();},2200);}
                else{showAl('danger','<i class="fa-solid fa-triangle-exclamation me-2"></i>'+(r.message||'Import failed.'));pw.classList.add('d-none');}
            }catch(e){showAl('danger','Server error (HTTP '+xhr.status+'). '+xhr.responseText.substring(0,120));pw.classList.add('d-none');}
        });
        xhr.addEventListener('error',function(){btn.innerHTML='<i class="fa-solid fa-upload me-1"></i> Import Equipment';btn.disabled=false;showAl('danger','Network error. Please try again.');pw.classList.add('d-none');});
        xhr.open('POST','<?= site_url("customer/assets/bulk-import") ?>');
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.send(fd);
    });
})();
</script>

<?= $this->endSection() ?>
