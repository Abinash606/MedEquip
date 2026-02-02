<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Edit Site</h1>
    <a href="<?= site_url('admin/sites') ?>" class="btn btn-secondary">&larr; Back to Sites</a>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
        <?php foreach (session('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('admin/sites/update/' . $site['id']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Site Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= set_value('name', $site['name']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="customer_id">Customer<span class="text-danger">*</span></label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Select customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['id'] ?>" <?= set_value('customer_id', $site['customer_id']) == $customer['id'] ? 'selected' : '' ?>><?= esc($customer['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="site_identifier">Site Identifier</label>
            <input type="text" class="form-control" id="site_identifier" name="site_identifier" value="<?= set_value('site_identifier', $site['site_identifier']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="contact_name">Contact Name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?= set_value('contact_name', $site['contact_name']) ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email', $site['email']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= set_value('phone', $site['phone']) ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= set_value('address', $site['address']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" value="<?= set_value('city', $site['city']) ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label" for="state">State</label>
            <input type="text" class="form-control" id="state" name="state" value="<?= set_value('state', $site['state']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="zip">Zip</label>
            <input type="text" class="form-control" id="zip" name="zip" value="<?= set_value('zip', $site['zip']) ?>">
        </div>
        <div class="col-md-4">
            <!-- additional fields placeholder -->
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Site</button>
</form>

<?= $this->endSection() ?>