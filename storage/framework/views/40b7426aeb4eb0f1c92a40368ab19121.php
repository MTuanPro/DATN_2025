<?php $__env->startSection('title', 'Lịch sử đăng ký môn học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Xem lại các môn đã đăng ký</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dang-ky-mon-hoc.index')); ?>">Đăng ký môn
                                    học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('sinh-vien.dang-ky-mon-hoc.my-registrations')); ?>" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Học kỳ</label>
                        <select name="hoc_ky_id" class="form-select">
                            <option value="">Tất cả</option>
                            <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hk->id); ?>" <?php echo e(request('hoc_ky_id') == $hk->id ? 'selected' : ''); ?>>
                                    <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="cho_xep_lop" <?php echo e(request('trang_thai') == 'cho_xep_lop' ? 'selected' : ''); ?>>Chờ
                                xếp lớp</option>
                            <option value="da_xep_lop" <?php echo e(request('trang_thai') == 'da_xep_lop' ? 'selected' : ''); ?>>Đã xếp
                                lớp</option>
                            <option value="that_bai" <?php echo e(request('trang_thai') == 'that_bai' ? 'selected' : ''); ?>>Thất bại
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Chờ xếp lớp</h6>
                        <h3 class="text-warning"><?php echo e($thongKe['cho_xep_lop']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đã xếp lớp</h6>
                        <h3 class="text-success"><?php echo e($thongKe['da_xep_lop']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Thất bại</h6>
                        <h3 class="text-danger"><?php echo e($thongKe['that_bai']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Tổng tín chỉ</h6>
                        <h3 class="text-primary"><?php echo e($thongKe['tong_tin_chi']); ?> TC</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách đăng ký -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Lịch sử đăng ký (<?php echo e($registrations->total()); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if($registrations->isEmpty()): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Bạn chưa có lịch sử đăng ký nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Môn học</th>
                                    <th>Tín chỉ</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Ưu tiên</th>
                                    <th>Trạng thái</th>
                                    <th>Lớp học phần</th>
                                    <th>Lý do thất bại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($registrations->firstItem() + $index); ?></td>
                                        <td>
                                            <strong><?php echo e($dk->monHoc->ten_mon); ?></strong>
                                            <br><small class="text-muted"><?php echo e($dk->monHoc->ma_mon); ?></small>
                                        </td>
                                        <td><?php echo e($dk->monHoc->tin_chi); ?> TC</td>
                                        <td><?php echo e($dk->hocKy->ten_hoc_ky); ?></td>
                                        <td><?php echo e($dk->ngay_dang_ky->format('d/m/Y H:i')); ?></td>
                                        <td>
                                            <?php if($dk->uu_tien >= 100): ?>
                                                <span class="badge bg-danger"><?php echo e($dk->uu_tien); ?></span>
                                            <?php elseif($dk->uu_tien >= 50): ?>
                                                <span class="badge bg-warning"><?php echo e($dk->uu_tien); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e($dk->uu_tien); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php switch($dk->trang_thai):
                                                case ('cho_xep_lop'): ?>
                                                    <span class="badge bg-warning">Chờ xếp lớp</span>
                                                <?php break; ?>

                                                <?php case ('da_xep_lop'): ?>
                                                    <span class="badge bg-success">Đã xếp lớp</span>
                                                <?php break; ?>

                                                <?php case ('that_bai'): ?>
                                                    <span class="badge bg-danger">Thất bại</span>
                                                <?php break; ?>
                                            <?php endswitch; ?>
                                        </td>
                                        <td>
                                            <?php if($dk->lopHocPhanSinhVien): ?>
                                                <code><?php echo e($dk->lopHocPhanSinhVien->lopHocPhan->ma_lop_hoc_phan); ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa xếp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($dk->trang_thai == 'that_bai' && $dk->ly_do_that_bai): ?>
                                                <small class="text-danger"><?php echo e($dk->ly_do_that_bai); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($registrations->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/sinhvien/dang-ky-mon-hoc/my-registrations.blade.php ENDPATH**/ ?>