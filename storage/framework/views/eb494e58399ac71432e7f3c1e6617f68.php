<?php $__env->startSection('title', 'Cấu hình Đầu điểm'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cấu hình Đầu điểm</h3>
                    <p class="text-subtitle text-muted">Lớp: <?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->ten_lop_hp); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Cấu hình điểm</li>
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
                        <div class="col-md-8">
                            <p><strong>Môn học:</strong> <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
                            <p><strong>Học kỳ:</strong> <?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> -
                                <?php echo e($lopHocPhan->hocKy->nam_hoc); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4>Tổng tỷ lệ:
                                <span class="badge <?php echo e($summary['total_percentage'] == 100 ? 'bg-success' : 'bg-warning'); ?>">
                                    <?php echo e($summary['total_percentage']); ?>%
                                </span>
                            </h4>
                            <p class="text-muted">Còn lại: <?php echo e($summary['remaining_percentage']); ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thêm Đầu điểm</h5>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> 
                            <strong>Tỷ lệ % cho MỖI CỘT.</strong> VD: Quiz 5%/cột × 10 cột = 50% tổng điểm<br>
                            <strong>Số cột > 1:</strong> Tự động tạo nhiều đầu điểm. VD: "quiz" + 10 cột → quiz 1, quiz 2, ..., quiz 10
                        </small>
                    </p>
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

                    <?php if($summary['remaining_percentage'] > 0): ?>
                        <form action="<?php echo e(route('dao-tao.lop-hoc-phan.cau-hinh-diem.store', $lopHocPhan->id)); ?>"
                            method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tên đầu điểm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_dau_diem"
                                        class="form-control <?php $__errorArgs = ['ten_dau_diem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="VD: Chuyên cần, Giữa kỳ..." required>
                                    <?php $__errorArgs = ['ten_dau_diem'];
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
                                <div class="col-md-2">
                                    <label class="form-label">Tỷ lệ %/cột <span class="text-danger">*</span></label>
                                    <input type="number" name="ty_le"
                                        class="form-control <?php $__errorArgs = ['ty_le'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" step="0.01"
                                        min="0.01" max="100" placeholder="5" required>
                                    <?php $__errorArgs = ['ty_le'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">% cho mỗi cột</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Số cột <span class="text-danger">*</span></label>
                                    <input type="number" name="so_cot"
                                        class="form-control <?php $__errorArgs = ['so_cot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="1"
                                        max="20" value="1" required>
                                    <?php $__errorArgs = ['so_cot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Nếu > 1, tạo nhiều đầu điểm</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" name="ghi_chu" class="form-control" placeholder="Ghi chú...">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Thêm đầu điểm
                                </button>
                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Đã cấu hình đầy đủ 100% tỷ lệ điểm.
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-sm btn-secondary">Quay
                                lại</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Danh sách đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Đầu điểm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên đầu điểm</th>
                                    <th>Tỷ lệ %</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhan->cauHinhDauDiem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cau_hinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($cau_hinh->ten_dau_diem); ?></strong></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo e($cau_hinh->ty_le); ?>%</span>
                                        </td>
                                        <td><?php echo e($cau_hinh->ghi_chu ?? '-'); ?></td>
                                        <td>
                                            <form action="<?php echo e(route('dao-tao.cau-hinh-diem.destroy', $cau_hinh->id)); ?>"
                                                method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                        <td colspan="5" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có cấu hình đầu điểm nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if($lopHocPhan->cauHinhDauDiem->count() > 0): ?>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="2" class="text-end">TỔNG:</th>
                                        <th><span
                                                class="badge bg-<?php echo e($summary['total_percentage'] == 100 ? 'success' : 'warning'); ?>">
                                                <?php echo e($summary['total_percentage']); ?>%
                                            </span>

                                        </th>
                                        <th colspan="3">
                                            <?php if($summary['is_complete']): ?>
                                                <span class="text-success"><i class="bi bi-check-circle"></i> Đã đủ
                                                    100%</span>
                                            <?php else: ?>
                                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Còn
                                                    thiếu <?php echo e($tyLeConLai); ?>%</span>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Validation tỷ lệ % realtime
            document.querySelector('input[name="ty_le"]').addEventListener('input', function() {
                const max = parseFloat(this.max);
                const val = parseFloat(this.value);
                if (val > max) {
                    this.value = max;
                    alert('Tỷ lệ % không được vượt quá ' + max + '%');
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/cau-hinh-dau-diem/index.blade.php ENDPATH**/ ?>