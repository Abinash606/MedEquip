<?php /** @var string|null $title */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dashboard') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Additional styles for calendar and map views -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    
    <style>
        :root { --primary-color: #2563eb; --secondary-color: #64748b; --bg-color: #f8fafc; --sidebar-width: 260px; --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-color); color: #334155; overflow-x: hidden; }
        
        /* Layout */
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background: #fff; border-right: 1px solid #e2e8f0; z-index: 1000; overflow-y: auto; }
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem; transition: all 0.3s; }
        
        /* Cards & Glassmorphism */
        .glass-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: var(--card-shadow); padding: 1.5rem; height: 100%; margin-bottom: 1.5rem; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        
        /* Navigation */
        .nav-link { color: var(--secondary-color); padding: 0.8rem 1.5rem; display: flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 500; border-right: 3px solid transparent; transition: all 0.2s; cursor: pointer; }
        .nav-link:hover { color: var(--primary-color); background: #f1f5f9; }
        .nav-link.active { color: var(--primary-color); background: #eff6ff; border-right-color: var(--primary-color); }
        .nav-link i { width: 20px; text-align: center; }
        .section-header { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: 700; margin: 1.5rem 1.5rem 0.5rem; }

        /* Tables & Lists */
        .table-hover tbody tr:hover { background-color: #f8fafc; }
        .avatar-circle { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .status-badge { font-size: 0.75rem; padding: 0.25em 0.6em; border-radius: 4px; font-weight: 600; }
        
        /* Visuals for Gantt/Schedule */
        .gantt-row { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .gantt-label { width: 140px; font-size: 0.9rem; font-weight: 500; }
        .gantt-timeline { flex-grow: 1; height: 30px; background: #f1f5f9; border-radius: 6px; position: relative; }
        .gantt-bar { position: absolute; height: 100%; border-radius: 6px; font-size: 0.75rem; color: white; display: flex; align-items: center; padding-left: 8px; white-space: nowrap; overflow: hidden; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        /* Schedule view containers */
        .schedule-view { display: none; }
        .schedule-view.active { display: block; }
        .timeline-view { display: none; }
        .timeline-view.active { display: block; }

        /* Utilities */
        .view-section { display: none; }
        .view-section.active { display: block; }

        /* Settings panes hidden by default; shown when active */
        .settings-pane { display: none; }
        .settings-pane:not(.d-none) { display: block; }

        /* Collapsible sidebar styles */
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .sidebar-text { display: none; }
        .sidebar.collapsed .nav-link i { margin-right: 0; }
        .sidebar.collapsed .section-header { text-align: center; }
        .main-content.collapsed { margin-left: 80px; }
        #sidebar-toggle { position: absolute; bottom: 10px; right: -15px; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }
		/* Style for valid input fields */
input.valid {
    border-color: #28a745; /* Green border for valid fields */
}

/* Style for invalid input fields */
input.invalid, .error {
    border-color: #dc3545; /* Red border for invalid fields */
}

/* Style for error message */
.error {
    color: #dc3545; /* Red color for error messages */
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Style for valid input field when it has been validated */
input.valid:focus {
    border-color: #28a745; /* Green border on focus for valid fields */
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); /* Soft green box shadow */
}

/* Style for invalid input field when it has been validated */
input.invalid:focus {
    border-color: #dc3545; /* Red border on focus for invalid fields */
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25); /* Soft red box shadow */
}

/* Optional: Styling for form inputs when they are required */
input[required] {
    background-color: #f9f9f9; /* Light gray background for required fields */
}

        /* Custom message styling */
        .error {
            font-size: 14px;
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
                transition: left 0.3s;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.collapsed {
                margin-left: 0;
            }

            #sidebar-toggle {
                display: none;
            }

            .hamburger-menu {
                display: block;
            }
        }

        @media (min-width: 769px) {
            .hamburger-menu {
                display: none;
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
        <div class="sidebar-nav">
            <?php $role = session('role'); ?>

            <!-- Role: Super Admin -->
            <?php if ($role === 'super_admin'): ?>
                <div class="section-header">CORE OPERATIONS</div>
                <a class="nav-link<?= url_is('admin/dashboard') ? ' active' : '' ?>"
                    href="<?= site_url('admin/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('admin/scheduling*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/scheduling') ?>">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Scheduling</span>
                </a>

                <div class="section-header">ASSETS & CLIENTS</div>
                <a class="nav-link<?= url_is('admin/customers*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/customers') ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers</span>
                </a>
                <a class="nav-link<?= url_is('admin/sites*') ? ' active' : '' ?>" href="<?= site_url('admin/sites') ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Sites</span>
                </a>
                <a class="nav-link<?= url_is('admin/equipment*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/equipment') ?>">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Equipment DB</span>
                </a>
                <a class="nav-link<?= url_is('admin/inspection-reports*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/inspection-reports') ?>">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Inspection Reports</span>
                </a>
                <a class="nav-link<?= url_is('admin/inventory*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/inventory') ?>">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Inventory</span>
                </a>
                <a class="nav-link<?= url_is('admin/technicians*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/technicians') ?>">
                    <i class="fa-solid fa-users-cog"></i>
                    <span>Technicians</span>
                </a>

                <div class="section-header">ADMIN & ANALYTICS</div>
                <a class="nav-link<?= url_is('admin/financials*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/financials') ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Financials</span>
                </a>
                <a class="nav-link<?= url_is('admin/data-ops*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/data-ops') ?>">
                    <i class="fa-solid fa-database"></i>
                    <span>Data Ops</span>
                </a>
                <a class="nav-link<?= url_is('admin/settings*') ? ' active' : '' ?>"
                    href="<?= site_url('admin/settings') ?>">
                    <i class="fa-solid fa-gears"></i>
                    <span>Settings</span>
                </a>

                <!-- Role: Customer -->
            <?php elseif ($role === 'customer'): ?>
                <a class="nav-link<?= url_is('customer/dashboard') ? ' active' : '' ?>"
                    href="<?= site_url('customer/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('customer/sites*') ? ' active' : '' ?>"
                    href="<?= site_url('customer/sites') ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Sites</span>
                </a>

                <!-- Role: Technician -->
            <?php elseif ($role === 'technician'): ?>
                <a class="nav-link<?= url_is('technician/dashboard') ? ' active' : '' ?>"
                    href="<?= site_url('technician/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('technician/work-orders*') ? ' active' : '' ?>"
                    href="<?= site_url('technician/work-orders') ?>">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Work Orders</span>
                </a>
            <?php endif; ?>
        </div>

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


    </nav>
    <?= $this->include('partials/scripts') ?>
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/modals') ?>

</body>

</html>