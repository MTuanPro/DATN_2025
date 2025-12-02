<?php $__env->startSection('title', 'Ghi nhận thanh toán'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ghi nhận thanh toán</h3>
                    <p class="text-subtitle text-muted">Ghi nhận khoản thanh toán học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ghi nhận thanh toán</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?php echo e(route('dao-tao.hoc-phi.storePayment', $hocPhi->id)); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="alert alert-info">
                                    <h5>Thông tin sinh viên</h5>
                                    <p class="mb-0"><strong>MSSV:</strong> <?php echo e($hocPhi->sinhVien->ma_sinh_vien); ?></p>
                                    <p class="mb-0"><strong>Họ tên:</strong> <?php echo e($hocPhi->sinhVien->ho_ten); ?></p>
                                    <p class="mb-0"><strong>Học kỳ:</strong> <?php echo e($hocPhi->hocKy->ten_hoc_ky); ?></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                                    <input type="number" name="so_tien_dong" id="so_tien_dong" class="form-control <?php $__errorArgs = ['so_tien_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('so_tien_dong')); ?>" required min="1000" step="1000" max="<?php echo e($hocPhi->so_tien_con_lai); ?>"
                                           placeholder="Nhập số tiền thanh toán">
                                    <?php $__errorArgs = ['so_tien_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Số tiền còn lại: <strong class="text-danger"><?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ</strong></small>
                                    <small id="so_tien_error" class="text-danger d-none"></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày đóng <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_dong" class="form-control <?php $__errorArgs = ['ngay_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('ngay_dong', date('Y-m-d'))); ?>" required>
                                    <?php $__errorArgs = ['ngay_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                    <select name="phuong_thuc_thanh_toan" id="phuong_thuc_thanh_toan" class="form-select <?php $__errorArgs = ['phuong_thuc_thanh_toan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">-- Chọn phương thức --</option>
                                        <option value="Tiền mặt" <?php echo e(old('phuong_thuc_thanh_toan') == 'Tiền mặt' ? 'selected' : ''); ?>>Tiền mặt</option>
                                        <option value="Chuyển khoản" <?php echo e(old('phuong_thuc_thanh_toan') == 'Chuyển khoản' ? 'selected' : ''); ?>>Chuyển khoản</option>
                                        <option value="Thẻ ATM" <?php echo e(old('phuong_thuc_thanh_toan') == 'Thẻ ATM' ? 'selected' : ''); ?>>Thẻ ATM</option>
                                    </select>
                                    <?php $__errorArgs = ['phuong_thuc_thanh_toan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3" id="ngan_hang_field" style="display: none;">
                                    <label class="form-label">Ngân hàng</label>
                                    <input type="text" name="ngan_hang" class="form-control <?php $__errorArgs = ['ngan_hang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('ngan_hang')); ?>" placeholder="Tên ngân hàng (nếu chuyển khoản)">
                                    <?php $__errorArgs = ['ngan_hang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú (nếu có)"><?php echo e(old('ghi_chu')); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Biên lai thanh toán (nếu có)</label>
                                    <input type="file" name="bien_lai_file" class="form-control <?php $__errorArgs = ['bien_lai_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           accept="image/*,.pdf">
                                    <?php $__errorArgs = ['bien_lai_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?php echo e(route('dao-tao.hoc-phi.show', $hocPhi->id)); ?>" class="btn btn-secondary">Hủy</a>
                                    <button type="submit" class="btn btn-success">Ghi nhận thanh toán</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Tổng hợp học phí</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tổng học phí:</td>
                                    <td class="text-end"><strong><?php echo e(number_format($hocPhi->tong_so_tien, 0, ',', '.')); ?> đ</strong></td>
                                </tr>
                                <tr>
                                    <td>Đã đóng:</td>
                                    <td class="text-end text-success"><strong><?php echo e(number_format($hocPhi->so_tien_da_dong, 0, ',', '.')); ?> đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td>Còn lại:</td>
                                    <td class="text-end text-danger"><h4><?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ</h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phuongThucSelect = document.getElementById('phuong_thuc_thanh_toan');
        const nganHangField = document.getElementById('ngan_hang_field');
        const soTienDongInput = document.getElementById('so_tien_dong');
        const soTienError = document.getElementById('so_tien_error');
        const soTienConLai = <?php echo e($hocPhi->so_tien_con_lai); ?>;
        
        // Hiển thị/ẩn trường ngân hàng
        if (phuongThucSelect && nganHangField) {
            phuongThucSelect.addEventListener('change', function() {
                if (this.value === 'Chuyển khoản') {
                    nganHangField.style.display = 'block';
                } else {
                    nganHangField.style.display = 'none';
                }
            });
            
            // Check on page load (for old input)
            if (phuongThucSelect.value === 'Chuyển khoản') {
                nganHangField.style.display = 'block';
            }
        }
        
        // Validate số tiền không vượt quá số tiền còn lại
        if (soTienDongInput && soTienError) {
            soTienDongInput.addEventListener('input', function() {
                const soTien = parseFloat(this.value) || 0;
                if (soTien > soTienConLai) {
                    soTienError.textContent = 'Số tiền đóng không được vượt quá số tiền còn lại (' + new Intl.NumberFormat('vi-VN').format(soTienConLai) + ' đ)';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                } else {
                    soTienError.classList.add('d-none');
                    this.classList.remove('is-invalid');
                }
            });
            
            // Validate khi submit form
            const form = soTienDongInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const soTien = parseFloat(soTienDongInput.value) || 0;
                    if (soTien > soTienConLai) {
                        e.preventDefault();
                        soTienError.textContent = 'Số tiền đóng không được vượt quá số tiền còn lại (' + new Intl.NumberFormat('vi-VN').format(soTienConLai) + ' đ)';
                        soTienError.classList.remove('d-none');
                        soTienDongInput.classList.add('is-invalid');
                        soTienDongInput.focus();
                        return false;
                    }
                });
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-phi/payment.blade.php ENDPATH**/ ?>