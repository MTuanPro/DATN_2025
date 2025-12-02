<?php $__env->startSection('title', 'Sửa Vai trò'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Vai trò</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin vai trò</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.vai-tro.index')); ?>">Vai trò</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Vai trò</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.vai-tro.update', $vaiTro)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ma_vai_tro" class="form-label">
                                                Mã vai trò <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control <?php $__errorArgs = ['ma_vai_tro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="ma_vai_tro" name="ma_vai_tro"
                                                value="<?php echo e(old('ma_vai_tro', $vaiTro->ma_vai_tro)); ?>"
                                                placeholder="VD: giang_vien">
                                            <?php $__errorArgs = ['ma_vai_tro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="text-muted">Chỉ sử dụng chữ cái, số, dấu gạch ngang và gạch
                                                dưới</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ten_vai_tro" class="form-label">
                                                Tên vai trò <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control <?php $__errorArgs = ['ten_vai_tro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="ten_vai_tro" name="ten_vai_tro"
                                                value="<?php echo e(old('ten_vai_tro', $vaiTro->ten_vai_tro)); ?>"
                                                placeholder="VD: Giảng viên">
                                            <?php $__errorArgs = ['ten_vai_tro'];
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
                                        <div class="form-group mb-3">
                                            <label for="mo_ta" class="form-label">Mô tả</label>
                                            <textarea class="form-control <?php $__errorArgs = ['mo_ta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="mo_ta" name="mo_ta" rows="3"
                                                placeholder="Mô tả về vai trò này..."><?php echo e(old('mo_ta', $vaiTro->mo_ta)); ?></textarea>
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
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="muc_do_uu_tien" class="form-label">
                                                Mức độ ưu tiên <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                class="form-control <?php $__errorArgs = ['muc_do_uu_tien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="muc_do_uu_tien" name="muc_do_uu_tien"
                                                value="<?php echo e(old('muc_do_uu_tien', $vaiTro->muc_do_uu_tien)); ?>" min="1"
                                                max="100" placeholder="1-100">
                                            <?php $__errorArgs = ['muc_do_uu_tien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="text-muted">Từ 1 (thấp nhất) đến 100 (cao nhất)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                    <a href="<?php echo e(route('admin.vai-tro.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin bổ sung</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Số lượng người dùng:</strong>
                                <span class="badge bg-info"><?php echo e($vaiTro->users_count); ?> người</span>
                            </div>

                            <div class="mb-3">
                                <strong>Ngày tạo:</strong><br>
                                <small class="text-muted"><?php echo e($vaiTro->created_at->format('d/m/Y H:i')); ?></small>
                            </div>

                            <div class="mb-3">
                                <strong>Cập nhật lần cuối:</strong><br>
                                <small class="text-muted"><?php echo e($vaiTro->updated_at->format('d/m/Y H:i')); ?></small>
                            </div>

                            <?php if($vaiTro->users_count > 0): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Vai trò này đang có <?php echo e($vaiTro->users_count); ?> người dùng.
                                    Hãy cẩn thận khi thay đổi!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/vai-tro/edit.blade.php ENDPATH**/ ?>