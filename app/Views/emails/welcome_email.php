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
    <title>Welcome Email</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="Company Logo">
        </div>
        <div class="content">
            <h2>Welcome, <?= esc($customer_name) ?>!</h2>
            <p>Thank you for registering with us. We're excited to have you on board. At AssetIQ, we strive to provide
                excellent service and support. We hope you have a great experience with us!</p>
            <p>To get started, you can log in to your account using the link below:</p>
            <a href="<?= site_url('login') ?>" class="cta-button">Log In to Your Account</a>
            <p>If you need any assistance, feel free to contact our support team at <a
                    href="mailto:support@company.com">support@company.com</a>.</p>
        </div>
        <div class="footer">
            <p>© <?= date('Y') ?> [Company Name]. All rights reserved.</p>
            <p>1234 Street Name, City, Country</p>
        </div>
    </div>
</body>

</html>