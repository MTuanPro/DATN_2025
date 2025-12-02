<?php $__env->startSection('title', 'Kết quả học tập'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kết quả học tập</h3>
                    <p class="text-subtitle text-muted">Xem kết quả học tập sinh viên các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kết quả học tập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('giangvien.ket-qua-hoc-tap.index')); ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả học kỳ --</option>
                                    <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($hocKy->id); ?>"
                                            <?php echo e(request('hoc_ky_id') == $hocKy->id ? 'selected' : ''); ?>>
                                            <?php echo e($hocKy->ten_hoc_ky); ?> - <?php echo e($hocKy->nam_hoc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="mo_lop" <?php echo e(request('trang_thai') == 'mo_lop' ? 'selected' : ''); ?>>Mở
                                        lớp</option>
                                    <option value="dang_hoc" <?php echo e(request('trang_thai') == 'dang_hoc' ? 'selected' : ''); ?>>
                                        Đang học</option>
                                    <option value="ket_thuc" <?php echo e(request('trang_thai') == 'ket_thuc' ? 'selected' : ''); ?>>
                                        Kết thúc</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Mã lớp, tên lớp, môn học..." value="<?php echo e(request('search')); ?>">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Tìm kiếm
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
                    <h4 class="card-title">Danh sách lớp học phần</h4>
                </div>
                <div class="card-body">
                    <?php if($lopHocPhans->isEmpty()): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Không tìm thấy lớp học phần nào.
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
                                        <th>Sĩ số</th>
                                        <th>Trạng thái</th>
                                        <th>Điểm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($lopHocPhans->firstItem() + $index); ?></td>
                                            <td><strong><?php echo e($lop->ma_lop_hp ?? 'N/A'); ?></strong></td>
                                            <td>
                                                <?php if($lop->monHoc): ?>
                                                    <?php echo e($lop->monHoc->ma_mon); ?> - <?php echo e($lop->monHoc->ten_mon); ?><br>
                                                    <small class="text-muted"><?php echo e($lop->monHoc->so_tin_chi); ?> TC</small>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($lop->hocKy): ?>
                                                    <?php echo e($lop->hocKy->ten_hoc_ky); ?><br>
                                                    <small class="text-muted"><?php echo e($lop->hocKy->nam_hoc); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($lop->so_sinh_vien); ?> SV</span>
                                            </td>
                                            <td>
                                                <?php if($lop->trang_thai_lop == 'mo_lop' || $lop->trang_thai_lop == 'mo_dang_ky'): ?>
                                                    <span class="badge bg-secondary">Mở lớp</span>
                                                <?php elseif($lop->trang_thai_lop == 'dang_hoc'): ?>
                                                    <span class="badge bg-primary">Đang học</span>
                                                <?php elseif($lop->trang_thai_lop == 'ket_thuc'): ?>
                                                    <span class="badge bg-success">Kết thúc</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><?php echo e($lop->trang_thai_lop ?? 'N/A'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="mb-1">
                                                    <?php if($lop->da_nhap_diem): ?>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> <?php echo e($lop->sv_da_nhap); ?>/<?php echo e($lop->so_sinh_vien); ?> SV
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-exclamation-circle"></i> Chưa nhập
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($lop->so_sinh_vien > 0): ?>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar 
                                                        <?php if($lop->ty_le_nhap >= 100): ?> bg-success
                                                        <?php elseif($lop->ty_le_nhap >= 70): ?> bg-info
                                                        <?php elseif($lop->ty_le_nhap >= 30): ?> bg-warning
                                                        <?php else: ?> bg-danger
                                                        <?php endif; ?>" 
                                                        role="progressbar" 
                                                        style="width: <?php echo e($lop->ty_le_nhap); ?>%;" 
                                                        aria-valuenow="<?php echo e($lop->ty_le_nhap); ?>" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted"><?php echo e($lop->ty_le_nhap); ?>% đã nhập</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?php echo e(route('giangvien.nhap-diem.show', $lop->id)); ?>"
                                                        class="btn btn-warning" title="Nhập điểm">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('giangvien.ket-qua-hoc-tap.show', $lop->id)); ?>"
                                                        class="btn btn-primary" title="Xem bảng điểm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('giangvien.ket-qua-hoc-tap.phan-tich', $lop->id)); ?>"
                                                        class="btn btn-info" title="Phân tích điểm">
                                                        <i class="bi bi-graph-up"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            <?php echo e($lopHocPhans->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/ket-qua-hoc-tap/index.blade.php ENDPATH**/ ?>