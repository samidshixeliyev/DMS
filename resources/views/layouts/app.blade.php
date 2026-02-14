<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMS Tətbiqi')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
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
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem !important;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            margin: 0 1px;
            white-space: nowrap;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.12);
        }

        .navbar .nav-link i {
            font-size: 0.9rem;
        }

        .nav-separator {
            color: rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            padding: 0 0.25rem;
            font-size: 1.2rem;
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

        main {
            flex: 1;
            padding: 1.5rem 0;
        }

        .container-fluid {
            max-width: 100%;
            padding: 0 1rem;
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

        .filter-card .card-header {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.08), rgba(0, 180, 216, 0.02));
            cursor: pointer;
        }

        .filter-card .card-header h5 {
            font-size: 0.95rem;
            color: var(--primary);
        }

        .filter-card .select2-container--bootstrap-5 .select2-selection {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            min-height: 38px;
            font-size: 0.875rem;
        }
        .filter-card .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 0.75rem;
        }
        .filter-card .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .filter-card .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.15);
        }

        .flatpickr-input {
            background: #fff !important;
        }

        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        footer .text-muted {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }

        .detail-table td {
            font-size: 0.9rem;
        }

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
                            <i class="bi bi-file-text me-1"></i> Hüquqi Aktlar
                        </a>
                    </li>
                    <li class="nav-separator">|</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('act-types.*') ? 'active' : '' }}" 
                           href="{{ route('act-types.index') }}">
                            <i class="bi bi-bookmark me-1"></i> Sənəd növləri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('issuing-authorities.*') ? 'active' : '' }}" 
                           href="{{ route('issuing-authorities.index') }}">
                            <i class="bi bi-building-check me-1"></i> Verən orqanlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" 
                           href="{{ route('departments.index') }}">
                            <i class="bi bi-diagram-3 me-1"></i> Şöbələr
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('executors.*') ? 'active' : '' }}" 
                           href="{{ route('executors.index') }}">
                            <i class="bi bi-people me-1"></i> İcraçılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('execution-notes.*') ? 'active' : '' }}" 
                           href="{{ route('execution-notes.index') }}">
                            <i class="bi bi-sticky me-1"></i> İcra qeydləri
                        </a>
                    </li>
                    @if(auth()->user()->user_role === 'admin')
                    <li class="nav-separator">|</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                           href="{{ route('users.index') }}">
                            <i class="bi bi-person-gear me-1"></i> İstifadəçilər
                        </a>
                    </li>
                    @endif
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
                                    {{ auth()->user()->user_role === 'admin' ? 'Admin' : (auth()->user()->user_role === 'manager' ? 'Menecer' : 'İstifadəçi') }}
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
            &copy; {{ date('Y') }} DMS &mdash; Sənəd İdarəetmə Sistemi
        </div>
    </footer>

    <!-- jQuery (required for DataTables & Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS + FixedColumns + Buttons -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Flatpickr JS + Azerbaijan locale -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/az.js"></script>
    
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

        // Global Select2 defaults
        $.fn.select2.defaults.set('theme', 'bootstrap-5');
        $.fn.select2.defaults.set('language', {
            noResults: function() { return 'Nəticə tapılmadı'; },
            searching: function() { return 'Axtarılır...'; },
            removeAllItems: function() { return 'Hamısını sil'; }
        });
    </script>
    
    @stack('scripts')
</body>
</html>