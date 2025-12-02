<?php $__env->startSection('title', 'Sửa Thông tin Tài khoản'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Thông tin Tài khoản</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin tài khoản: <?php echo e($user->name); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Tài khoản</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">
                    
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Cơ bản</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                
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
                                        id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
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
                                        id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
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
                                        <option value="hoat_dong"
                                            <?php echo e(old('trang_thai', $user->trang_thai) == 'hoat_dong' ? 'selected' : ''); ?>>
                                            Hoạt động
                                        </option>
                                        <option value="khoa"
                                            <?php echo e(old('trang_thai', $user->trang_thai) == 'khoa' ? 'selected' : ''); ?>>
                                            Khóa
                                        </option>
                                        <option value="ngung_hoat_dong"
                                            <?php echo e(old('trang_thai', $user->trang_thai) == 'ngung_hoat_dong' ? 'selected' : ''); ?>>
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
                                    <label class="form-label">Vai trò</label>
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
                                                        <input class="form-check-input" type="checkbox" name="vai_tro[]"
                                                            value="<?php echo e($vaiTro->id); ?>" id="role_<?php echo e($vaiTro->id); ?>"
                                                            <?php echo e(in_array($vaiTro->id, old('vai_tro', $userVaiTroIds)) ? 'checked' : ''); ?>>
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

                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Cập nhật
                                    </button>
                                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Bổ sung</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?php echo e($user->id); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td><?php echo e($user->created_at->format('d/m/Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật:</strong></td>
                                    <td><?php echo e($user->updated_at->format('d/m/Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email verified:</strong></td>
                                    <td>
                                        <?php if($user->email_verified_at): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i>
                                                <?php echo e($user->email_verified_at->format('d/m/Y')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa xác thực</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Đăng nhập cuối:</strong></td>
                                    <td>
                                        <?php if($user->lan_dang_nhap_cuoi): ?>
                                            <?php echo e($user->lan_dang_nhap_cuoi->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Chưa đăng nhập</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-lock text-warning"></i>
                                Reset Mật khẩu
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle-fill"></i>
                                <strong>Lưu ý:</strong>
                                <ul class="mb-0 mt-2 small">
                                    <li>Hệ thống sẽ gửi <strong>link reset mật khẩu</strong> qua email</li>
                                    <li>Người dùng tự tạo mật khẩu mới theo yêu cầu bảo mật</li>
                                    <li>Link có hiệu lực trong <strong>60 phút</strong></li>
                                </ul>
                            </div>

                            <form action="<?php echo e(route('admin.users.reset-password', $user->id)); ?>" method="POST"
                                onsubmit="return confirm('⚠️ Xác nhận gửi email reset mật khẩu đến:\n\n📧 <?php echo e($user->email); ?>\n\nNgười dùng sẽ nhận link để tự đặt lại mật khẩu mới.')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-envelope-fill"></i>
                                    Gửi Email Reset Mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>

                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Hành động Khác</h5>
                        </div>
                        <div class="card-body">
                            <?php if($user->id !== Auth::id()): ?>
                                <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm w-100"
                                        onclick="return confirm('Xác nhận xóa tài khoản này?')">
                                        <i class="bi bi-trash"></i> Xóa tài khoản
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>