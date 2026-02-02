<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 2.5rem; width: 100%; max-width: 400px; }
        .brand { color: #2563eb; font-weight: 700; font-size: 1.5rem; text-align: center; margin-bottom: 2rem; }
        .btn-primary { background: #2563eb; border: none; padding: 0.8rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand">
        <i class="fa-solid fa-heart-pulse"></i> MedEquip
    </div>
    
    <h4 class="fw-bold mb-3">Forgot Password</h4>
    <p class="text-muted small mb-4">Enter your email and we'll send you instructions to reset your password.</p>

    <?php if (! empty($message)): ?>
        <div class="alert alert-info"><?= esc($message) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('forgot') ?>" method="post">
        <input type="hidden" name="csrf_test_name" value="43a7ab486d04abd303f4e230ccfe71bf">        
            <div class="mb-4">
            <label class="form-label small fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
        <div class="text-center">
            <a href="<?= site_url('login') ?>" class="small text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Login</a>
        </div>
    </form>
</div>

    </body>
</html>