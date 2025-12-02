<?php $__env->startSection('title', 'Thêm đầu điểm'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Thêm đầu điểm</h3>
                <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.cau-hinh-diem.index')); ?>">Cấu hình điểm</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.cau-hinh-diem.show', $lopHocPhan->id)); ?>">Chi tiết</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5>Thêm cấu hình đầu điểm mới</h5>
            </div>
            <div class="card-body">
                <?php if($tyLeConLai <= 0): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Đã đạt 100% tỷ lệ. Không thể thêm đầu điểm mới.
                    </div>
                    <a href="<?php echo e(route('giangvien.cau-hinh-diem.show', $lopHocPhan->id)); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Tỷ lệ còn lại có thể thêm: <strong><?php echo e($tyLeConLai); ?>%</strong>
                    </div>

                    <form action="<?php echo e(route('giangvien.cau-hinh-diem.store', $lopHocPhan->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ten_dau_diem">Tên đầu điểm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['ten_dau_diem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="ten_dau_diem" name="ten_dau_diem" 
                                        value="<?php echo e(old('ten_dau_diem')); ?>"
                                        placeholder="VD: Chuyên cần, Giữa kỳ, Cuối kỳ, Bài tập, Tiểu luận...">
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
                                    <small class="text-muted">Nhập tên mô tả cho đầu điểm</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ty_le">Tỷ lệ (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['ty_le'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="ty_le" name="ty_le" 
                                        value="<?php echo e(old('ty_le')); ?>"
                                        min="1" max="<?php echo e($tyLeConLai); ?>" step="0.1"
                                        placeholder="VD: 10, 20, 30...">
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
                                    <small class="text-muted">Tối đa: <?php echo e($tyLeConLai); ?>%</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="so_cot">Số cột điểm <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['so_cot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="so_cot" name="so_cot" 
                                        value="<?php echo e(old('so_cot', 1)); ?>"
                                        min="1" max="10"
                                        placeholder="VD: 1, 2, 3...">
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
                                    <small class="text-muted">Số lần nhập điểm (VD: Bài tập có 3 cột = 3 lần làm bài)</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <strong>Gợi ý các đầu điểm phổ biến:</strong>
                            <ul class="mb-0">
                                <li><strong>Chuyên cần (10-20%):</strong> Điểm danh, tham gia lớp - 1 cột</li>
                                <li><strong>Bài tập (10-30%):</strong> Bài tập về nhà - 2-5 cột</li>
                                <li><strong>Giữa kỳ (20-30%):</strong> Kiểm tra giữa kỳ - 1 cột</li>
                                <li><strong>Cuối kỳ (40-60%):</strong> Thi cuối kỳ - 1 cột</li>
                                <li><strong>Thực hành (10-30%):</strong> Bài lab, thực hành - 2-4 cột</li>
                                <li><strong>Tiểu luận (20-40%):</strong> Đề tài, báo cáo - 1-2 cột</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu cấu hình
                            </button>
                            <a href="<?php echo e(route('giangvien.cau-hinh-diem.show', $lopHocPhan->id)); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/cau-hinh-diem/create.blade.php ENDPATH**/ ?>