<?php $__env->startSection('title', 'Danh sách lớp giảng dạy'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Danh sách lớp giảng dạy</h3>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lớp học phần được phân công</h4>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <form method="GET" action="<?php echo e(route('giangvien.lop-giang-day.index')); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hoc_ky_id">Học kỳ</label>
                                    <select name="hoc_ky_id" id="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($hocKy->id); ?>" <?php echo e($hocKyId == $hocKy->id ? 'selected' : ''); ?>>
                                                <?php echo e($hocKy->ten_hoc_ky); ?> (<?php echo e($hocKy->nam_hoc); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Tên lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Vai trò</th>
                                    <th>Số SV</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $phanCongs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $phanCong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($phanCongs->firstItem() + $index); ?></td>
                                        <td>
                                            <strong><?php echo e($phanCong->lopHocPhan->ma_lop_hp); ?></strong>
                                        </td>
                                        <td><?php echo e($phanCong->lopHocPhan->ten_lop_hp); ?></td>
                                        <td>
                                            <?php echo e($phanCong->lopHocPhan->monHoc->ma_mon ?? ''); ?> - 
                                            <?php echo e($phanCong->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($phanCong->lopHocPhan->hocKy->ten_hoc_ky); ?><br>
                                            <small class="text-muted"><?php echo e($phanCong->lopHocPhan->hocKy->nam_hoc); ?></small>
                                        </td>
                                        <td>
                                            <?php if($phanCong->vai_tro == 'giang_vien_chinh'): ?>
                                                <span class="badge bg-primary">GV Chính</span>
                                            <?php elseif($phanCong->vai_tro == 'giang_vien_phu'): ?>
                                                <span class="badge bg-info">GV Phụ</span>
                                            <?php elseif($phanCong->vai_tro == 'tro_giang'): ?>
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark"><?php echo e($phanCong->vai_tro); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary">
                                                <?php echo e($phanCong->lopHocPhan->so_sinh_vien); ?>/<?php echo e($phanCong->lopHocPhan->suc_chua); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($phanCong->lopHocPhan->trang_thai_lop == 'mo_dang_ky'): ?>
                                                <span class="badge bg-warning">Mở đăng ký</span>
                                            <?php elseif($phanCong->lopHocPhan->trang_thai_lop == 'dang_hoc'): ?>
                                                <span class="badge bg-success">Đang học</span>
                                            <?php elseif($phanCong->lopHocPhan->trang_thai_lop == 'ket_thuc'): ?>
                                                <span class="badge bg-secondary">Kết thúc</span>
                                            <?php elseif($phanCong->lopHocPhan->trang_thai_lop == 'huy'): ?>
                                                <span class="badge bg-danger">Hủy</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark"><?php echo e($phanCong->lopHocPhan->trang_thai_lop); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('giangvien.lop-giang-day.show', $phanCong->lop_hoc_phan_id)); ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có lớp học phần nào được phân công.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($phanCongs->hasPages()): ?>
                        <div class="mt-3">
                            <?php echo e($phanCongs->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form');
        if (filterForm) {
            const selects = filterForm.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/lop-giang-day/index.blade.php ENDPATH**/ ?>