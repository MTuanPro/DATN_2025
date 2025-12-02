<?php $__env->startSection('title', 'Import giảng viên từ Excel'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import giảng viên từ Excel</h3>
                    <p class="text-subtitle text-muted">Tải lên file Excel để thêm nhiều giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.giang-vien.index')); ?>">Giảng viên</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Import</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hướng dẫn import</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Lưu ý quan trọng:</h5>
                                <ol class="mb-0">
                                    <li>File import phải ở định dạng <strong>.xlsx</strong> hoặc <strong>.csv</strong></li>
                                    <li>Tải file mẫu bên dưới để xem cấu trúc dữ liệu</li>
                                    <li>Không thay đổi tên các cột trong file mẫu</li>
                                    <li>Mã giảng viên: Nếu đã tồn tại, hệ thống sẽ tự động cập nhật thông tin</li>
                                    <li>Email: Phải là duy nhất (không trùng lặp với giảng viên khác)</li>
                                    <li>Các trường có dấu (*) là bắt buộc phải nhập</li>
                                    <li>Định dạng ngày sinh: Hỗ trợ nhiều định dạng: <code>YYYY-MM-DD</code> (1990-05-15), <code>DD/MM/YYYY</code> (15/05/1990), <code>DD-MM-YYYY</code> (15-05-1990)</li>
                                    <li><strong>Lưu ý:</strong> Nếu Mã giảng viên đã tồn tại, hệ thống sẽ tự động cập nhật thông tin thay vì tạo mới</li>
                                    <li>Giới tính: <code>Nam</code>, <code>Nữ</code> hoặc <code>Khác</code></li>
                                    <li><strong class="text-primary">Tài khoản đăng nhập:</strong> Hệ thống sẽ tự động tạo tài khoản đăng nhập cho tất cả giảng viên với mật khẩu mặc định: <code class="text-danger">12345678</code></li>
                                </ol>
                            </div>

                            <div class="mb-3">
                                <a href="<?php echo e(route('dao-tao.giang-vien.download-template')); ?>" class="btn btn-success">
                                    <i class="bi bi-download"></i> Tải file mẫu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tải lên file Excel</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('dao-tao.giang-vien.import')); ?>" method="POST"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="form-group">
                                    <label for="file" class="form-label">Chọn file Excel <span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="file" name="file" accept=".xlsx,.xls,.csv" required>
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
                                    <small class="text-muted">Chấp nhận file: .xlsx, .xls, .csv (Tối đa 5MB)</small>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload"></i> Bắt đầu Import
                                    </button>
                                    <a href="<?php echo e(route('dao-tao.giang-vien.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Cấu trúc file Excel mẫu</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ma_giang_vien *</th>
                                            <th>ho_ten *</th>
                                            <th>email *</th>
                                            <th>khoa *</th>
                                            <th>trinh_do</th>
                                            <th>so_dien_thoai</th>
                                            <th>ngay_sinh</th>
                                            <th>gioi_tinh</th>
                                            <th>dia_chi</th>
                                            <th>chuyen_mon</th>
                                            <th>ngay_vao_truong</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>GV001</td>
                                            <td>Nguyễn Văn A</td>
                                            <td>gv001@example.com</td>
                                            <td>Công nghệ thông tin</td>
                                            <td>Thạc sĩ</td>
                                            <td>0912345678</td>
                                            <td>1985-05-15</td>
                                            <td>Nam</td>
                                            <td>Hà Nội</td>
                                            <td>Lập trình</td>
                                            <td>2020-01-01</td>
                                        </tr>
                                        <tr>
                                            <td>GV002</td>
                                            <td>Trần Thị B</td>
                                            <td>gv002@example.com</td>
                                            <td>Kế toán</td>
                                            <td>Tiến sĩ</td>
                                            <td>0987654321</td>
                                            <td>1990-10-20</td>
                                            <td>Nữ</td>
                                            <td>TP. HCM</td>
                                            <td>Kế toán tài chính</td>
                                            <td>2021-06-01</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">
                                <strong>Ghi chú:</strong>
                                <ul class="mb-0">
                                    <li><strong>khoa</strong>: Nhập tên khoa hoặc mã khoa (VD: "Công nghệ thông tin" hoặc "CNTT")</li>
                                    <li><strong>trinh_do</strong>: Nhập tên trình độ (VD: "Thạc sĩ", "Tiến sĩ", "Cử nhân") - có thể để trống</li>
                                    <li><strong>ngay_sinh</strong>: Định dạng YYYY-MM-DD (VD: 1985-05-15)</li>
                                    <li><strong>gioi_tinh</strong>: Nam, Nữ hoặc Khác</li>
                                    <li><strong>ngay_vao_truong</strong>: Định dạng YYYY-MM-DD (VD: 2020-01-01) - mặc định là hôm nay nếu để trống</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/giang-vien/import.blade.php ENDPATH**/ ?>