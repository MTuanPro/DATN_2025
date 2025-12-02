<?php $__env->startSection('title', 'Chi tiết Học phí'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết học phí của tôi</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e($hocPhi->hocKy->ten_hoc_ky); ?> - <?php echo e($hocPhi->hocKy->nam_hoc); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã môn</th>
                                            <th>Tên môn học</th>
                                            <th>Số tín chỉ</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $chiTietHienThi = $hocPhi->chiTietHocPhiMon->where('trang_thai', '!=', 'huy');
                                        ?>
                                        <?php $__currentLoopData = $chiTietHienThi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($ct->monHoc->ma_mon); ?></td>
                                                <td><?php echo e($ct->monHoc->ten_mon); ?></td>
                                                <td><?php echo e($ct->so_tin_chi); ?></td>
                                                <td><?php echo e(number_format($ct->don_gia_tin_chi, 0, ',', '.')); ?> đ</td>
                                                <td><?php echo e(number_format($ct->thanh_tien, 0, ',', '.')); ?> đ</td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Tổng hợp</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tổng học phí môn học:</td>
                                    <td class="text-end">
                                        <strong><?php echo e(number_format($hocPhi->tong_hoc_phi_mon_hoc, 0, ',', '.')); ?> đ</strong>
                                    </td>
                                </tr>
                                <?php if($hocPhi->phi_dich_vu > 0): ?>
                                <tr>
                                    <td>Phí dịch vụ:</td>
                                    <td class="text-end">
                                        <strong><?php echo e(number_format($hocPhi->phi_dich_vu, 0, ',', '.')); ?> đ</strong>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td><strong>Tổng học phí:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary"><?php echo e(number_format($hocPhi->tong_so_tien, 0, ',', '.')); ?> đ</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Đã đóng:</td>
                                    <td class="text-end text-success">
                                        <strong><?php echo e(number_format($hocPhi->so_tien_da_dong, 0, ',', '.')); ?> đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td>Còn lại:</td>
                                    <td class="text-end text-danger">
                                        <h4><?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hạn đóng:</td>
                                    <td class="text-end"><span
                                            class="badge bg-warning"><?php echo e($hocPhi->han_dong->format('d/m/Y')); ?></span></td>
                                </tr>
                                <tr>
                                    <td>Trạng thái:</td>
                                    <td class="text-end">
                                        <?php if($hocPhi->trang_thai == 'da_nop_du'): ?>
                                            <span class="badge bg-success">Đã nộp đủ</span>
                                        <?php elseif($hocPhi->trang_thai == 'qua_han'): ?>
                                            <span class="badge bg-danger">Quá hạn</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa nộp đủ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <?php if($hocPhi->so_tien_con_lai > 0): ?>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('sinh-vien.hoc-phi.momo-payment', $hocPhi->id)); ?>" class="btn btn-primary w-100">
                                        <i class="bi bi-wallet2"></i> Thanh toán qua MoMo
                                    </a>
                                    <a href="<?php echo e(route('sinh-vien.hoc-phi.vnpay-payment', $hocPhi->id)); ?>" class="btn btn-warning w-100 text-white">
                                        <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                                    </a>
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo e(route('sinh-vien.hoc-phi.lich-su', $hocPhi->id)); ?>" class="btn btn-info w-100 mt-2">
                                <i class="bi bi-clock-history"></i> Xem lịch sử đóng
                            </a>
                            <a href="<?php echo e(route('sinh-vien.hoc-phi.huong-dan')); ?>" class="btn btn-success w-100 mt-2">
                                <i class="bi bi-question-circle"></i> Hướng dẫn nộp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/hoc-phi/show.blade.php ENDPATH**/ ?>