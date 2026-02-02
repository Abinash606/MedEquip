<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<<h3 class="fw-bold mb-4">Data Operations</h3>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-import me-2"></i>Bulk Operations</h5>
                    <div class="mb-3">
                        <label class="form-label">Import Equipment Data (CSV/JSON)</label>
                        <input class="form-control" type="file">
                    </div>
                    <button class="btn btn-primary w-100">Upload & Process</button>
                    <hr>
                    <button class="btn btn-outline-secondary w-100">Export All Service Records</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-server me-2"></i>Backup & Recovery</h5>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
                        <div>
                            <div class="fw-bold">Last Automated Backup</div>
                            <div class="small text-muted">Today, 04:00 AM</div>
                        </div>
                        <span class="badge bg-success">Success</span>
                    </div>
                    <button class="btn btn-dark w-100"><i class="fa-solid fa-database me-2"></i> Trigger Manual Backup</button>
                </div>
            </div>
        </div>
<?= $this->endSection() ?>
