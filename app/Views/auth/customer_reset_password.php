<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MedEquip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --bg-light: #f8fafc;
        }

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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
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
</head>
<div class="container" style="max-width: 400px; margin-top: 80px;">
    <div class="login-card">
        <div class="brand-logo">
            <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetiq Logo">
        </div>
        <?php if (! empty($error)): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <form action="<?= site_url('customer/update-password') ?>" method="post" id="reset-password-form">
            <input type="hidden" name="reset_token" value="<?= esc($resetToken) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Reset Password</button>
                <a href="<?= site_url('login') ?>" class="small text-center">Back to login</a>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // jQuery Validation
        $("#reset-password-form").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 8
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password" // Ensure the confirm password matches the new password
                }
            },
            messages: {
                password: {
                    required: "Please enter a new password.",
                    minlength: "Password must be at least 8 characters long."
                },
                confirm_password: {
                    required: "Please confirm your password.",
                    equalTo: "Password confirmation does not match."
                }
            },
            submitHandler: function(form) {
                form.submit(); // Submit the form if validation passes
            }
        });
    });
</script>
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --bg-light: #f8fafc;
    }

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
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
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
</body>

</html>