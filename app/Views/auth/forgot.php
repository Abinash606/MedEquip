<!DOCTYPE html>
<html lang="en">

<head>
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

        .brand {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="brand">
            <i class="fa-solid fa-heart-pulse"></i> MedEquip
        </div>

        <h4 class="fw-bold mb-3">Forgot Password</h4>
        <p class="text-muted small mb-4">
            Enter your email and we’ll send you instructions to reset your password.
        </p>

        <div id="alertBox" class="alert d-none"></div>

        <form id="forgotForm">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                Send Reset Link
            </button>

            <div class="text-center">
                <a href="<?= site_url('login') ?>" class="small text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('submitBtn');
            const alert = document.getElementById('alertBox');
            const data = new FormData(form);

            alert.className = 'alert d-none';
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Sending...';

            fetch("<?= site_url('forgot') ?>", {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: data
                })
                .then(async response => {
                    const text = await response.text();

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Non-JSON response:', text);
                        throw new Error('Invalid server response');
                    }
                })
                .then(res => {
                    alert.classList.remove('d-none');
                    alert.classList.add('alert-info');
                    alert.innerText = res.message || 'Request processed successfully.';
                    form.reset();
                })
                .catch(() => {
                    alert.classList.remove('d-none');
                    alert.classList.add('alert-danger');
                    alert.innerText = 'Server error. Please try again.';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Send Reset Link';
                });
        });
    </script>


</body>

</html>