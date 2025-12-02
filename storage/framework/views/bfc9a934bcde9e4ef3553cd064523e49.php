<?php $__env->startSection('title', 'Điểm danh'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Quản lý điểm danh</h3>
                <p class="text-subtitle text-muted">Danh sách buổi học cần điểm danh</p>
            </div>
            <a href="<?php echo e(route('giangvien.diem-danh.report')); ?>" class="btn btn-primary">
                <i class="bi bi-graph-up"></i> Báo cáo điểm danh
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('giangvien.diem-danh.index')); ?>" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <?php $__currentLoopData = $danhSachLopHocPhan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lop->id); ?>" <?php echo e(request('lop_hoc_phan_id') == $lop->id ? 'selected' : ''); ?>>
                                            <?php echo e($lop->ma_lop_hp); ?> - <?php echo e($lop->monHoc->ten_mon); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <option value="chua_day" <?php echo e(request('trang_thai') == 'chua_day' ? 'selected' : ''); ?>>Chưa dạy</option>
                                    <option value="dang_day" <?php echo e(request('trang_thai') == 'dang_day' ? 'selected' : ''); ?>>Đang dạy</option>
                                    <option value="da_day" <?php echo e(request('trang_thai') == 'da_day' ? 'selected' : ''); ?>>Đã dạy</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" name="tu_ngay" class="form-control" value="<?php echo e(request('tu_ngay')); ?>"
                                       onchange="document.getElementById('filterForm').submit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" name="den_ngay" class="form-control" value="<?php echo e(request('den_ngay')); ?>"
                                       onchange="document.getElementById('filterForm').submit()">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="<?php echo e(route('giangvien.diem-danh.index')); ?>" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách buổi học -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Danh sách buổi học
                        <span class="badge bg-primary"><?php echo e($buoiHocList->total()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($buoiHocList->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày học</th>
                                        <th>Tiết</th>
                                        <th>Lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Phòng</th>
                                        <th>Trạng thái</th>
                                        <th>Thống kê điểm danh</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $buoiHocList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $buoiHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($buoiHocList->firstItem() + $index); ?></td>
                                            <td>
                                                <strong><?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?></strong><br>
                                                <small class="text-muted"><?php echo e($buoiHoc->ngay_hoc->dayName); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo e($buoiHoc->tiet_bat_dau); ?>-<?php echo e($buoiHoc->tiet_ket_thuc); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?></td>
                                            <td>
                                                <strong><?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></strong>
                                            </td>
                                            <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if($buoiHoc->trang_thai == 'chua_day'): ?>
                                                    <span class="badge bg-secondary">Chưa dạy</span>
                                                <?php elseif($buoiHoc->trang_thai == 'dang_day'): ?>
                                                    <span class="badge bg-warning">Đang dạy</span>
                                                <?php elseif($buoiHoc->trang_thai == 'da_day'): ?>
                                                    <span class="badge bg-success">Đã dạy</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Hủy</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($buoiHoc->diem_danh_stats && $buoiHoc->diem_danh_stats->tong > 0): ?>
                                                    <div class="small">
                                                        <span class="text-success">✓ <?php echo e($buoiHoc->diem_danh_stats->co_mat); ?></span> /
                                                        <span class="text-danger">✗ <?php echo e($buoiHoc->diem_danh_stats->vang); ?></span> /
                                                        <span class="text-warning">⏱ <?php echo e($buoiHoc->diem_danh_stats->di_tre); ?></span> /
                                                        <span class="text-info">☂ <?php echo e($buoiHoc->diem_danh_stats->nghi_phep); ?></span>
                                                    </div>
                                                    <small class="text-muted">
                                                        Tổng: <?php echo e($buoiHoc->diem_danh_stats->tong); ?>

                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa điểm danh</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('giangvien.diem-danh.show', $buoiHoc->id)); ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   title="Điểm danh">
                                                    <i class="bi bi-clipboard-check"></i> Điểm danh
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Hiển thị <?php echo e($buoiHocList->firstItem()); ?> - <?php echo e($buoiHocList->lastItem()); ?> 
                                trong tổng <?php echo e($buoiHocList->total()); ?> buổi học
                            </div>
                            <div>
                                <?php echo e($buoiHocList->appends(request()->query())->links()); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Không tìm thấy buổi học nào.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/diem-danh/index.blade.php ENDPATH**/ ?>