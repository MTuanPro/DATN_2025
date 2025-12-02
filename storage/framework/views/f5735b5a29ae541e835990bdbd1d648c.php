<?php $__env->startSection('title', 'Chỉnh sửa Chuyên ngành'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa Chuyên ngành</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.chuyen-nganh.index')); ?>">Chuyên ngành</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Chuyên ngành</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.chuyen-nganh.update', $chuyenNganh->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="form-group row mb-3">
                            <label for="ma_chuyen_nganh" class="col-md-4 col-form-label">Mã Chuyên ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ma_chuyen_nganh" name="ma_chuyen_nganh"
                                    class="form-control <?php $__errorArgs = ['ma_chuyen_nganh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('ma_chuyen_nganh', $chuyenNganh->ma_chuyen_nganh)); ?>" required>
                                <?php $__errorArgs = ['ma_chuyen_nganh'];
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

                        <div class="form-group row mb-3">
                            <label for="ten_chuyen_nganh" class="col-md-4 col-form-label">Tên Chuyên ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_chuyen_nganh" name="ten_chuyen_nganh"
                                    class="form-control <?php $__errorArgs = ['ten_chuyen_nganh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('ten_chuyen_nganh', $chuyenNganh->ten_chuyen_nganh)); ?>" required>
                                <?php $__errorArgs = ['ten_chuyen_nganh'];
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

                        <div class="form-group row mb-3">
                            <label for="nganh_id" class="col-md-4 col-form-label">Ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="nganh_id" id="nganh_id"
                                    class="form-select <?php $__errorArgs = ['nganh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Chọn ngành --</option>
                                    <?php $__currentLoopData = $nganhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nganh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($nganh->id); ?>"
                                            <?php echo e(old('nganh_id', $chuyenNganh->nganh_id) == $nganh->id ? 'selected' : ''); ?>>
                                            <?php echo e($nganh->ten_nganh); ?> (<?php echo e($nganh->khoa->ten_khoa ?? 'N/A'); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['nganh_id'];
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

                        <div class="form-group row mb-3">
                            <label for="tong_tin_chi_toi_thieu" class="col-md-4 col-form-label">Tổng tín chỉ tối
                                thiểu</label>
                            <div class="col-md-8">
                                <input type="number" id="tong_tin_chi_toi_thieu" name="tong_tin_chi_toi_thieu"
                                    class="form-control <?php $__errorArgs = ['tong_tin_chi_toi_thieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('tong_tin_chi_toi_thieu', $chuyenNganh->tong_tin_chi_toi_thieu)); ?>"
                                    min="0" max="200">
                                <?php $__errorArgs = ['tong_tin_chi_toi_thieu'];
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

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="4" class="form-control <?php $__errorArgs = ['mo_ta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('mo_ta', $chuyenNganh->mo_ta)); ?></textarea>
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
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Cập nhật
                                </button>
                                <a href="<?php echo e(route('dao-tao.chuyen-nganh.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/chuyen-nganh/edit.blade.php ENDPATH**/ ?>