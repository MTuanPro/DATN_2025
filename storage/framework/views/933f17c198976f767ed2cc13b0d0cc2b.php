<?php $__env->startSection('title', 'Duyệt điểm'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Duyệt điểm</h3>
                    <p class="text-subtitle text-muted">Quản lý và duyệt điểm các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Duyệt điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('dao-tao.duyet-diem.index')); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($hk->id); ?>" <?php echo e($hocKyId == $hk->id ? 'selected' : ''); ?>>
                                                <?php echo e($hk->ten_hoc_ky); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="da_khoa_diem" <?php echo e($trangThai == 'da_khoa_diem' ? 'selected' : ''); ?>>Đã
                                            khóa điểm</option>
                                        <option value="da_duyet_diem" <?php echo e($trangThai == 'da_duyet_diem' ? 'selected' : ''); ?>>
                                            Đã duyệt</option>
                                        <option value="dang_hoc" <?php echo e($trangThai == 'dang_hoc' ? 'selected' : ''); ?>>Đang học
                                        </option>
</select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách lớp học phần</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>SV có điểm</th>
                                    <th>Tiến độ</th>
                                    <th>Điểm TB</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($lop['ma_lop_hp']); ?></strong></td>
                                        <td><?php echo e($lop['ten_lop_hp']); ?></td>
                                        <td><?php echo e($lop['mon_hoc']); ?></td>
                                        <td><?php echo e($lop['hoc_ky']); ?></td>
                                        <td class="text-center">
                                            <?php echo e($lop['sv_co_diem']); ?>/<?php echo e($lop['tong_sv']); ?>

</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar 
                                                <?php if($lop['ty_le'] >= 100): ?> bg-success
                                                <?php elseif($lop['ty_le'] >= 50): ?> bg-info
                                                <?php else: ?> bg-warning <?php endif; ?>"
                                                    role="progressbar" style="width: <?php echo e($lop['ty_le']); ?>%;">
                                                    <?php echo e($lop['ty_le']); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($lop['diem_tb']): ?>
                                                <strong class="text-primary"><?php echo e($lop['diem_tb']); ?></strong>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($lop['trang_thai'] === 'da_khoa_diem'): ?>
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            <?php elseif($lop['trang_thai'] === 'da_duyet_diem'): ?>
                                                <span class="badge bg-success">Đã duyệt</span>
                                            <?php elseif($lop['trang_thai'] === 'dang_hoc'): ?>
                                                <span class="badge bg-info">Đang học</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e($lop['trang_thai']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('dao-tao.duyet-diem.show', $lop['id'])); ?>"
                                                class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Chưa có lớp học phần nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
</table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/extensions/simple-datatables/umd/simple-datatables.js')); ?>"></script>
    <script>
        let table1 = document.querySelector('#table1');
        if (table1) {
            let dataTable = new simpleDatatables.DataTable(table1);
        }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/duyet-diem/index.blade.php ENDPATH**/ ?>