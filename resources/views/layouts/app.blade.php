<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMS Application')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2a5298;
            --primary-dark: #0f1f33;
            --accent: #00b4d8;
            --accent-light: #48cae4;
            --accent-dark: #0096c7;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
            --bg-main: #f0f4f8;
            --bg-card: #ffffff;
            --text-primary: #1a1a2e;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
            --radius: 12px;
            --radius-sm: 8px;
        }

        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%) !important;
            border-bottom: none;
            padding: 0.6rem 0;
            box-shadow: 0 4px 20px rgba(30, 58, 95, 0.25);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff !important;
        }

        .navbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(0, 180, 216, 0.3);
        }

        .navbar .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            margin: 0 2px;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.12);
        }

        .navbar .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .navbar .dropdown-item {
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .navbar .dropdown-item:hover {
            background: var(--bg-main);
        }

        .navbar .dropdown-item i {
            width: 20px;
            margin-right: 0.5rem;
            color: var(--primary-light);
        }

        .dropdown-divider {
            margin: 0.25rem 0;
        }

        /* ── Main Content ── */
        main {
            flex: 1;
            padding: 1.5rem 0;
        }

        .container-fluid {
            max-width: 1400px;
            padding: 0 1.5rem;
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            font-weight: 800;
            font-size: 1.75rem;
            color: var(--primary-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            background: var(--bg-card);
            overflow: hidden;
        }

        .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            font-weight: 700;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* ── Tables ── */
        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1rem;
            border: none;
            white-space: nowrap;
        }

        .table thead th:first-child {
            border-radius: var(--radius-sm) 0 0 0;
        }

        .table thead th:last-child {
            border-radius: 0 var(--radius-sm) 0 0;
        }

        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
        }

        .table-hover tbody tr:hover {
            background: rgba(0, 180, 216, 0.04);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(240, 244, 248, 0.5);
        }

        /* ── Buttons ── */
        .btn {
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: var(--radius-sm);
            padding: 0.45rem 1rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            box-shadow: 0 2px 6px rgba(30, 58, 95, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.35);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, var(--success));
            box-shadow: 0 2px 6px rgba(6, 214, 160, 0.25);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, var(--success), #059669);
            box-shadow: 0 4px 12px rgba(6, 214, 160, 0.35);
            transform: translateY(-1px);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent-dark), var(--accent));
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 180, 216, 0.25);
        }

        .btn-info:hover {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #e5a100, var(--warning));
            color: var(--text-primary);
            box-shadow: 0 2px 6px rgba(255, 209, 102, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #d63384, var(--danger));
            color: #fff;
            box-shadow: 0 2px 6px rgba(239, 71, 111, 0.25);
        }

        .btn-danger:hover {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: #cbd5e1;
            color: var(--text-primary);
        }

        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.78rem;
        }

        .btn-group-actions .btn {
            margin: 0 2px;
        }

        /* ── Action buttons in table ── */
        .action-btns {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .action-btns .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        /* ── Forms ── */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.15);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.825rem;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
        }

        /* ── Modals ── */
        .modal-content {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 1rem 1.25rem;
        }

        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 1.5rem 1.25rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 0.75rem 1.25rem;
        }

        /* ── Alerts ── */
        .alert {
            border-radius: var(--radius-sm);
            border: none;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(6, 214, 160, 0.1), rgba(6, 214, 160, 0.05));
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 71, 111, 0.1), rgba(239, 71, 111, 0.05));
            color: #9b1c31;
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(255, 209, 102, 0.15), rgba(255, 209, 102, 0.05));
            color: #92400e;
            border-left: 4px solid var(--warning);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.1), rgba(0, 180, 216, 0.05));
            color: #0c4a6e;
            border-left: 4px solid var(--accent);
        }

        /* ── Pagination ── */
        .pagination {
            gap: 4px;
        }

        .page-link {
            border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.4rem 0.75rem;
        }

        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* ── Filter Card ── */
        .filter-card .card-header {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.08), rgba(0, 180, 216, 0.02));
            cursor: pointer;
        }

        .filter-card .card-header h5 {
            font-size: 0.95rem;
            color: var(--primary);
        }

        /* ── Footer ── */
        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        footer .text-muted {
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* ── Badge ── */
        .badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }

        /* ── Show modal table ── */
        .detail-table th {
            background: var(--bg-main);
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-table td {
            font-size: 0.9rem;
        }

        /* ── Empty state ── */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* ── Loading spinner ── */
        .spinner-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner-overlay.active {
            display: flex;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .page-header .btn {
                width: 100%;
            }
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span class="brand-icon"><i class="bi bi-file-earmark-text"></i></span>
                DMS
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('legal-acts.*') ? 'active' : '' }}" 
                           href="{{ route('legal-acts.index') }}">
                            <i class="bi bi-file-text me-1"></i> Legal Acts
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-grid me-1"></i> Reference Data
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('act-types.*') ? 'active' : '' }}" 
                                   href="{{ route('act-types.index') }}">
                                    <i class="bi bi-bookmark"></i> Sənədin növü
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('issuing-authorities.*') ? 'active' : '' }}" 
                                   href="{{ route('issuing-authorities.index') }}">
                                    <i class="bi bi-building-check"></i> Issuing Authorities
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('departments.*') ? 'active' : '' }}" 
                                   href="{{ route('departments.index') }}">
                                    <i class="bi bi-diagram-3"></i> Departments
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('executors.*') ? 'active' : '' }}" 
                                   href="{{ route('executors.index') }}">
                                    <i class="bi bi-people"></i> Executors
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('execution-notes.*') ? 'active' : '' }}" 
                                   href="{{ route('execution-notes.index') }}">
                                    <i class="bi bi-sticky"></i> Execution Notes
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ auth()->user()->name }} {{ auth()->user()->surname }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="px-3 py-2">
                                <small class="text-muted d-block">Rol</small>
                                <span class="badge {{ auth()->user()->user_role === 'admin' ? 'bg-danger' : (auth()->user()->user_role === 'manager' ? 'bg-primary' : 'bg-secondary') }}">
                                    {{ ucfirst(auth()->user()->user_role) }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Çıxış
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="text-center p-3 text-muted">
            &copy; {{ date('Y') }} DMS &mdash; Document Management System
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Role check
        const userRole = @json(auth()->user()?->user_role ?? 'user');
        const canManage = ['admin', 'manager'].includes(userRole);
        const isAdmin = userRole === 'admin';
        
        // Helper: escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
        
        // Helper: fetch JSON with error handling
        async function fetchJson(url) {
            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                return await response.json();
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Məlumat yüklənmədi. Yenidən cəhd edin.');
                return null;
            }
        }

        // Auto-dismiss alerts after 4 seconds
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 4000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>