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
            color: #e0e0e0;
        }

        body.dark-mode .card {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        body.dark-mode .table {
            color: #e0e0e0;
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
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

        body.dark-mode .sidebar-link {
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-link:hover {
            background-color: #2d2d2d;
        }

        body.dark-mode .sidebar-item.active > .sidebar-link {
            background-color: #435ebe;
        }

        body.dark-mode .sidebar-title {
            color: #999;
        }

        body.dark-mode .submenu {
            background-color: #252525;
        }

        body.dark-mode .submenu .submenu-item a {
            color: #ccc;
        }

        body.dark-mode .submenu .submenu-item a:hover {
            background-color: #2d2d2d;
        }

        body.dark-mode .top-header {
            background-color: #2d2d2d;
            color: #e0e0e0;
            border-bottom: 1px solid #333;
        }

        body.dark-mode footer {
            background-color: #2d2d2d !important;
            color: #e0e0e0;
        }

        body.dark-mode .text-muted {
            color: #aaa !important;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: #3a3a3a;
            color: #e0e0e0;
            border-color: #555;
        }

        body.dark-mode .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
        }

        body.dark-mode .dropdown-menu {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        body.dark-mode .dropdown-item {
            color: #e0e0e0;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #3a3a3a;
        }

        body.dark-mode .alert {
            border-color: #555;
        }

        body.dark-mode .alert-success {
            background-color: #1e4620;
            color: #a8e6a3;
        }

        body.dark-mode .alert-danger {
            background-color: #4d1f1f;
            color: #f8b4b4;
        }

        body.dark-mode .alert-info {
            background-color: #1f3a4d;
            color: #a3d7f8;
        }

        body.dark-mode .badge {
            border: 1px solid #555;
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
