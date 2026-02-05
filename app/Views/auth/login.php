<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AssetIQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --bg-light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.1);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            max-height: 90px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            padding: 0.85rem 1rem;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            border: none;
            padding: 0.9rem;
            font-weight: 600;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .forgot-link {
            text-decoration: none;
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
        }

        .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .brand-logo img {
                max-height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="brand-logo">
                <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetiq Logo">
            </div>

            <!-- Page Title -->
            <h1 class="page-title">Sign In</h1>

            <!-- Error Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= esc($error) ?>
                </div>
            <?php endif; ?>

            <!-- Success Messages -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= site_url('login') ?>" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email"
                        required autocomplete="email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Enter your password" required autocomplete="current-password">
                </div>

                <div class="d-grid gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                    <a href="<?= site_url('forgot') ?>" class="forgot-link text-center">
                        <i class="fas fa-key me-1"></i>Forgot your password?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>