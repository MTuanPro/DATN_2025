<?php $__env->startSection('title', 'Thêm Phòng học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Phòng học mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.phong-hoc.index')); ?>">Phòng học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Phòng học</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.phong-hoc.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row mb-3">
                            <label for="ma_phong" class="col-md-4 col-form-label">Mã Phòng <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ma_phong" name="ma_phong"
                                    class="form-control <?php $__errorArgs = ['ma_phong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('ma_phong')); ?>" placeholder="Ví dụ: A101, B205, LAB01..." required>
                                <?php $__errorArgs = ['ma_phong'];
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
                            <label for="ten_phong" class="col-md-4 col-form-label">Tên Phòng <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_phong" name="ten_phong"
                                    class="form-control <?php $__errorArgs = ['ten_phong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('ten_phong')); ?>" placeholder="Ví dụ: Phòng học 101, Phòng thực hành..."
                                    required>
                                <?php $__errorArgs = ['ten_phong'];
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
                            <label for="suc_chua" class="col-md-4 col-form-label">Sức chứa</label>
                            <div class="col-md-8">
                                <input type="number" id="suc_chua" name="suc_chua"
                                    class="form-control <?php $__errorArgs = ['suc_chua'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('suc_chua')); ?>" min="1" max="500"
                                    placeholder="Số sinh viên tối đa">
                                <?php $__errorArgs = ['suc_chua'];
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
                            <label for="vi_tri" class="col-md-4 col-form-label">Vị trí</label>
                            <div class="col-md-8">
                                <input type="text" id="vi_tri" name="vi_tri"
                                    class="form-control <?php $__errorArgs = ['vi_tri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('vi_tri')); ?>"
                                    placeholder="Ví dụ: Tầng 1 nhà A, Tầng 2 nhà B...">
                                <?php $__errorArgs = ['vi_tri'];
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
                            <label for="loai_phong" class="col-md-4 col-form-label">Loại phòng</label>
                            <div class="col-md-8">
                                <select name="loai_phong" id="loai_phong"
                                    class="form-select <?php $__errorArgs = ['loai_phong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Chọn loại phòng --</option>
                                    <option value="Lý thuyết" <?php echo e(old('loai_phong') == 'Lý thuyết' ? 'selected' : ''); ?>>Lý
                                        thuyết</option>
                                    <option value="Thực hành" <?php echo e(old('loai_phong') == 'Thực hành' ? 'selected' : ''); ?>>Thực
                                        hành</option>
                                    <option value="Phòng máy" <?php echo e(old('loai_phong') == 'Phòng máy' ? 'selected' : ''); ?>>
                                        Phòng máy</option>
                                    <option value="Hội trường" <?php echo e(old('loai_phong') == 'Hội trường' ? 'selected' : ''); ?>>
                                        Hội trường</option>
                                </select>
                                <?php $__errorArgs = ['loai_phong'];
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
                            <label for="trang_thai" class="col-md-4 col-form-label">Trạng thái</label>
                            <div class="col-md-8">
                                <select name="trang_thai" id="trang_thai"
                                    class="form-select <?php $__errorArgs = ['trang_thai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="Hoạt động"
                                        <?php echo e(old('trang_thai', 'Hoạt động') == 'Hoạt động' ? 'selected' : ''); ?>>Hoạt động
                                    </option>
                                    <option value="Bảo trì" <?php echo e(old('trang_thai') == 'Bảo trì' ? 'selected' : ''); ?>>Bảo trì
                                    </option>
                                    <option value="Không sử dụng"
                                        <?php echo e(old('trang_thai') == 'Không sử dụng' ? 'selected' : ''); ?>>Không sử dụng</option>
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
                        </div>

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="3" class="form-control <?php $__errorArgs = ['mo_ta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Mô tả chi tiết về phòng học, trang thiết bị..."><?php echo e(old('mo_ta')); ?></textarea>
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
                                    <i class="bi bi-save"></i> Lưu
                                </button>
                                <a href="<?php echo e(route('dao-tao.phong-hoc.index')); ?>" class="btn btn-secondary">
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

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/phong-hoc/create.blade.php ENDPATH**/ ?>