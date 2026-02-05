<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedEquip Customer Portal | Facility Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
    /* Reusing your specified Design System */
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --bg-color: #f8fafc;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --sidebar-width: 280px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        color: #334155;
        overflow-x: hidden;
    }

    /* Sidebar & Layout */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: #ffffff;
        border-right: 1px solid #f1f5f9;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        padding: 2rem;
        transition: all 0.3s ease;
    }

    .brand-logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        padding: 2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-link {
        color: var(--secondary-color);
        font-weight: 500;
        padding: 0.8rem 1.5rem;
        margin: 0.2rem 1rem;
        border-radius: 12px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-link:hover,
    .nav-link.active {
        background-color: #eff6ff;
        color: var(--primary-color);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* Glass Cards */
    .glass-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: var(--card-shadow);
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
    }

    /* Hover background for list items */
    .hover-bg:hover {
        background-color: #f8fafc;
    }

    /* Custom Table & Status */
    .custom-table thead th {
        border-bottom: none;
        color: #94a3b8;
        font-weight: 500;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .custom-table td {
        vertical-align: middle;
        padding: 1rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .status-badge {
        padding: 0.35em 0.8em;
        border-radius: 50rem;
        font-size: 0.75em;
        font-weight: 600;
    }

    .status-operational {
        background: #d1fae5;
        color: #065f46;
    }

    .status-service {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-maintenance {
        background: #fef3c7;
        color: #92400e;
    }

    /* Action Buttons with gradient for modals */
    .btn-wow {
        background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 12px;
        font-weight: 500;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        transition: all 0.2s;
    }

    .btn-wow:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        color: white;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .sidebar {
            left: -300px;
        }

        .sidebar.active {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }
    }

    /* View sections for tabbed navigation */
    .view-section {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    .view-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Specific Customer Elements */
    .doc-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }


    /* DataTables polish to match the UI */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.35rem 0.6rem;
        outline: none;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.25rem 0.5rem;
    }

    .dt-buttons .dt-button {
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #2563eb !important;
        border-radius: 12px !important;
        padding: 0.4rem 0.75rem !important;
        font-weight: 600 !important;
        margin-right: 0.4rem !important;
    }

    .dt-buttons .dt-button:hover {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        font-size: 0.9rem;
    }

    /* Mobile overlay for sidebar */
    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.35);
        z-index: 999;
    }

    .mobile-overlay.active {
        display: block;
    }

    /* Responsive tweaks */
    @media (max-width: 991px) {
        .main-content {
            padding: 1rem;
        }
    }
    </style>
</head>

<body>

    <nav class="sidebar">
        <!-- Logo -->
        <div class="d-flex align-items-center gap-2 p-4 text-primary fw-bold fs-4">
            <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetIQ Logo"
                style="max-height: 90px; width: auto; max-width: 100%; object-fit: contain;">
        </div>

        <!-- Navigation -->
        <div class="d-flex flex-column h-100">
            <div class="flex-grow-1">
                <?php $role = session('role'); ?>



                <!-- Role: Customer -->
                <?php if ($role === 'customer'): ?>
                <a class="nav-link<?= url_is('customer/dashboard') ? ' active' : '' ?>"
                    href="<?= site_url('customer/dashboard') ?>">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Overview</span>
                </a>
                <a class="nav-link<?= url_is('customer/assets*') ? ' active' : '' ?>"
                    href="<?= site_url('customer/assets') ?>">
                    <i class="fa-solid fa-cubes"></i>
                    <span>Assets</span>
                </a>
                <a class="nav-link<?= url_is('customer/inspections*') ? ' active' : '' ?>"
                    href="<?= site_url('customer/inspections') ?>">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Inspections</span>
                </a>
                <a class="nav-link<?= url_is('customer/documents*') ? ' active' : '' ?>"
                    href="<?= site_url('customer/documents') ?>">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Documents</span>
                </a>

                <?php endif; ?>


                <!-- User Profile Footer -->
                <div class="mt-auto p-4 border-top">
                    <div class="d-flex align-items-center gap-2">

                        <?php
            $username = session('username') ?? 'SysAdmin';
            $initials = strtoupper(substr(trim($username), 0, 2));
        ?>

                        <!-- Avatar circle -->
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold"
                            style="width:35px;height:35px; font-size:12px; line-height:1;">
                            <?= esc($initials) ?>
                        </div>

                        <!-- Name + logout -->
                        <div class="small lh-sm sidebar-text">
                            <div class="fw-bold"><?= esc($username) ?></div>
                            <a href="<?= site_url('logout') ?>" class="text-muted text-decoration-none">Logout</a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="p-4 mb-3">
                <div class="glass-card bg-primary text-white border-0 p-3"
                    style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                    <h6 class="mb-1"><i class="fa-solid fa-headset me-2"></i>Support</h6>
                    <p class="small opacity-75 mb-2">Dedicated Manager: Mike R.</p>
                    <button class="btn btn-sm btn-light w-100 fw-bold text-primary"
                        fdprocessedid="vafm2m">Contact</button>
                </div>
            </div>
        </div>
    </nav>
    <?= $this->include('partials/customer-scripts') ?>
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/customer-modals') ?>

</body>

</html>