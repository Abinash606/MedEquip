<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="mb-4">Equipment</h1>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>Asset Tag</th>
            <th>Make</th>
            <th>Model</th>
            <th>Serial</th>
            <th>Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= esc($item['asset_tag']) ?></td>
                <td><?= esc($item['make'] ?? '') ?></td>
                <td><?= esc($item['model'] ?? '') ?></td>
                <td><?= esc($item['serial_number'] ?? '') ?></td>
                <td><?= esc($item['device_type'] ?? '') ?></td>
                <td><?= esc($item['status'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>