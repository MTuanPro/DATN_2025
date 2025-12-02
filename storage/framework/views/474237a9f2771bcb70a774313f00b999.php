<?php $__env->startSection('title', 'Thêm Học kỳ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Học kỳ Mới</h3>
                    <p class="text-subtitle text-muted">Nhập thông tin học kỳ vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-ky.index')); ?>">Học kỳ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Học kỳ</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('dao-tao.hoc-ky.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <div class="row">
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ten_hoc_ky" class="form-label">
                                            Tên học kỳ <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?php $__errorArgs = ['ten_hoc_ky'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="ten_hoc_ky" name="ten_hoc_ky" value="<?php echo e(old('ten_hoc_ky')); ?>"
                                            placeholder="VD: HK1, HK2, HK Hè" required>
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

                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="nam_hoc" class="form-label">
                                            Năm học <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?php $__errorArgs = ['nam_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="nam_hoc" name="nam_hoc" value="<?php echo e(old('nam_hoc')); ?>"
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

                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_bat_dau" class="form-label">
                                            Ngày bắt đầu <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control <?php $__errorArgs = ['ngay_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="ngay_bat_dau" name="ngay_bat_dau" value="<?php echo e(old('ngay_bat_dau')); ?>"
                                            required>
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

                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_ket_thuc" class="form-label">
                                            Ngày kết thúc <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control <?php $__errorArgs = ['ngay_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="ngay_ket_thuc" name="ngay_ket_thuc" value="<?php echo e(old('ngay_ket_thuc')); ?>"
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

                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_bat_dau_dang_ky" class="form-label">
                                            Ngày bắt đầu đăng ký môn
                                        </label>
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
                                            value="<?php echo e(old('ngay_bat_dau_dang_ky')); ?>">
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

                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_ket_thuc_dang_ky" class="form-label">
                                            Ngày kết thúc đăng ký môn
                                        </label>
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
                                            value="<?php echo e(old('ngay_ket_thuc_dang_ky')); ?>">
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

                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="mo_ta" class="form-label">Mô tả</label>
                                        <textarea class="form-control <?php $__errorArgs = ['mo_ta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="mo_ta" name="mo_ta" rows="3"
                                            placeholder="Nhập mô tả về học kỳ"><?php echo e(old('mo_ta')); ?></textarea>
                                        <?php $__errorArgs = ['mo_ta'];
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

                                    
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="la_hoc_ky_hien_tai"
                                                id="la_hoc_ky_hien_tai" value="1"
                                                <?php echo e(old('la_hoc_ky_hien_tai') ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="la_hoc_ky_hien_tai">
                                                Đặt làm học kỳ hiện tại
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> Chỉ có 1 học kỳ được đánh dấu là hiện tại
                                        </small>
                                    </div>
                                </div>

                                
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Lưu
                                    </button>
                                    <a href="<?php echo e(route('dao-tao.hoc-ky.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
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

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-ky/create.blade.php ENDPATH**/ ?>