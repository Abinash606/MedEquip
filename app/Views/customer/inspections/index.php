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
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Inspections</h3>
        <p class="text-muted small mb-0">Track scheduled and completed equipment inspections.</p>
    </div>
</div>

<div class="content">
<section id="inspections" class="view-section active">
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Upcoming Inspections</h5>
                </div>

                <?php if (!empty($upcomingInspections)): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($upcomingInspections as $inspection): ?>
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <div class="fw-bold">
                                        <?= date('M j, Y', strtotime($inspection['scheduled_at'])) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= esc($inspection['device_type'] ?? 'Equipment') ?>
                                        <?php if (!empty($inspection['inspection_type'])): ?>
                                            <?= esc($inspection['inspection_type']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php
                                // Determine badge
                                $scheduledDate = strtotime($inspection['scheduled_at']);
                                $daysUntil = ceil(($scheduledDate - time()) / (60 * 60 * 24));

                                if ($inspection['status'] === 'Scheduled'):
                                    $badgeClass = 'bg-info text-dark';
                                    $badgeText = 'Scheduled';
                                elseif ($daysUntil <= 7):
                                    $badgeClass = 'bg-warning text-dark';
                                    $badgeText = 'Due Soon';
                                else:
                                    $badgeClass = 'bg-success';
                                    $badgeText = 'Booked';
                                endif;
                                ?>

                                <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">No upcoming inspections</div>
                                <div class="text-muted small">Schedule an inspection</div>
                            </div>
                            <span class="badge bg-secondary">N/A</span>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="glass-card">
                <h5 class="fw-bold mb-3">Inspection History</h5>

                <?php if (!empty($inspectionHistory)): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($inspectionHistory as $index => $inspection): ?>
                            <li
                                class="d-flex justify-content-between align-items-center py-2 <?= $index < count($inspectionHistory) - 1 ? 'border-bottom' : '' ?>">
                                <div>
                                    <div class="fw-bold">
                                        <?= date('M j, Y', strtotime($inspection['completed_at'] ?? $inspection['scheduled_at'])) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= esc($inspection['device_type'] ?? 'Equipment') ?> –
                                        <?= esc($inspection['status']) ?>
                                    </div>
                                </div>

                                <?php
                                $status = $inspection['status'];
                                $badgeClass = ($status === 'Pass' || $status === 'Completed') ? 'bg-success' : 'bg-danger';
                                ?>

                                <span class="badge <?= $badgeClass ?>"><?= esc($status) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">No history available</div>
                                <div class="text-muted small">Completed inspections will appear here</div>
                            </div>
                            <span class="badge bg-secondary">N/A</span>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</div>
<?= $this->endSection() ?>