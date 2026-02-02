<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Sites</h1>
    <a href="#" class="btn btn-primary">Add Site</a>
</div>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>City</th>
            <th>State</th>
            <th>Contact</th>
            <th>Email</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sites as $site): ?>
            <tr>
                <td><?= esc($site['name']) ?></td>
                <td><?= esc($site['city'] ?? '') ?></td>
                <td><?= esc($site['state'] ?? '') ?></td>
                <td><?= esc($site['contact_name'] ?? '') ?></td>
                <td><?= esc($site['email'] ?? '') ?></td>
                <td><a href="<?= site_url('customer/sites/' . $site['id']) ?>" class="btn btn-sm btn-outline-primary">Details</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>