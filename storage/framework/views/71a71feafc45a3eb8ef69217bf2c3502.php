<?php $__env->startSection('title', 'Import Lớp hành chính từ Excel'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import Lớp hành chính từ Excel</h3>
                    <p class="text-subtitle text-muted">Nhập hàng loạt lớp hành chính từ file Excel</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hanh-chinh.index')); ?>">Lớp hành chính</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Import Excel</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <!-- Hướng dẫn -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn Import</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Tải file template Excel bên dưới</li>
                        <li>Điền thông tin lớp hành chính vào file (bắt đầu từ dòng 2)</li>
                        <li>Đảm bảo các trường có dấu <span class="text-danger">*</span> không được để trống</li>
                        <li>
                            <strong>Lưu ý về thông tin:</strong>
                            <ul>
                                <li><strong>Mã lớp:</strong> Phải là duy nhất, không trùng lặp (VD: CNTT-K25-01)</li>
                                <li><strong>Khóa học:</strong> Nhập tên khóa học (VD: K25, K26, K2021-2025)</li>
                                <li><strong>Ngành:</strong> Nhập tên ngành hoặc mã ngành (VD: "Công nghệ thông tin" hoặc "CNTT")</li>
                                <li><strong>Giảng viên chủ nhiệm:</strong> Nhập tên hoặc mã giảng viên (VD: "Nguyễn Văn A" hoặc "GV001") - có thể để trống</li>
                            </ul>
                        </li>
                        <li>Upload file và nhấn Import</li>
                    </ol>

                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('dao-tao.lop-hanh-chinh.download-template')); ?>" class="btn btn-success btn-lg">
                            <i class="bi bi-download"></i> Tải Template Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Import -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload File Excel</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.lop-hanh-chinh.import')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <div class="mb-4">
                            <label for="file" class="form-label">Chọn file Excel <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="file"
                                name="file" accept=".xlsx,.xls,.csv,.txt" required>
                            <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Chấp nhận file .xlsx, .xls, .csv, .txt (tối đa 5MB)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-clock-history"></i>
                            <strong>Thời gian xử lý:</strong> Có thể mất vài phút tùy thuộc vào số lượng lớp.
                            Vui lòng không tắt trình duyệt trong khi import.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('dao-tao.lop-hanh-chinh.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Lớp hành chính
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Các trường trong template -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Danh sách trường trong file Excel</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Tên cột</th>
                                    <th>Bắt buộc</th>
                                    <th>Định dạng</th>
                                    <th>Ví dụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mã lớp</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi, không trùng</td>
                                    <td>CNTT-K25-01</td>
                                </tr>
                                <tr>
                                    <td>Tên lớp</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi</td>
                                    <td>Công Nghệ Thông Tin K25</td>
                                </tr>
                                <tr>
                                    <td>Khóa học</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Tên khóa học có sẵn</td>
                                    <td>K25</td>
                                </tr>
                                <tr>
                                    <td>Ngành</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Tên ngành hoặc mã ngành</td>
                                    <td>Công nghệ thông tin hoặc CNTT</td>
                                </tr>
                                <tr>
                                    <td>Giảng viên chủ nhiệm</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Tên hoặc mã giảng viên</td>
                                    <td>Nguyễn Văn A hoặc GV001</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lop-hanh-chinh/import.blade.php ENDPATH**/ ?>