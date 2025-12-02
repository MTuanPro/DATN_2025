<?php $__env->startSection('title', 'Tra cứu giảng viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tra cứu giảng viên</h3>
                    <p class="text-subtitle text-muted">Tìm kiếm và xem thông tin giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tra cứu giảng viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('sinh-vien.tra-cuu.giang-vien')); ?>" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Mã GV, tên, email, SĐT...">
                        </div>
                        <div class="col-md-3">
                            <label for="khoa_id" class="form-label">Khoa</label>
                            <select class="form-select" id="khoa_id" name="khoa_id">
                                <option value="">-- Tất cả --</option>
                                <?php $__currentLoopData = $khoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $khoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($khoa->id); ?>" <?php echo e(request('khoa_id') == $khoa->id ? 'selected' : ''); ?>>
                                        <?php echo e($khoa->ten_khoa); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="trinh_do_id" class="form-label">Trình độ</label>
                            <select class="form-select" id="trinh_do_id" name="trinh_do_id">
                                <option value="">-- Tất cả --</option>
                                <?php $__currentLoopData = $trinhDos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($td->id); ?>" <?php echo e(request('trinh_do_id') == $td->id ? 'selected' : ''); ?>>
                                        <?php echo e($td->ten_trinh_do); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="gioi_tinh" class="form-label">Giới tính</label>
                            <select class="form-select" id="gioi_tinh" name="gioi_tinh">
                                <option value="">-- Tất cả --</option>
                                <option value="nam" <?php echo e(request('gioi_tinh') == 'nam' ? 'selected' : ''); ?>>Nam</option>
                                <option value="nu" <?php echo e(request('gioi_tinh') == 'nu' ? 'selected' : ''); ?>>Nữ</option>
                                <option value="khac" <?php echo e(request('gioi_tinh') == 'khac' ? 'selected' : ''); ?>>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách giảng viên</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Khoa</th>
                                    <th>Trình độ</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Chuyên môn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($giangViens->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($gv->ma_giang_vien); ?></strong></td>
                                        <td>
                                            <strong><?php echo e($gv->ho_ten); ?></strong>
                                            <?php if($gv->gioi_tinh): ?>
                                                <br><small class="text-muted">
                                                    <?php echo e($gv->gioi_tinh == 'nam' ? 'Nam' : ($gv->gioi_tinh == 'nu' ? 'Nữ' : 'Khác')); ?>

                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($gv->khoa->ten_khoa ?? 'N/A'); ?></td>
                                        <td><?php echo e($gv->trinhDo->ten_trinh_do ?? 'N/A'); ?></td>
                                        <td><?php echo e($gv->email ?? 'N/A'); ?></td>
                                        <td><?php echo e($gv->so_dien_thoai ?? 'N/A'); ?></td>
                                        <td><?php echo e($gv->chuyen_mon ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không tìm thấy giảng viên nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị <?php echo e($giangViens->firstItem() ?? 0); ?> - <?php echo e($giangViens->lastItem() ?? 0); ?>

                                trong tổng số <?php echo e($giangViens->total()); ?> giảng viên
                            </small>
                        </div>
                        <div>
                            <?php echo e($giangViens->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/tra-cuu/giang-vien.blade.php ENDPATH**/ ?>