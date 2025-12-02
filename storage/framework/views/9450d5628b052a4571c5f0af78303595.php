

<?php $__env->startSection('title', 'Thanh toán học phí qua VNPay'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thanh toán học phí qua VNPay</h3>
                    <p class="text-subtitle text-muted">Thanh toán nhanh chóng và an toàn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.hoc-phi.show', $hocPhi->id)); ?>">Chi tiết</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán VNPay</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if(session('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="alert alert-info">
                                <h5><i class="bi bi-info-circle"></i> Thông tin sinh viên</h5>
                                <p class="mb-1"><strong>MSSV:</strong> <?php echo e($hocPhi->sinhVien->ma_sinh_vien); ?></p>
                                <p class="mb-1"><strong>Họ tên:</strong> <?php echo e($hocPhi->sinhVien->ho_ten); ?></p>
                                <p class="mb-0"><strong>Học kỳ:</strong> <?php echo e($hocPhi->hocKy->ten_hoc_ky); ?> - <?php echo e($hocPhi->hocKy->nam_hoc); ?></p>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mô tả</th>
                                            <th class="text-end">Số tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Tổng học phí</td>
                                            <td class="text-end"><strong><?php echo e(number_format($hocPhi->tong_so_tien, 0, ',', '.')); ?> đ</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Đã thanh toán</td>
                                            <td class="text-end text-success"><?php echo e(number_format($hocPhi->so_tien_da_dong, 0, ',', '.')); ?> đ</td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td><strong>Còn lại</strong></td>
                                            <td class="text-end"><strong class="text-danger"><?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <form action="<?php echo e(route('sinh-vien.hoc-phi.vnpay-initiate', $hocPhi->id)); ?>" method="POST" id="paymentForm">
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Số tiền thanh toán <span class="text-danger">*</span></strong>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="so_tien_dong" 
                                               id="so_tien_dong" 
                                               class="form-control <?php $__errorArgs = ['so_tien_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               value="<?php echo e(old('so_tien_dong', $hocPhi->so_tien_con_lai)); ?>" 
                                               required 
                                               min="1000" 
                                               step="1000" 
                                               max="<?php echo e($hocPhi->so_tien_con_lai); ?>"
                                               placeholder="Nhập số tiền thanh toán">
                                        <span class="input-group-text">VND</span>
                                    </div>
                                    <?php $__errorArgs = ['so_tien_dong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">
                                        Số tiền tối thiểu: 1,000 đ | Số tiền tối đa: <?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ
                                    </small>
                                    <small id="so_tien_error" class="text-danger d-none"></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Ngân hàng (Tùy chọn)</strong>
                                    </label>
                                    <select name="bank_code" class="form-select">
                                        <option value="">-- Chọn ngân hàng (Để trống để chọn tất cả) --</option>
                                        <option value="VNBANK">Ngân hàng VNBank</option>
                                        <option value="VIETCOMBANK">Ngân hàng Vietcombank</option>
                                        <option value="VIETINBANK">Ngân hàng VietinBank</option>
                                        <option value="BIDV">Ngân hàng BIDV</option>
                                        <option value="TECHCOMBANK">Ngân hàng Techcombank</option>
                                        <option value="ACB">Ngân hàng ACB</option>
                                        <option value="SACOMBANK">Ngân hàng Sacombank</option>
                                        <option value="AGRIBANK">Ngân hàng Agribank</option>
                                        <option value="MBBANK">Ngân hàng MB Bank</option>
                                        <option value="TPBANK">Ngân hàng TPBank</option>
                                    </select>
                                    <small class="text-muted">Bạn có thể chọn ngân hàng cụ thể hoặc để trống để chọn tất cả</small>
                                </div>

                                <div class="mb-3">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="setAmount(<?php echo e($hocPhi->so_tien_con_lai); ?>)">
                                            Thanh toán toàn bộ
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="setAmount(<?php echo e(min(1000000, $hocPhi->so_tien_con_lai)); ?>)">
                                            1,000,000 đ
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="setAmount(<?php echo e(min(500000, $hocPhi->so_tien_con_lai)); ?>)">
                                            500,000 đ
                                        </button>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-exclamation-triangle"></i> Lưu ý:</h6>
                                    <ul class="mb-0">
                                        <li>Bạn sẽ được chuyển đến trang thanh toán của VNPay</li>
                                        <li>Vui lòng không đóng trình duyệt trong quá trình thanh toán</li>
                                        <li>Sau khi thanh toán thành công, bạn sẽ được chuyển về trang này</li>
                                        <li>Hỗ trợ thanh toán qua thẻ ATM, Internet Banking, và Ví điện tử</li>
                                        <li>Nếu có vấn đề, vui lòng liên hệ bộ phận tài vụ</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                                    </button>
                                    <a href="<?php echo e(route('sinh-vien.hoc-phi.show', $hocPhi->id)); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-shield-check"></i> Bảo mật</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Thanh toán được bảo mật bởi VNPay
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Không lưu trữ thông tin thẻ
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Giao dịch được mã hóa SSL
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Hỗ trợ nhiều ngân hàng
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Hỗ trợ 24/7
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-question-circle"></i> Hỗ trợ</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Hotline:</strong> 1900-xxxx</p>
                            <p class="mb-2"><strong>Email:</strong> support@university.edu.vn</p>
                            <p class="mb-0"><strong>Thời gian:</strong> 8:00 - 17:00 (T2-T6)</p>
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
        const soTienDongInput = document.getElementById('so_tien_dong');
        const soTienError = document.getElementById('so_tien_error');
        const soTienConLai = <?php echo e($hocPhi->so_tien_con_lai); ?>;
        const submitBtn = document.getElementById('submitBtn');
        const paymentForm = document.getElementById('paymentForm');

        // Validate số tiền không vượt quá số tiền còn lại
        if (soTienDongInput) {
            soTienDongInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                
                if (value < 1000) {
                    soTienError.textContent = 'Số tiền tối thiểu là 1,000 đ';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else if (value > soTienConLai) {
                    soTienError.textContent = 'Số tiền không được vượt quá số tiền còn lại';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else {
                    soTienError.classList.add('d-none');
                    this.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            });

            // Check on page load
            soTienDongInput.dispatchEvent(new Event('input'));
        }

        // Prevent double submission
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            });
        }
    });

    function setAmount(amount) {
        const input = document.getElementById('so_tien_dong');
        if (input) {
            input.value = amount;
            input.dispatchEvent(new Event('input'));
        }
    }
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/hoc-phi/vnpay-payment.blade.php ENDPATH**/ ?>