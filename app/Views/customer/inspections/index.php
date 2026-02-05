<?= $this->extend('layouts/customer-header') ?>
<?= $this->section('content') ?>
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
<?= $this->endSection() ?>