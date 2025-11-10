<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - S-MIS21</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">

    @stack('styles')

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .top-header {
            --header-height: 64px;
            position: fixed;
            top: 0;
            left: 300px;
            right: 0;
            height: var(--header-height);
            z-index: 1050;
            backdrop-filter: blur(4px);
            background: #fff;
            display: flex;
            align-items: center;
            padding: .5rem 1rem;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .04);
        }

        .top-header .badge {
            transform: translate(8%, -35%);
        }

        /* Notification dropdown styles */
        .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, .08);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.bg-light {
            background-color: #e8f4fd !important;
            border-left: 3px solid #435ebe;
        }

        .dropdown-divider {
            margin: 0;
        }

        #main {
            margin-left: 300px;
            padding-top: calc(2rem + var(--header-height));
            padding-right: 2rem;
            padding-left: 2rem;
            padding-bottom: 2rem;
        }

        .top-header .input-group .form-control {
            min-width: 160px;
            max-width: 520px;
        }

        @media screen and (max-width: 1199px) {
            .top-header {
                left: 0;
                width: 100%;
            }

            #main {
                margin-left: 0;
                padding-top: calc(2rem + var(--header-height));
            }
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #f5f5f5 !important;
        }

        body.dark-mode * {
            color: #f5f5f5;
        }

        body.dark-mode .card {
            background-color: #2d2d2d;
            color: #f5f5f5;
            border-color: #404040;
        }

        body.dark-mode .card-header {
            background-color: #252525;
            color: #f5f5f5;
            border-bottom: 1px solid #404040;
        }

        body.dark-mode .card-title,
        body.dark-mode .card-text,
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, 
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
        body.dark-mode p, body.dark-mode span, body.dark-mode label,
        body.dark-mode .page-heading h3 {
            color: #f5f5f5 !important;
        }

        body.dark-mode .table {
            color: #f5f5f5;
        }

        body.dark-mode .table thead th {
            color: #f5f5f5;
            background-color: #252525;
            border-color: #404040;
        }

        body.dark-mode .table tbody td {
            color: #f5f5f5;
            border-color: #404040;
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
        }

        body.dark-mode #sidebar {
            background-color: #1e1e1e;
        }

        body.dark-mode #sidebar .sidebar-wrapper {
            background-color: #1e1e1e;
        }

        body.dark-mode .sidebar-header {
            background-color: #1e1e1e;
            border-bottom: 1px solid #333;
        }

        body.dark-mode .sidebar-header h3 {
            color: #f5f5f5 !important;
        }

        body.dark-mode .sidebar-link {
            color: #f5f5f5 !important;
        }

        body.dark-mode .sidebar-link:hover {
            background-color: #2d2d2d;
            color: #fff !important;
        }

        body.dark-mode .sidebar-item.active > .sidebar-link {
            background-color: #435ebe;
            color: #fff !important;
        }

        body.dark-mode .sidebar-title {
            color: #aaa !important;
        }

        body.dark-mode .submenu {
            background-color: #252525;
        }

        body.dark-mode .submenu .submenu-item a {
            color: #e0e0e0 !important;
        }

        body.dark-mode .submenu .submenu-item a:hover {
            background-color: #2d2d2d;
            color: #fff !important;
        }

        body.dark-mode .submenu .submenu-item.active a {
            color: #fff !important;
        }

        body.dark-mode .top-header {
            background-color: #2d2d2d;
            color: #f5f5f5;
            border-bottom: 1px solid #333;
        }

        body.dark-mode .top-header .btn-link {
            color: #f5f5f5 !important;
        }

        body.dark-mode footer {
            background-color: #2d2d2d !important;
            color: #f5f5f5 !important;
            border-top: 1px solid #404040 !important;
        }

        body.dark-mode footer h5,
        body.dark-mode footer h6,
        body.dark-mode footer a {
            color: #f5f5f5 !important;
        }

        body.dark-mode footer a:hover {
            color: #435ebe !important;
        }

        body.dark-mode .text-muted {
            color: #bbb !important;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode textarea {
            background-color: #3a3a3a;
            color: #f5f5f5 !important;
            border-color: #555;
        }

        body.dark-mode .form-control::placeholder {
            color: #999 !important;
        }

        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            background-color: #404040;
            color: #f5f5f5 !important;
            border-color: #435ebe;
        }

        body.dark-mode .form-label {
            color: #f5f5f5 !important;
        }

        body.dark-mode .form-check-label {
            color: #f5f5f5 !important;
        }

        body.dark-mode .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #fff !important;
        }

        body.dark-mode .btn-primary:hover {
            background-color: #364a92;
            border-color: #364a92;
        }

        body.dark-mode .btn-secondary {
            background-color: #555;
            border-color: #555;
            color: #fff !important;
        }

        body.dark-mode .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff !important;
        }

        body.dark-mode .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff !important;
        }

        body.dark-mode .btn-link {
            color: #6ea8fe !important;
        }

        body.dark-mode .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        body.dark-mode .dropdown-item {
            color: #f5f5f5 !important;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #3a3a3a;
            color: #fff !important;
        }

        body.dark-mode .dropdown-divider {
            border-color: #404040;
        }

        body.dark-mode .alert {
            border-color: #555;
        }

        body.dark-mode .alert-success {
            background-color: #1e4620;
            color: #c3e6cb !important;
            border-color: #2d5a2d;
        }

        body.dark-mode .alert-danger {
            background-color: #4d1f1f;
            color: #f5c2c7 !important;
            border-color: #6d2828;
        }

        body.dark-mode .alert-info {
            background-color: #1f3a4d;
            color: #b6d4fe !important;
            border-color: #2d5a6d;
        }

        body.dark-mode .alert-warning {
            background-color: #664d03;
            color: #ffecb5 !important;
            border-color: #7a5d0a;
        }

        body.dark-mode .badge {
            border: 1px solid #555;
        }

        body.dark-mode .badge-primary {
            background-color: #435ebe;
            color: #fff !important;
        }

        body.dark-mode .modal-content {
            background-color: #2d2d2d;
            color: #f5f5f5;
            border-color: #404040;
        }

        body.dark-mode .modal-header {
            background-color: #252525;
            border-bottom: 1px solid #404040;
        }

        body.dark-mode .modal-footer {
            border-top: 1px solid #404040;
        }

        body.dark-mode .breadcrumb {
            background-color: #2d2d2d;
        }

        body.dark-mode .breadcrumb-item a {
            color: #6ea8fe !important;
        }

        body.dark-mode .breadcrumb-item.active {
            color: #f5f5f5 !important;
        }

        body.dark-mode .pagination .page-link {
            background-color: #2d2d2d;
            color: #f5f5f5;
            border-color: #404040;
        }

        body.dark-mode .pagination .page-link:hover {
            background-color: #3a3a3a;
            color: #fff;
        }

        body.dark-mode .pagination .page-item.active .page-link {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #fff !important;
        }

        body.dark-mode input[readonly] {
            background-color: #2d2d2d !important;
            color: #bbb !important;
        }

        body.dark-mode .list-group-item {
            background-color: #2d2d2d;
            color: #f5f5f5;
            border-color: #404040;
        }

        body.dark-mode hr {
            border-color: #404040;
            opacity: 0.3;
        }
    </style>
</head>

<body>
    <div id="app">
        @include('layouts.blocks.sidebar-admin')

        <div id="main">
            @include('layouts.blocks.header')

            @yield('content')

            @include('layouts.blocks.footer')
        </div>
    </div>

    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
