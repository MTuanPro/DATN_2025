<?php $__env->startSection('title', 'Phân công Giảng dạy'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Phân công Giảng dạy</h3>
                    <p class="text-subtitle text-muted">Lớp: <?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->ten_lop_hp); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Phân công GV</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin lớp học phần -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Môn học:</strong> <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
                            <p><strong>Học kỳ:</strong> <?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> -
                                <?php echo e($lopHocPhan->hocKy->nam_hoc); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sĩ số:</strong> <?php echo e($lopHocPhan->so_luong_dang_ky); ?>/<?php echo e($lopHocPhan->suc_chua); ?></p>
                            <p><strong>Hình thức:</strong>
                                <?php if($lopHocPhan->hinh_thuc == 'offline'): ?>
                                    <span class="badge bg-secondary">Offline</span>
                                <?php elseif($lopHocPhan->hinh_thuc == 'online'): ?>
                                    <span class="badge bg-primary">Online</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Hybrid</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form phân công -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thêm Giảng viên</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('dao-tao.lop-hoc-phan.phan-cong.store', $lopHocPhan->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                                <select name="giang_vien_id"
                                    class="form-select <?php $__errorArgs = ['giang_vien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Chọn giảng viên --</option>
                                    <?php $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gv->id); ?>"><?php echo e($gv->ma_giang_vien); ?> -
                                            <?php echo e($gv->ho_ten); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['giang_vien_id'];
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
                            <div class="col-md-3">
                                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                <select name="vai_tro" class="form-select <?php $__errorArgs = ['vai_tro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="giang_vien_chinh">Giảng viên chính</option>
                                    <option value="giang_vien_phu">Giảng viên phụ</option>
                                    <option value="tro_giang">Trợ giảng</option>
                                </select>
                                <?php $__errorArgs = ['vai_tro'];
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
                            <div class="col-md-4">
                                <label class="form-label">Ghi chú</label>
                                <input type="text" name="ghi_chu" class="form-control" placeholder="Ghi chú...">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Phân công
                            </button>
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách giảng viên đã phân công -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Giảng viên</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Vai trò</th>
                                    <th>Ngày phân công</th>
                                    <th>Người phân công</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhan->lopHocPhanGiangVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($pc->giangVien->ma_giang_vien); ?></td>
                                        <td><?php echo e($pc->giangVien->ho_ten); ?></td>
                                        <td>
                                            <?php if($pc->vai_tro == 'giang_vien_chinh'): ?>
                                                <span class="badge bg-primary">Giảng viên chính</span>
                                            <?php elseif($pc->vai_tro == 'giang_vien_phu'): ?>
                                                <span class="badge bg-info">Giảng viên phụ</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($pc->ngay_phan_cong ? date('d/m/Y', strtotime($pc->ngay_phan_cong)) : 'N/A'); ?>

                                        </td>
                                        <td><?php echo e($pc->nguoiPhanCong->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($pc->phan_cong_giang_day ?? '-'); ?></td>
                                        <td>
                                            <form action="<?php echo e(route('dao-tao.phan-cong.destroy', $pc->id)); ?>" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có giảng viên nào được phân công</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/phan-cong-giang-day/index.blade.php ENDPATH**/ ?>