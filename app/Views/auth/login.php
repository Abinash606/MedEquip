<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MedEquip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #2563eb; --secondary-color: #64748b; --bg-light: #f8fafc; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; padding: 2.5rem; }
        .brand-logo { color: var(--primary-color); font-size: 2rem; font-weight: 700; margin-bottom: 2rem; text-align: center; }
        .btn-primary { background-color: var(--primary-color); border: none; padding: 0.8rem; font-weight: 600; border-radius: 8px; }
        .form-control { padding: 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    </style>
</head>

<div class="container" style="max-width: 400px; margin-top: 80px;">
    <div class="login-card">
        <div class="brand-logo"><i class="fa-solid fa-heart-pulse"></i> MedEquip</div>
        <h5 class="text-center mb-4 fw-bold">Sign In</h5>
    <?php if (! empty($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>
    <form action="<?= site_url('login') ?>" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="<?= site_url('forgot') ?>" class="small text-center">Forgot your password?</a>
        </div>
    </form>
</div>
</div>
</body>
</html>

<style>
    :root { --primary-color: #2563eb; --secondary-color: #64748b; --bg-light: #f8fafc; }
body { 
    font-family: 'Poppins', sans-serif; 
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
    height: 100vh; 
    display: flex; 
    align-items: center; 
    justify-content: center;
}
.login-card { 
    background: rgba(255, 255, 255, 0.8); 
    backdrop-filter: blur(10px); 
    border-radius: 16px; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
    width: 100%; 
    max-width: 400px; 
    padding: 2.5rem; 
}
.brand-logo { 
    color: var(--primary-color); 
    font-size: 2rem; 
    font-weight: 700; 
    margin-bottom: 2rem; 
    text-align: center; 
}
.btn-primary { 
    background-color: var(--primary-color); 
    border: none; 
    padding: 0.8rem; 
    font-weight: 600; 
    border-radius: 8px; 
}
.form-control { 
    padding: 0.8rem; 
    border-radius: 8px; 
    border: 1px solid #e2e8f0; 
}

    </style>