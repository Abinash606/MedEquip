<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3 class="fw-bold mb-4">Data Operations</h3>
<div class="row g-4">
    <div class="col-md-6">
        <div class="glass-card">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-import me-2"></i>Bulk Operations</h5>
            <!-- <div class="mb-3">
                    <label class="form-label">Import Equipment Data (CSV/JSON)</label>
                    <input class="form-control" type="file">
                </div>
                <button class="btn btn-primary w-100">Upload & Process</button> -->
            <form action="<?= base_url('admin/data-operations/import-equipment') ?>" method="post"
                enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Import Equipment Data (CSV)</label>
                    <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                </div>

                <button class="btn btn-primary w-100">
                    Upload & Process
                </button>

            </form>


            <hr>
            <!-- <button class="btn btn-outline-secondary w-100">Export All Service Records</button> -->
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-card">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-server me-2"></i>Backup & Recovery</h5>
            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
                <div>
                    <!-- <div class="fw-bold">Last Automated Backup</div>
                        <div class="small text-muted">Today, 04:00 AM</div> -->
                    <div class="fw-bold">Last Automated Backup</div>
                    <div class="small text-muted">
                        <?= isset($lastBackup) && $lastBackup
                            ? date('d M Y, h:i A', strtotime($lastBackup->created_at))
                            : 'No backup yet' ?>
                    </div>

                </div>
                <span class="badge bg-success">Success</span>
            </div>
            <button id="backupBtn" class="btn btn-dark w-100">
                <i class="fa-solid fa-database me-2"></i> Trigger Manual Backup
            </button>

            <a id="downloadLink" class="btn btn-success w-100 mt-3 d-none" href="#">
                <i class="fa-solid fa-download me-2"></i> Download Backup (.sql.gz)
            </a>
            <script>
            document.getElementById('backupBtn').addEventListener('click', function() {
                fetch("<?= base_url('admin/data-operations/generate-backup') ?>")
                    .then(res => res.json())
                    .then(data => {
                        // if (data.status === 'success') {
                        //     const link = document.getElementById('downloadLink');
                        //     link.href = data.file;
                        //     link.classList.remove('d-none');
                        // }
                        if (data.status === 'success') {
                            document.querySelector('.small.text-muted').innerText = data.time;

                            const link = document.getElementById('downloadLink');
                            link.href = data.file;
                            link.classList.remove('d-none');
                        }

                    })
                    .catch(err => console.error(err));
            });
            </script>
        </div>
    </div>
</div>
<?= $this->endSection() ?>