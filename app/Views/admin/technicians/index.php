<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Technicians</h1>
    <a href="#" class="btn btn-primary">Add Technician</a>
</div>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Specialization</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($technicians as $technician): ?>
            <tr>
                <td><?= esc($technician['name'] ?? '') ?></td>
                <td><?= esc($technician['user_email'] ?? '') ?></td>
                <td><?= esc($technician['phone'] ?? '') ?></td>
                <td><?= esc($technician['specialization'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>