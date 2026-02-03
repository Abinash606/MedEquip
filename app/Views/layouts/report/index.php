<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<!-- Reports view -->
    <section id="reports" class="view-section active">
        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card mb-4">
                    <h5 class="fw-bold mb-3">Reports &amp; Documents</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Q3 Inspection Summary</div>
                                <div class="text-muted small">Oct&nbsp;12 • 2.4&nbsp;MB</div>
                            </div>
                            <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Invoice&nbsp;#INV‑2025‑001</div>
                                <div class="text-muted small">Oct&nbsp;10 • Paid</div>
                            </div>
                            <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Device Certification Records</div>
                                <div class="text-muted small">Sep&nbsp;20 • 1.2&nbsp;MB</div>
                            </div>
                            <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                        </li>
                    </ul>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-3">View All Documents</button>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>