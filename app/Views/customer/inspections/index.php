<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="mb-4">Inspections</h1>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Equipment</th>
            <th>Scheduled Date</th>
            <th>Status</th>
            <th>Technician</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inspections as $inspection): ?>
            <tr>
                <td><?= esc($inspection['id']) ?></td>
                <td><?= esc($inspection['equipment_id']) ?></td>
                <td><?= esc($inspection['scheduled_at']) ?></td>
                <td><?= esc($inspection['status']) ?></td>
                <td><?= esc($inspection['technician_id'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>