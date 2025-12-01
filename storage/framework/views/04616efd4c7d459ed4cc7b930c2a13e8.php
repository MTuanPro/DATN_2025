<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Đào tạo Dashboard'); ?> - S-MIS21</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/iconly/bold.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-icons/bootstrap-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.svg')); ?>" type="image/x-icon">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <?php echo $__env->yieldPushContent('styles'); ?>

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

        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #ffffff !important;
        }


        body.dark-mode .card {
            background-color: #2d2d2d;
            color: #ffffff;
            border-color: #404040;
        }

        body.dark-mode .card-header {
            background-color: #252525;
            color: #ffffff;
            border-bottom: 1px solid #404040;
        }

        body.dark-mode .card-title,
        body.dark-mode .card-text,
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, 
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
        body.dark-mode p, body.dark-mode span, body.dark-mode label,
        body.dark-mode .page-heading h3 {
            color: #ffffff !important;
        }

        body.dark-mode .table {
            color: #ffffff;
        }

        body.dark-mode .table thead th {
            color: #ffffff;
            background-color: #252525;
            border-color: #404040;
        }

        body.dark-mode .table tbody td {
            color: #ffffff;
            border-color: #404040;
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

        body.dark-mode .sidebar-header h3 {
            color: #ffffff !important;
        }

        body.dark-mode .sidebar-link {
            color: #ffffff !important;
        }

        body.dark-mode .sidebar-link:hover {
            background-color: #2d2d2d;
            color: #ffffff !important;
        }

        body.dark-mode .sidebar-item.active > .sidebar-link {
            background-color: #435ebe;
            color: #ffffff !important;
        }

        body.dark-mode .sidebar-title {
            color: #aaa !important;
        }

        body.dark-mode .submenu {
            background-color: #252525;
        }

        body.dark-mode .submenu .submenu-item a {
            color: #ffffff !important;
        }

        body.dark-mode .submenu .submenu-item a:hover {
            background-color: #2d2d2d;
            color: #ffffff !important;
        }

        /* Header dark mode - Nền cùng màu sidebar */
        body.dark-mode .top-header {
            background-color: #1e1e1e !important;
            color: #ffffff !important;
            border-bottom: 1px solid #404040;
        }

        body.dark-mode .top-header * {
            color: #ffffff !important;
        }

        body.dark-mode .top-header .btn,
        body.dark-mode .top-header button {
            background-color: transparent;
            color: #ffffff !important;
            border: none;
        }

        body.dark-mode .top-header .btn:hover,
        body.dark-mode .top-header button:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }

        body.dark-mode .top-header .btn-link {
            color: #ffffff !important;
        }

        body.dark-mode .top-header .text-muted {
            color: #ffffff !important;
        }

        body.dark-mode .top-header i {
            color: #ffffff !important;
        }

        body.dark-mode .top-header .dropdown-toggle {
            color: #ffffff !important;
        }

        body.dark-mode .top-header span {
            color: #ffffff !important;
        }

        body.dark-mode .top-header .badge {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }

        body.dark-mode footer {
            background-color: #2d2d2d !important;
            color: #ffffff !important;
        }

        body.dark-mode footer * {
            color: #ffffff !important;
        }

        body.dark-mode .text-muted {
            color: #bbb !important;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode textarea {
            background-color: #3a3a3a;
            color: #ffffff !important;
            border-color: #555;
        }

        body.dark-mode .form-control::placeholder {
            color: #999 !important;
        }

        body.dark-mode .form-label {
            color: #ffffff !important;
        }

        body.dark-mode .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #ffffff !important;
        }

        body.dark-mode .dropdown-menu {
            background-color: #2d2d2d;
            color: #ffffff;
            border-color: #404040;
        }

        body.dark-mode .dropdown-item {
            color: #ffffff !important;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #3a3a3a;
            color: #ffffff !important;
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
        }

        body.dark-mode .alert-danger {
            background-color: #4d1f1f;
            color: #f5c2c7 !important;
        }

        body.dark-mode .alert-info {
            background-color: #1f3a4d;
            color: #b6d4fe !important;
        }

        body.dark-mode .badge {
            border: 1px solid #555;
        }

        /* Đảm bảo các phần tử có nền sáng vẫn có chữ tối để dễ đọc */
        body.dark-mode .badge.bg-primary,
        body.dark-mode .badge.bg-success,
        body.dark-mode .badge.bg-info,
        body.dark-mode .badge.bg-warning,
        body.dark-mode .badge.bg-danger {
            color: #ffffff !important;
        }

        body.dark-mode .badge.bg-light,
        body.dark-mode .badge.bg-secondary {
            color: #212529 !important;
            background-color: #e9ecef !important;
        }

        body.dark-mode .btn-primary,
        body.dark-mode .btn-success,
        body.dark-mode .btn-info,
        body.dark-mode .btn-danger,
        body.dark-mode .btn-warning {
            color: #ffffff !important;
        }

        body.dark-mode .btn-light,
        body.dark-mode .btn-secondary {
            color: #212529 !important;
            background-color: #e9ecef !important;
            border-color: #dee2e6 !important;
        }

        body.dark-mode .btn-light:hover,
        body.dark-mode .btn-secondary:hover {
            background-color: #d3d3d3 !important;
            color: #212529 !important;
        }

        /* Breadcrumb */
        body.dark-mode .breadcrumb {
            background-color: transparent;
        }

        body.dark-mode .breadcrumb-item a {
            color: #6c757d !important;
        }

        body.dark-mode .breadcrumb-item.active {
            color: #e9ecef !important;
        }

        /* Pagination */
        body.dark-mode .pagination .page-link {
            background-color: #2d2d2d;
            color: #e9ecef;
            border-color: #404040;
        }

        body.dark-mode .pagination .page-link:hover {
            background-color: #3a3a3a;
            color: #ffffff;
        }

        body.dark-mode .pagination .page-item.active .page-link {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #ffffff;
        }

        /* Modal */
        body.dark-mode .modal-content {
            background-color: #2d2d2d;
            color: #e9ecef;
            border-color: #404040;
        }

        body.dark-mode .modal-header {
            border-bottom-color: #404040;
        }

        body.dark-mode .modal-footer {
            border-top-color: #404040;
        }

        body.dark-mode .modal-title {
            color: #ffffff !important;
        }

        /* Input group */
        body.dark-mode .input-group-text {
            background-color: #2b3035;
            color: #e9ecef;
            border-color: #4a5057;
        }

        /* List group */
        body.dark-mode .list-group-item {
            background-color: #2d2d2d;
            color: #e9ecef;
            border-color: #404040;
        }

        body.dark-mode .list-group-item.active {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #ffffff;
        }

        /* Nav tabs */
        body.dark-mode .nav-tabs {
            border-bottom-color: #404040;
        }

        body.dark-mode .nav-tabs .nav-link {
            color: #e9ecef;
            border-color: transparent;
        }

        body.dark-mode .nav-tabs .nav-link:hover {
            border-color: #404040;
            color: #ffffff;
        }

        body.dark-mode .nav-tabs .nav-link.active {
            background-color: #2d2d2d;
            border-color: #404040 #404040 #2d2d2d;
            color: #ffffff;
        }

        /* Select2 Dark Mode */
        body.dark-mode .select2-container--bootstrap-5 .select2-selection {
            background-color: #3a3a3a !important;
            border-color: #555 !important;
            color: #ffffff !important;
        }

        body.dark-mode .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
        }

        body.dark-mode .select2-dropdown {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
        }

        body.dark-mode .select2-results__option {
            background-color: #2d2d2d !important;
            color: #ffffff !important;
        }

        body.dark-mode .select2-results__option--highlighted {
            background-color: #435ebe !important;
            color: #ffffff !important;
        }

        body.dark-mode .select2-search--dropdown .select2-search__field {
            background-color: #3a3a3a !important;
            color: #ffffff !important;
            border-color: #555 !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <?php echo $__env->make('layouts.blocks.sidebar-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div id="main">
            <?php echo $__env->make('layouts.blocks.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->yieldContent('content'); ?>

            <?php echo $__env->make('layouts.blocks.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendors/apexcharts/apexcharts.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Khởi tạo Select2 cho tất cả select -->
    <script>
        // Hàm khởi tạo Select2
        function initSelect2() {
            $('select:not(.no-select2):not(.select2-hidden-accessible)').select2({
                theme: 'bootstrap-5',
                language: {
                    noResults: function() {
                        return "Không tìm thấy kết quả";
                    },
                    searching: function() {
                        return "Đang tìm kiếm...";
                    }
                },
                placeholder: function() {
                    return $(this).find('option[value=""]').text() || '-- Chọn --';
                },
                allowClear: true,
                width: '100%'
            });
        }

        // Khởi tạo khi DOM ready
        $(document).ready(function() {
            initSelect2();
        });

        // Khởi tạo lại khi modal được mở (cho các select trong modal)
        $(document).on('shown.bs.modal', '.modal', function() {
            initSelect2();
        });

        // Khởi tạo lại cho các select được tạo động
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).find('select:not(.no-select2):not(.select2-hidden-accessible)').length > 0) {
                initSelect2();
            }
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/layouts/layout-daotao.blade.php ENDPATH**/ ?>