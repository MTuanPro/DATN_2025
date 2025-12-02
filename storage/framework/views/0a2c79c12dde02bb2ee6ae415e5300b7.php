

<?php $__env->startSection('title', 'Thanh toán VNPay - Test Mode'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thanh toán VNPay - Test Mode</h3>
                    <p class="text-subtitle text-muted">Chế độ test - Không cần tài khoản VNPay thật</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">
                                <i class="bi bi-exclamation-triangle"></i> Chế độ Test
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="bi bi-info-circle"></i> Thông báo</h5>
                                <p class="mb-0">
                                    Đây là chế độ test của VNPay. Bạn có thể mô phỏng các trường hợp thanh toán mà không cần tài khoản VNPay thật.
                                </p>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Lưu ý:</h6>
                                <ul class="mb-0">
                                    <li>Đây là giao dịch giả lập, không có tiền thật được chuyển</li>
                                    <li>Chỉ dùng cho mục đích phát triển và test</li>
                                    <li>Khi triển khai production, cần tắt TEST_MODE và cấu hình VNPay thật</li>
                                </ul>
                            </div>

                            <div class="text-center my-4">
                                <p><strong>Mã giao dịch:</strong> <?php echo e($orderId); ?></p>
                                <p><strong>Học phí ID:</strong> <?php echo e($hocPhiId); ?></p>
                            </div>

                            <form action="<?php echo e(route('sinh-vien.payment.vnpay.test.process')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="orderId" value="<?php echo e($orderId); ?>">
                                <input type="hidden" name="hoc_phi_id" value="<?php echo e($hocPhiId); ?>">

                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="success" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle"></i> Mô phỏng thanh toán thành công
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn btn-warning">
                                        <i class="bi bi-x-circle"></i> Mô phỏng hủy thanh toán
                                    </button>
                                    <button type="submit" name="action" value="fail" class="btn btn-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Mô phỏng thanh toán thất bại
                                    </button>
                                    <a href="<?php echo e(route('sinh-vien.hoc-phi.show', $hocPhiId)); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/hoc-phi/vnpay-test.blade.php ENDPATH**/ ?>