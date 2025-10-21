<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Giảng viên Dashboard') - S-MIS21</title>

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
    </style>
</head>

<body>
    <div id="app">
        @include('layouts.blocks.sidebar-giangvien')

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
