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

        @media (max-width: 768px) {
            .sidebar { left: -260px; transition: left 0.3s; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; }
            .main-content.collapsed { margin-left: 0; }
            #sidebar-toggle { display: none; }
            .hamburger-menu { display: block; }
        }

        @media (min-width: 769px) {
            .hamburger-menu { display: none; }
        }
    </style>