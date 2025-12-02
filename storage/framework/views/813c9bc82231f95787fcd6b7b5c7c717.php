<?php $__env->startSection('content'); ?>
    <div class="container">
        <h2>Thêm Khóa học mới</h2>

        <form action="<?php echo e(route('dao-tao.khoa-hoc.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label for="ten_khoa_hoc" class="form-label">Tên Khóa học <span class="text-danger">*</span></label>
                <input type="text" name="ten_khoa_hoc" id="ten_khoa_hoc"
                    class="form-control <?php $__errorArgs = ['ten_khoa_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('ten_khoa_hoc')); ?>"
                    placeholder="VD: K17, K2021-2025..." required>
                <?php $__errorArgs = ['ten_khoa_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="text-muted">Tên định danh khóa học (VD: K17, K18, K2021-2025)</small>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nam_bat_dau" class="form-label">Năm bắt đầu <span class="text-danger">*</span></label>
                    <input type="number" name="nam_bat_dau" id="nam_bat_dau"
                        class="form-control <?php $__errorArgs = ['nam_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('nam_bat_dau', date('Y'))); ?>" min="2000" max="2100" required>
                    <?php $__errorArgs = ['nam_bat_dau'];
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

                <div class="col-md-4 mb-3">
                    <label for="nam_ket_thuc" class="form-label">Năm kết thúc <span class="text-danger">*</span></label>
                    <input type="number" name="nam_ket_thuc" id="nam_ket_thuc"
                        class="form-control <?php $__errorArgs = ['nam_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('nam_ket_thuc', date('Y') + 4)); ?>" min="2000" max="2100" required>
                    <?php $__errorArgs = ['nam_ket_thuc'];
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

                <div class="col-md-4 mb-3">
                    <label for="so_nam_dao_tao" class="form-label">Số năm đào tạo <span class="text-danger">*</span></label>
                    <input type="number" name="so_nam_dao_tao" id="so_nam_dao_tao"
                        class="form-control <?php $__errorArgs = ['so_nam_dao_tao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('so_nam_dao_tao', 4)); ?>" min="1" max="10" required>
                    <?php $__errorArgs = ['so_nam_dao_tao'];
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

            <div class="mb-3">
                <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                <select name="trang_thai" id="trang_thai" class="form-select <?php $__errorArgs = ['trang_thai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    required>
                    <option value="dang_hoc" <?php echo e(old('trang_thai') == 'dang_hoc' ? 'selected' : ''); ?>>Đang học</option>
                    <option value="da_tot_nghiep" <?php echo e(old('trang_thai') == 'da_tot_nghiep' ? 'selected' : ''); ?>>Đã tốt
                        nghiệp</option>
                </select>
                <?php $__errorArgs = ['trang_thai'];
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

            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả</label>
                <textarea name="mo_ta" id="mo_ta" class="form-control" rows="4"><?php echo e(old('mo_ta')); ?></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-success">Lưu</button>
                <a href="<?php echo e(route('dao-tao.khoa-hoc.index')); ?>" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/khoa-hoc/create.blade.php ENDPATH**/ ?>