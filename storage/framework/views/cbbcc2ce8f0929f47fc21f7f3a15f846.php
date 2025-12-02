<?php $__env->startSection('title', 'Import Sinh viên từ Excel'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import Sinh viên từ Excel</h3>
                    <p class="text-subtitle text-muted">Nhập hàng loạt sinh viên từ file Excel</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.sinh-vien.index')); ?>">Sinh viên</a></li>
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
                        <li>Điền thông tin sinh viên vào file (bắt đầu từ dòng 2)</li>
                        <li><strong>Lưu ý:</strong> Nếu MSSV đã tồn tại, hệ thống sẽ tự động cập nhật thông tin thay vì tạo mới</li>
                        <li>Đảm bảo các trường có dấu <span class="text-danger">*</span> không được để trống</li>
                        <li>
                            <strong>Lưu ý về thông tin:</strong>
                            <ul>
                                <li><strong>Mã lớp:</strong> Phải tồn tại trong hệ thống (VD: CNTT-K15-01)</li>
                                <li><strong>Khóa học:</strong> Nhập tên khóa học (VD: K15, K16, K2021-2025)</li>
                                <li><strong>Ngành:</strong> Nhập tên ngành hoặc mã ngành (VD: "Công nghệ thông tin" hoặc "CNTT")</li>
                                <li><strong>Chuyên ngành:</strong> Nhập mã chuyên ngành (VD: CNTT01) - Có thể để trống</li>
                                <li><strong>Trạng thái:</strong> Nhập tên trạng thái hoặc mã (VD: "Đang học" hoặc "DANG_HOC")</li>
                            </ul>
                        </li>
                        <li>Upload file và nhấn Import</li>
                        <li><strong>Mật khẩu mặc định:</strong> Là Mã sinh viên</li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Chú ý:</strong> Hệ thống sẽ tự động tạo tài khoản đăng nhập cho sinh viên với:
                        <ul class="mb-0 mt-2">
                            <li>Email: Theo file Excel</li>
                            <li>Mật khẩu: Mã sinh viên</li>
                            <li>Vai trò: Sinh viên</li>
                        </ul>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('dao-tao.sinh-vien.download-template')); ?>" class="btn btn-success btn-lg">
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
                    <form action="<?php echo e(route('dao-tao.sinh-vien.import')); ?>" method="POST" enctype="multipart/form-data">
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
                            <strong>Thời gian xử lý:</strong> Có thể mất vài phút tùy thuộc vào số lượng sinh viên.
                            Vui lòng không tắt trình duyệt trong khi import.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('dao-tao.sinh-vien.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Sinh viên
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
                                    <td>Mã SV</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi, không trùng</td>
                                    <td>2021600001</td>
                                </tr>
                                <tr>
                                    <td>Họ tên</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi</td>
                                    <td>Nguyễn Văn A</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Email, không trùng</td>
                                    <td>nva@example.com</td>
                                </tr>
                                <tr>
                                    <td>Ngày sinh</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>YYYY-MM-DD</td>
                                    <td>2003-01-15</td>
                                </tr>
                                <tr>
                                    <td>Giới tính</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>nam/nu/khac</td>
                                    <td>nam</td>
                                </tr>
                                <tr>
                                    <td>SĐT</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Số điện thoại</td>
                                    <td>0901234567</td>
                                </tr>
                                <tr>
                                    <td>CCCD</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>12 số, không trùng</td>
                                    <td>001203012345</td>
                                </tr>
                                <tr>
                                    <td>Mã lớp</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã lớp có sẵn</td>
                                    <td>CNTT-K15</td>
                                </tr>
                                <tr>
                                    <td>Khóa học</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Tên khóa học có sẵn</td>
                                    <td>K15</td>
                                </tr>
                                <tr>
                                    <td>Ngành</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Tên ngành hoặc mã ngành</td>
                                    <td>Công nghệ thông tin hoặc CNTT</td>
                                </tr>
                                <tr>
                                    <td>Chuyên ngành</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Mã chuyên ngành</td>
                                    <td>CNTT01</td>
                                </tr>
                                <tr>
                                    <td>Kỳ hiện tại</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Số từ 1-8</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td>Trạng thái</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Tên trạng thái hoặc mã (mặc định: Đang học)</td>
                                    <td>Đang học hoặc DANG_HOC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/sinh-vien/import.blade.php ENDPATH**/ ?>