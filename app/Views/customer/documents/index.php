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
table.dataTable thead th {
    background: rgba(7,10,18,.6) !important;
    color: rgba(233,237,255,.55) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
}
table.dataTable tbody tr { background: transparent !important; }
table.dataTable tbody tr:hover { background: rgba(255,255,255,.04) !important; }
table.dataTable tbody td { border-bottom: 1px solid rgba(255,255,255,.05) !important; color: #E9EDFF !important; }
table.dataTable.stripe tbody tr.odd, table.dataTable.stripe tbody tr.even { background: transparent !important; }
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
.modal .btn-primary { background: linear-gradient(90deg,rgba(34,211,238,.9),rgba(124,58,237,.8)) !important; border: none !important; color: #fff !important; }
.modal .btn-outline-secondary { color: rgba(233,237,255,.7) !important; border-color: rgba(255,255,255,.2) !important; }
/* ===== END DARK THEME ===== */
</style>
<!-- Documents section -->
    <div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Documents &amp; Reports</h3>
        <p class="text-muted small mb-0">Access inspection reports and compliance documents.</p>
    </div>
</div>
<div class="content">
<section id="documents" class="view-section active">
        <div class="glass-card">
            <h5 class="fw-bold mb-3">Documents &amp; Reports</h5>
            <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-bold">Compliance Report Q3</div>
                        <div class="text-muted small">Oct&nbsp;12 • 2.4&nbsp;MB</div>
                    </div>
                    <button class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i></button>
                </li>
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-bold">Invoice #INV‑2025‑001</div>
                        <div class="text-muted small">Oct&nbsp;10 • Paid</div>
                    </div>
                    <button class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i></button>
                </li>
                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <div class="fw-bold">Service Logs 2024</div>
                        <div class="text-muted small">Sep&nbsp;20 • 1.2&nbsp;MB</div>
                    </div>
                    <button class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i></button>
                </li>
            </ul>
            <button class="btn btn-primary btn-sm w-100 mt-3">View All Documents</button>
        </div>
    </section>
</main>
<?= $this->endSection() ?>