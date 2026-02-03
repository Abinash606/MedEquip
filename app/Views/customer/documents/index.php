<?= $this->extend('layouts/customer-header') ?>

<?= $this->section('content') ?>
<!-- Documents section -->
    <section id="documents" class="view-section active">
        <div class="glass-card">
            <h5 class="fw-bold mb-3">Documents &amp; Reports</h5>
            <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-bold">Compliance Report Q3</div>
                        <div class="text-muted small">Oct&nbsp;12 • 2.4&nbsp;MB</div>
                    </div>
                    <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                </li>
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-bold">Invoice #INV‑2025‑001</div>
                        <div class="text-muted small">Oct&nbsp;10 • Paid</div>
                    </div>
                    <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                </li>
                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <div class="fw-bold">Service Logs 2024</div>
                        <div class="text-muted small">Sep&nbsp;20 • 1.2&nbsp;MB</div>
                    </div>
                    <button class="btn btn-sm btn-light"><i class="fa-solid fa-download"></i></button>
                </li>
            </ul>
            <button class="btn btn-outline-primary btn-sm w-100 mt-3">View All Documents</button>
        </div>
    </section>
</main>
<?= $this->endSection() ?>