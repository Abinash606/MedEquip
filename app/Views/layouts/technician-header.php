<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedEquip Customer Portal | Service Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <style>
        :root {
            --primary-color: #2563eb;
            /* Modern Medical Blue */
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

        /* --- Sidebar --- */
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

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        /* --- Modern Cards (The WOW Factor) --- */
        .glass-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        /* Status Badges */
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

        /* --- Custom Table --- */
        .custom-table thead th {
            border-bottom: none;
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .custom-table td {
            vertical-align: middle;
            padding: 1rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        /* --- Action Buttons --- */
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

        /* Mobile Responsive Toggle */
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

            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                z-index: 999;
            }

            .mobile-overlay.active {
                display: block;
            }
        }

        /* Circular Progress for Health Score */
        .progress-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: conic-gradient(var(--success-color) 85%, #e2e8f0 0);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-circle-inner {
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* View sections for tabbed navigation. Each page area is hidden by default and shown when active. */
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
    </style>
</head>

<body>

    <div class="mobile-overlay" id="mobileOverlay"></div>

    <nav class="sidebar" id="sidebar">
        <!-- <div class="brand-logo">
        <i class="fa-solid fa-heart-pulse"></i> MedEquip
    </div> -->
        <div class="d-flex align-items-center gap-2 p-4 text-primary fw-bold fs-4">
            <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetIQ Logo"
                style="max-height: 90px; width: auto; max-width: 100%; object-fit: contain;">
        </div>
        <div class="d-flex flex-column h-100">
            <div class="flex-grow-1">
                <p class="text-uppercase text-muted small fw-bold px-4 mb-2 mt-2">Main Menu</p>
                <!-- Sidebar navigation links now call switchTab() to toggle between views -->
                <a class="nav-link<?= url_is('technician/dashboard') ? ' active' : '' ?>" href="<?= site_url('technician/dashboard') ?>">
                    <i class="fa-solid fa-grid-2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('technician/customers') ? ' active' : '' ?>" href="<?= site_url('technician/customers') ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers</span>
                </a>
                <a class="nav-link<?= url_is('technician/sites') ? ' active' : '' ?>" href="<?= site_url('technician/sites') ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Sites</span>
                </a>
                <a class="nav-link<?= url_is('technician/inspections') ? ' active' : '' ?>" href="<?= site_url('technician/inspections') ?>">
                    <i class="fa-solid fa-clipboard-check"></i> Inspections
                    <span class="badge bg-danger rounded-pill ms-auto">2</span>
                </a>
                <a class="nav-link<?= url_is('technician/service-history') ? ' active' : '' ?>" href="<?= site_url('technician/service-history') ?>">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Service History</span>
                </a>
                <a class="nav-link<?= url_is('technician/reports') ? ' active' : '' ?>" href="<?= site_url('technician/reports') ?>">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Reports</span>
                </a>
            </div>
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
            <div class="p-4 mb-5">
                <div class="glass-card bg-primary text-white border-0 p-3" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h6 class="mb-0">Need Help?</h6>
                    </div>
                    <p class="small opacity-75 mb-2">Contact support for urgent issues.</p>
                    <button class="btn btn-sm btn-light w-100 fw-bold text-primary">Support Center</button>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <?= $this->include('partials/customer-scripts') ?>
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/customer-modals') ?>


</body>

</html>