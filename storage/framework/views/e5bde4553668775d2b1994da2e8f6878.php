<?php $__env->startSection('title', 'Lịch học chi tiết'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học chi tiết</h3>
                    <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active">Lịch chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách lịch học chi tiết</h5>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#generateModal">
                                <i class="bi bi-calendar-check"></i> Tạo tự động
                            </button>
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-chi-tiet.create', $lopHocPhan)); ?>"
                                class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm buổi học
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày học</th>
                                    <th>Ca</th>
                                    <th>Giờ</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lichHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lichHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e(Carbon\Carbon::parse($lichHoc->ngay_hoc)->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php if($lichHoc->caHoc): ?>
                                                <span class="badge bg-info"><?php echo e($lichHoc->caHoc->ten_ca); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(Carbon\Carbon::parse($lichHoc->gio_bat_dau)->format('H:i')); ?> -
                                            <?php echo e(Carbon\Carbon::parse($lichHoc->gio_ket_thuc)->format('H:i')); ?></td>
                                        <td><?php echo e($lichHoc->phongHoc->ten_phong ?? '-'); ?></td>
                                        <td><?php echo e($lichHoc->giangVien->ho_ten ?? '-'); ?></td>
                                        <td>
                                            <?php if($lichHoc->trang_thai == 'chua_day'): ?>
                                                <span class="badge bg-secondary">Chưa dạy</span>
                                            <?php elseif($lichHoc->trang_thai == 'dang_day'): ?>
                                                <span class="badge bg-info">Đang dạy</span>
                                            <?php elseif($lichHoc->trang_thai == 'da_day'): ?>
                                                <span class="badge bg-success">Đã dạy</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.lich-chi-tiet.edit', $lichHoc)); ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if($lichHoc->trang_thai != 'huy'): ?>
                                                    <form action="<?php echo e(route('dao-tao.lich-chi-tiet.cancel', $lichHoc)); ?>"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy buổi học này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('dao-tao.lich-chi-tiet.destroy', $lichHoc)); ?>"
                                                    method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Chưa có lịch học chi tiết nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php echo e($lichHocs->links()); ?>


                    <div class="mt-3">
                        <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal tạo lịch tự động -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('dao-tao.lop-hoc-phan.lich-chi-tiet.generate', $lopHocPhan)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tạo lịch học tự động</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tạo lịch chi tiết tự động từ lịch cố định đã thiết lập</p>
                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
                        </div>
                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Tạo lịch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-hoc-chi-tiet/index.blade.php ENDPATH**/ ?>