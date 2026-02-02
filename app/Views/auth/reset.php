<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container" style="max-width: 400px; margin-top: 80px;">
    <h2 class="mb-4">Reset Password</h2>
    <?php if (! empty($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('reset/' . esc($token)) ?>" method="post">
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Reset Password</button>
            <a href="<?= site_url('login') ?>" class="small text-center">Back to login</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>