<?php $__env->startSection('title', 'Quản lý Tài khoản'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Tài khoản</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả tài khoản trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
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
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Danh sách Tài khoản</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tạo tài khoản mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    
                    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo tên hoặc email..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="hoat_dong" <?php echo e(request('status') == 'hoat_dong' ? 'selected' : ''); ?>>Hoạt
                                        động</option>
                                    <option value="khoa" <?php echo e(request('status') == 'khoa' ? 'selected' : ''); ?>>Khóa</option>
                                    <option value="ngung_hoat_dong"
                                        <?php echo e(request('status') == 'ngung_hoat_dong' ? 'selected' : ''); ?>>Ngừng hoạt động
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">-- Tất cả vai trò --</option>
                                    <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaiTro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vaiTro->ma_vai_tro); ?>"
                                            <?php echo e(request('role') == $vaiTro->ma_vai_tro ? 'selected' : ''); ?>>
                                            <?php echo e($vaiTro->ten_vai_tro); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Email verified</th>
                                    <th>Đăng nhập cuối</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($user->id); ?></td>
                                        <td>
                                            <strong><?php echo e($user->name); ?></strong>
                                        </td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td>
                                            <?php $__currentLoopData = $user->vaiTro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaiTro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-info"><?php echo e($vaiTro->ten_vai_tro); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($user->vaiTro->isEmpty()): ?>
                                                <span class="text-muted">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user->trang_thai == 'hoat_dong'): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php elseif($user->trang_thai == 'khoa'): ?>
                                                <span class="badge bg-danger">Khóa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Ngừng hoạt động</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user->email_verified_at): ?>
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <small><?php echo e($user->email_verified_at->format('d/m/Y')); ?></small>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                <small>Chưa xác thực</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user->lan_dang_nhap_cuoi): ?>
                                                <small><?php echo e($user->lan_dang_nhap_cuoi->format('d/m/Y H:i')); ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Chưa đăng nhập</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <?php if($user->id !== Auth::id()): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-<?php echo e($user->trang_thai == 'hoat_dong' ? 'danger' : 'success'); ?> toggle-status"
                                                        data-user-id="<?php echo e($user->id); ?>"
                                                        data-current-status="<?php echo e($user->trang_thai); ?>"
                                                        title="<?php echo e($user->trang_thai == 'hoat_dong' ? 'Khóa' : ($user->trang_thai == 'khoa' ? 'Mở khóa' : 'Kích hoạt lại')); ?>">
                                                        <i
                                                            class="bi bi-<?php echo e($user->trang_thai == 'hoat_dong' ? 'lock' : 'unlock'); ?>"></i>
                                                    </button>

                                                    <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Không có tài khoản nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Hiển thị <?php echo e($users->firstItem() ?? 0); ?> - <?php echo e($users->lastItem() ?? 0); ?>

                            trong tổng số <?php echo e($users->total()); ?> tài khoản
                        </div>
                        <div>
                            <?php echo e($users->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Toggle status
                document.querySelectorAll('.toggle-status').forEach(button => {
                    button.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        const currentStatus = this.dataset.currentStatus;

                        if (confirm(
                                `Bạn có chắc chắn muốn ${currentStatus === 'hoat_dong' ? 'khóa' : 'mở khóa'} tài khoản này?`
                                )) {
                            fetch(`/admin/users/${userId}/toggle-status`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        location.reload();
                                    } else {
                                        alert(data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Có lỗi xảy ra!');
                                });
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/users/index.blade.php ENDPATH**/ ?>