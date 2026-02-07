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
            padding: 15px;
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
            font-size: 22px;
            color: #333;
        }

        .content p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            padding: 12px 22px;
            background-color: #39AC73;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }
    </style>

    <title>Reset Your Password - <?= esc($company_name) ?></title>
</head>

<body>
    <div class="container">

        <!-- Header Logo -->
        <div class="header">
            <?php
            // If logo is full URL → use directly
            if (filter_var($company_logo, FILTER_VALIDATE_URL)) {
                $logoPath = $company_logo;
            } else {
                // Otherwise use uploaded logo path
                $logoPath = base_url('uploads/logos/' . $company_logo);
            }
            ?>
            <img src="<?= $logoPath ?>" alt="Company Logo">
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Reset Your Password, <?= esc($username) ?>!</h2>

            <p>
                We received a request to reset your password for your
                <strong><?= esc($company_name) ?></strong> account.
            </p>

            <p>
                Click the button below to set a new password. This link will expire in
                <strong>1 hour</strong>.
            </p>

            <a href="<?= esc($reset_link) ?>" class="cta-button">
                Reset My Password
            </a>

            <p style="margin-top:25px;">
                If you did not request this, please ignore this email.
            </p>

            <p>
                Need help? Contact us at
                <a href="mailto:support@assetiq.com">support@assetiq.com</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© <?= date('Y') ?> <?= esc($company_name) ?>. All rights reserved.</p>
        </div>

    </div>
</body>

</html>