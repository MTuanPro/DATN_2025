<?php $__env->startSection('title', 'Chỉnh sửa học kỳ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa học kỳ</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-ky.index')); ?>">Học kỳ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin học kỳ</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.hoc-ky.update', $hocKy->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ten_hoc_ky" class="form-label">Tên học kỳ <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['ten_hoc_ky'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ten_hoc_ky"
                                        name="ten_hoc_ky" required>
                                        <option value="">-- Chọn học kỳ --</option>
                                        <option value="Học kỳ 1"
                                            <?php echo e(old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ 1' ? 'selected' : ''); ?>>Học
                                            kỳ 1</option>
                                        <option value="Học kỳ 2"
                                            <?php echo e(old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ 2' ? 'selected' : ''); ?>>Học
                                            kỳ 2</option>
                                        <option value="Học kỳ hè"
                                            <?php echo e(old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ hè' ? 'selected' : ''); ?>>Học
                                            kỳ hè</option>
                                    </select>
                                    <?php $__errorArgs = ['ten_hoc_ky'];
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nam_hoc" class="form-label">Năm học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['nam_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="nam_hoc" name="nam_hoc" value="<?php echo e(old('nam_hoc', $hocKy->nam_hoc)); ?>"
                                        placeholder="VD: 2024-2025" required>
                                    <?php $__errorArgs = ['nam_hoc'];
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['ngay_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ngay_bat_dau" name="ngay_bat_dau"
                                        value="<?php echo e(old('ngay_bat_dau', $hocKy->ngay_bat_dau->format('Y-m-d'))); ?>" required>
                                    <?php $__errorArgs = ['ngay_bat_dau'];
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['ngay_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ngay_ket_thuc" name="ngay_ket_thuc"
                                        value="<?php echo e(old('ngay_ket_thuc', $hocKy->ngay_ket_thuc->format('Y-m-d'))); ?>"
                                        required>
                                    <?php $__errorArgs = ['ngay_ket_thuc'];
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_bat_dau_dang_ky" class="form-label">Ngày bắt đầu đăng ký</label>
                                    <input type="date"
                                        class="form-control <?php $__errorArgs = ['ngay_bat_dau_dang_ky'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ngay_bat_dau_dang_ky" name="ngay_bat_dau_dang_ky"
                                        value="<?php echo e(old('ngay_bat_dau_dang_ky', $hocKy->ngay_bat_dau_dang_ky?->format('Y-m-d'))); ?>">
                                    <?php $__errorArgs = ['ngay_bat_dau_dang_ky'];
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_ket_thuc_dang_ky" class="form-label">Ngày kết thúc đăng ký</label>
                                    <input type="date"
                                        class="form-control <?php $__errorArgs = ['ngay_ket_thuc_dang_ky'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ngay_ket_thuc_dang_ky" name="ngay_ket_thuc_dang_ky"
                                        value="<?php echo e(old('ngay_ket_thuc_dang_ky', $hocKy->ngay_ket_thuc_dang_ky?->format('Y-m-d'))); ?>">
                                    <?php $__errorArgs = ['ngay_ket_thuc_dang_ky'];
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="la_hoc_ky_hien_tai"
                                        name="la_hoc_ky_hien_tai" value="1"
                                        <?php echo e(old('la_hoc_ky_hien_tai', $hocKy->la_hoc_ky_hien_tai) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="la_hoc_ky_hien_tai">
                                        Đặt làm học kỳ hiện tại
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                            <a href="<?php echo e(route('dao-tao.hoc-ky.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-ky/edit.blade.php ENDPATH**/ ?>