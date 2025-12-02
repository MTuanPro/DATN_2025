<?php $__env->startSection('title', 'Tạo Tài khoản Mới'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tạo Tài khoản Mới</h3>
                    <p class="text-subtitle text-muted">Thêm tài khoản người dùng vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Tài khoản</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
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
                            <h4 class="card-title">Thông tin Tài khoản</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.users.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="name" name="name" value="<?php echo e(old('name')); ?>"
                                        placeholder="Nhập họ và tên" required>
                                    <?php $__errorArgs = ['name'];
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

                                
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="email" name="email" value="<?php echo e(old('email')); ?>"
                                        placeholder="Nhập địa chỉ email" required>
                                    <?php $__errorArgs = ['email'];
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

                                
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">
                                        Mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="password" name="password" placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                                        required>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                                </div>

                                
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                                </div>

                                
                                <div class="form-group mb-3">
                                    <label for="trang_thai" class="form-label">
                                        Trạng thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?php $__errorArgs = ['trang_thai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="trang_thai"
                                        name="trang_thai" required>
                                        <option value="">-- Chọn trạng thái --</option>
                                        <option value="hoat_dong" <?php echo e(old('trang_thai') == 'hoat_dong' ? 'selected' : ''); ?>>
                                            Hoạt động
                                        </option>
                                        <option value="khoa" <?php echo e(old('trang_thai') == 'khoa' ? 'selected' : ''); ?>>
                                            Khóa
                                        </option>
                                        <option value="ngung_hoat_dong"
                                            <?php echo e(old('trang_thai') == 'ngung_hoat_dong' ? 'selected' : ''); ?>>
                                            Ngừng hoạt động
                                        </option>
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

                                
                                <div class="form-group mb-4">
                                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    <p class="text-muted small">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>Lưu ý:</strong> Sinh viên và Giảng viên được tạo từ "Quản lý Sinh viên" và
                                        "Quản lý Giảng viên"
                                    </p>
                                    <div class="card">
                                        <div class="card-body">
                                            <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaiTro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(!in_array($vaiTro->ma_vai_tro, ['sinh_vien', 'giang_vien'])): ?>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio" name="vai_tro"
                                                            value="<?php echo e($vaiTro->id); ?>" id="role_<?php echo e($vaiTro->id); ?>"
                                                            <?php echo e(old('vai_tro') == $vaiTro->id ? 'checked' : ''); ?> required>
                                                        <label class="form-check-label" for="role_<?php echo e($vaiTro->id); ?>">
                                                            <strong><?php echo e($vaiTro->ten_vai_tro); ?></strong>
                                                            <?php if($vaiTro->mo_ta): ?>
                                                                <br><small class="text-muted"><?php echo e($vaiTro->mo_ta); ?></small>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['vai_tro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div id="additional-fields" style="display: none;">
                                    <hr class="my-4">
                                    <h5 class="mb-3">Thông tin bổ sung</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                                <input type="text" class="form-control" id="so_dien_thoai"
                                                    name="so_dien_thoai" value="<?php echo e(old('so_dien_thoai')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                                <input type="date" class="form-control" id="ngay_sinh"
                                                    name="ngay_sinh" value="<?php echo e(old('ngay_sinh')); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                                <select class="form-select" id="gioi_tinh" name="gioi_tinh">
                                                    <option value="">-- Chọn giới tính --</option>
                                                    <option value="Nam"
                                                        <?php echo e(old('gioi_tinh') == 'Nam' ? 'selected' : ''); ?>>Nam</option>
                                                    <option value="Nữ"
                                                        <?php echo e(old('gioi_tinh') == 'Nữ' ? 'selected' : ''); ?>>Nữ</option>
                                                    <option value="Khác"
                                                        <?php echo e(old('gioi_tinh') == 'Khác' ? 'selected' : ''); ?>>Khác</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>
                                                <input type="file" class="form-control" id="anh_dai_dien"
                                                    name="anh_dai_dien" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="dia_chi" class="form-label">Địa chỉ</label>
                                        <textarea class="form-control" id="dia_chi" name="dia_chi" rows="2"><?php echo e(old('dia_chi')); ?></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="ghi_chu" class="form-label">Ghi chú</label>
                                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="2"><?php echo e(old('ghi_chu')); ?></textarea>
                                    </div>
                                </div>

                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Tạo tài khoản
                                    </button>
                                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
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

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/users/create.blade.php ENDPATH**/ ?>