<?php $__env->startSection('title', 'Cấu hình Đầu điểm'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Cấu hình Đầu điểm</h3>
                <p class="text-subtitle text-muted">Quản lý cấu hình đầu điểm cho lớp học phần</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cấu hình điểm</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('dao-tao.cau-hinh-diem.list')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">Tất cả</option>
                                    <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($hk->id); ?>" <?php echo e(request('hoc_ky_id') == $hk->id ? 'selected' : ''); ?>>
                                            <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Mã lớp, tên lớp..." value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Danh sách lớp học phần -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Danh sách lớp học phần</h5>
            </div>
            <div class="card-body">
                <?php if($lopHocPhans->isEmpty()): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Không có lớp học phần nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Số đầu điểm</th>
                                    <th>Tổng tỷ lệ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($lopHocPhans->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($lop->ma_lop_hp); ?></strong></td>
                                        <td>
                                            <?php echo e($lop->monHoc->ma_mon); ?> - <?php echo e($lop->monHoc->ten_mon); ?><br>
                                            <small class="text-muted"><?php echo e($lop->monHoc->so_tin_chi); ?> TC</small>
                                        </td>
                                        <td>
                                            <?php echo e($lop->hocKy->ten_hoc_ky); ?><br>
                                            <small class="text-muted"><?php echo e($lop->hocKy->nam_hoc); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($lop->so_dau_diem); ?> đầu điểm</span>
                                        </td>
                                        <td>
                                            <?php if($lop->tong_ty_le == 100): ?>
                                                <span class="badge bg-success"><?php echo e($lop->tong_ty_le); ?>%</span>
                                            <?php elseif($lop->tong_ty_le > 0): ?>
                                                <span class="badge bg-warning"><?php echo e($lop->tong_ty_le); ?>%</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">0%</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($lop->da_cau_hinh && $lop->tong_ty_le == 100): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Hoàn thiện
                                                </span>
                                            <?php elseif($lop->da_cau_hinh): ?>
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> Chưa đủ 100%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-dash-circle"></i> Chưa cấu hình
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.cau-hinh-diem', $lop->id)); ?>" 
                                                class="btn btn-sm btn-primary" title="Xem & Cấu hình">
                                                <i class="bi bi-gear"></i> Cấu hình
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($lopHocPhans->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/cau-hinh-dau-diem/list-lop-hoc-phan.blade.php ENDPATH**/ ?>