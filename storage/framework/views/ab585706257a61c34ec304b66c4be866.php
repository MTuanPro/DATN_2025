<?php $__env->startSection('title', 'Quản lý Người nhận'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Người nhận Thông báo</h3>
                    <p class="text-subtitle text-muted">Xem danh sách người nhận và trạng thái đọc</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Người nhận</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light-primary">
                        <div class="card-body">
                            <h6 class="text-muted">Tổng người nhận</h6>
                            <h4><?php echo e(\App\Models\NguoiNhanThongBao::count()); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-success">
                        <div class="card-body">
                            <h6 class="text-muted">Đã đọc</h6>
                            <h4><?php echo e(\App\Models\NguoiNhanThongBao::where('da_doc', true)->count()); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-warning">
                        <div class="card-body">
                            <h6 class="text-muted">Chưa đọc</h6>
                            <h4><?php echo e(\App\Models\NguoiNhanThongBao::where('da_doc', false)->count()); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-info">
                        <div class="card-body">
                            <h6 class="text-muted">Đã gửi email</h6>
                            <h4><?php echo e(\App\Models\NguoiNhanThongBao::where('da_gui_email', true)->count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách người nhận</h4>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Form -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <select name="thong_bao_id" class="form-select">
                                <option value="">-- Tất cả thông báo --</option>
                                <?php $__currentLoopData = $thongBaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tb->id); ?>" <?php echo e(request('thong_bao_id') == $tb->id ? 'selected' : ''); ?>>
                                        <?php echo e($tb->tieu_de); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="da_doc" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="1" <?php echo e(request('da_doc') == '1' ? 'selected' : ''); ?>>Đã đọc</option>
                                <option value="0" <?php echo e(request('da_doc') == '0' ? 'selected' : ''); ?>>Chưa đọc</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm người nhận..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Lọc
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?php echo e(route('admin.nguoi-nhan-thong-bao.index')); ?>" class="btn btn-secondary w-100">
                                <i class="bi bi-x"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Thông báo</th>
                                    <th>Người nhận</th>
                                    <th>Email</th>
                                    <th>Đã đọc</th>
                                    <th>Email gửi</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $nguoiNhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('admin.thong-bao.show', $nn->thongBao->id)); ?>">
                                                <?php echo e(Str::limit($nn->thongBao->tieu_de, 50)); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($nn->nguoiNhan->name); ?></td>
                                        <td><?php echo e($nn->nguoiNhan->email); ?></td>
                                        <td>
                                            <?php if($nn->da_doc): ?>
                                                <span class="badge bg-success">Đã đọc</span>
                                                <br><small><?php echo e($nn->ngay_doc?->format('d/m/Y H:i')); ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Chưa đọc</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($nn->da_gui_email): ?>
                                                <i class="bi bi-check-circle text-success"></i> Đã gửi
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-muted"></i> Chưa gửi
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?php echo e($nn->created_at->format('d/m/Y H:i')); ?></small></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($nguoiNhans->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/nguoi-nhan-thong-bao/index.blade.php ENDPATH**/ ?>