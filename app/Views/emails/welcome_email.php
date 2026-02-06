<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            background-color: #235C47;
            padding: 10px;
            color: white;
            text-align: center;
        }

        .header img {
            width: 150px;
            height: auto;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content h2 {
            font-size: 24px;
            color: #333;
        }

        .content p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #39AC73;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
    <title>Welcome</title>
</head>

<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <?php if (!empty($company_logo)): ?>
                <img src="<?= base_url($company_logo) ?>" alt="<?= esc($company_name) ?> Logo">
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Welcome, <?= esc($customer_name) ?>!</h2>

            <p>
                Thank you for registering with
                <strong><?= esc($company_name) ?></strong>.
                We're excited to have you on board and look forward to supporting you.
            </p>

            <p>
                You can now log in to your account using the button below.
            </p>

            <a href="<?= site_url('login') ?>" class="cta-button" style="
                display:inline-block;
                padding:10px 20px;
                background-color:#39AC73;
                color:#ffffff !important;
                text-decoration:none;
                border-radius:5px;
                font-weight:500;
            ">
                Log In to Your Account
            </a>

            <p style="margin-top:20px;">
                If you need any assistance, feel free to contact our support team at
                <a href="mailto:support@assetiq.com">support@assetiq.com</a>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© <?= date('Y') ?> <?= esc($company_name) ?>. All rights reserved.</p>
        </div>

    </div>
</body>

</html>