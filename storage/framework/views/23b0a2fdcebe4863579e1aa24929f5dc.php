<?php $__env->startSection('title', 'Lịch học cố định'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học cố định</h3>
                    <p class="text-subtitle text-muted">
                        <?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?>

                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch học cố định</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách lịch học cố định</h5>
                        <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh.create', $lopHocPhan)); ?>"
                            class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm lịch học
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($lichHocs->isEmpty()): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có lịch học cố định nào.
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh.create', $lopHocPhan)); ?>">Thêm mới</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i> Tổng số: <strong><?php echo e($lichHocs->count()); ?></strong> buổi học
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="10%">Thứ</th>
                                        <th width="15%">Ca học</th>
                                        <th width="15%">Giờ học</th>
                                        <th width="15%">Phòng</th>
                                        <th width="20%">Giảng viên</th>
                                        <th width="10%">Hình thức</th>
                                        <th width="10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lichHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lichHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><strong><?php echo e($lichHoc->ten_thu); ?></strong></td>
                                            <td>
                                                <?php if($lichHoc->caHoc): ?>
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-clock"></i> <?php echo e($lichHoc->caHoc->ten_ca); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Tiết <?php echo e($lichHoc->tiet_bat_dau); ?>-<?php echo e($lichHoc->tiet_ket_thuc); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="bi bi-clock-history"></i>
                                                <?php if($lichHoc->caHoc): ?>
                                                    <?php echo e(Carbon\Carbon::parse($lichHoc->caHoc->gio_bat_dau)->format('H:i')); ?> - 
                                                    <?php echo e(Carbon\Carbon::parse($lichHoc->caHoc->gio_ket_thuc)->format('H:i')); ?>

                                                <?php else: ?>
                                                    <?php echo e(Carbon\Carbon::parse($lichHoc->gio_bat_dau)->format('H:i')); ?> - 
                                                    <?php echo e(Carbon\Carbon::parse($lichHoc->gio_ket_thuc)->format('H:i')); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="bi bi-door-open"></i>
                                                <?php echo e($lichHoc->phongHoc->ten_phong ?? '-'); ?>

                                                <?php if($lichHoc->phongHoc): ?>
                                                    <small class="text-muted">(<?php echo e($lichHoc->phongHoc->suc_chua); ?> chỗ)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="bi bi-person"></i>
                                                <?php echo e($lichHoc->giangVien->ho_ten ?? '-'); ?>

                                            </td>
                                            <td>
                                                <?php if($lichHoc->hinh_thuc == 'offline'): ?>
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-building"></i> Offline
                                                    </span>
                                                <?php elseif($lichHoc->hinh_thuc == 'online'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-camera-video"></i> Online
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-laptop"></i> Hybrid
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('dao-tao.lich-co-dinh.edit', $lichHoc)); ?>"
                                                        class="btn btn-sm btn-outline-warning"
                                                        title="Sửa">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('dao-tao.lich-co-dinh.destroy', $lichHoc)); ?>"
                                                        method="POST"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn xóa buổi học này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-hoc-co-dinh/index.blade.php ENDPATH**/ ?>