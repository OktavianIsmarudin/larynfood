<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laryn - Sistem Inventory UMKM')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0d47a1;
            --secondary-color: #1565c0;
            --accent-color: #1976d2;
            --light-bg: #f5f5f5;
            --border-color: #e0e0e0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            height: 100vh;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            overflow-y: auto;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
        }

        .sidebar .brand h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar .brand p {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.8;
        }

        .sidebar .nav-menu {
            list-style: none;
            padding: 0 10px;
        }

        .sidebar .nav-item {
            margin-bottom: 5px;
        }

        .sidebar .nav-item.header {
            padding: 10px 15px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .sidebar .nav-link {
            display: block;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 20px;
        }

        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar-custom {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-custom .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 600;
            font-size: 18px;
        }

        .navbar-custom .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-custom .user-name {
            color: #333;
            font-weight: 500;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 30px;
        }

        /* Card Styles */
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Stat Cards */
        .stat-card {
            padding: 25px;
            border-radius: 12px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            opacity: 0.15;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
        }

        .stat-card.blue::before {
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
        }

        .stat-card.green::before {
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #e65100 0%, #fb8c00 100%);
        }

        .stat-card.orange::before {
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-card.red {
            background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
        }

        .stat-card.red::before {
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-card h6 {
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 600;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card .stat-icon {
            font-size: 42px;
            opacity: 0.2;
            text-align: right;
            position: relative;
            z-index: 0;
        }

        /* Table Styles */
        .table {
            border-collapse: collapse;
        }

        .table thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--border-color);
        }

        .table thead th {
            font-weight: 600;
            color: #333;
            padding: 15px;
            border: none;
        }

        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Form Styles */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.15);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        /* Alert Styles */
        .alert {
            border-radius: 5px;
            border: none;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }

        .alert-warning {
            background-color: #fff3e0;
            color: #e65100;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .content-area {
                padding: 15px;
            }

            .sidebar .brand h4 {
                font-size: 20px;
            }

            .stat-card {
                margin-bottom: 15px;
            }

            .stat-card .stat-value {
                font-size: 20px;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        @auth
        <nav class="sidebar">
            <div class="brand">
                <h4><i class="fas fa-utensils"></i> Laryn</h4>
                <p>Sistem Inventory</p>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item header">Master Data</li>
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Kategori Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Supplier
                    </a>
                </li>

                <li class="nav-item header">Inventory</li>
                <li class="nav-item">
                    <a href="{{ route('stock-gudang.index') }}" class="nav-link {{ request()->routeIs('stock-gudang.*') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i> Stock Gudang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('produk-siap-jual.index') }}" class="nav-link {{ request()->routeIs('produk-siap-jual.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> Produk Siap Jual
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('produk-paket.index') }}" class="nav-link {{ request()->routeIs('produk-paket.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i> Produk Paket
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('bom.index') }}" class="nav-link {{ request()->routeIs('bom.*') ? 'active' : '' }}">
                        <i class="fas fa-file-recipe"></i> Bill of Material
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('nilai-gizi.index') }}" class="nav-link {{ request()->routeIs('nilai-gizi.*') ? 'active' : '' }}">
                        <i class="fas fa-seedling"></i> Nilai Gizi
                    </a>
                </li>

                <li class="nav-item header">Keuangan</li>
                <li class="nav-item">
                    <a href="{{ route('saldo-modal.index') }}" class="nav-link {{ request()->routeIs('saldo-modal.*') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> Saldo Modal
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pembelian.index') }}" class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders-management.index') }}" class="nav-link {{ request()->routeIs('orders-management.*') ? 'active' : '' }}">
                        <i class="fas fa-truck-fast"></i> Tracking Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('financial-report.index') }}" class="nav-link {{ request()->routeIs('financial-report.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Laporan Keuangan
                    </a>
                </li>

                <li class="nav-item header">Laporan</li>
                <li class="nav-item">
                    <a href="{{ route('reports.top-products') }}" class="nav-link">
                        <i class="fas fa-chart-bar"></i> Produk Terlaris
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.sales-summary') }}" class="nav-link">
                        <i class="fas fa-chart-line"></i> Rekap Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.purchase-summary') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i> Rekap Pembelian
                    </a>
                </li>

                <li class="nav-item header">AI Assistant</li>
                <li class="nav-item">
                    <a href="{{ route('knowledge-base.index') }}" class="nav-link {{ request()->routeIs('knowledge-base.*') ? 'active' : '' }}">
                        <i class="fas fa-robot"></i> Knowledge Base
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->isSuperAdmin())
                <li class="nav-item header">Super Admin</li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> Manajemen User
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        @endauth

        <!-- Main Content -->
        <div class="main-content" style="width: 100%;">
            @auth
            <!-- Navbar -->
            <nav class="navbar navbar-custom">
                <span class="navbar-brand">@yield('page-title', 'Dashboard')</span>
                <div class="ms-auto user-menu">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                </div>
            </nav>
            @endauth

            <!-- Content Area -->
            <div class="content-area">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @yield('extra-js')
</body>
</html>
