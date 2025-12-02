<?php $__env->startSection('title', 'Quản lý buổi học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Quản lý buổi học</h3>
            <a href="<?php echo e(route('giangvien.buoi-hoc.history')); ?>" class="btn btn-info">
                <i class="bi bi-clock-history"></i> Lịch sử đã dạy
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách buổi học</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="<?php echo e(route('giangvien.buoi-hoc.index')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="lop_hoc_phan_id" class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" id="lop_hoc_phan_id" class="form-select">
                                    <option value="">-- Tất cả lớp --</option>
                                    <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lhp->id); ?>" <?php echo e($lopHocPhanId == $lhp->id ? 'selected' : ''); ?>>
                                            <?php echo e($lhp->ma_lop_hp); ?> - <?php echo e($lhp->monHoc->ten_mon ?? 'N/A'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="trang_thai" class="form-label">Trạng thái</label>
                                <select name="trang_thai" id="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="chua_day" <?php echo e($trangThai == 'chua_day' ? 'selected' : ''); ?>>Chưa dạy</option>
                                    <option value="dang_day" <?php echo e($trangThai == 'dang_day' ? 'selected' : ''); ?>>Đang dạy</option>
                                    <option value="da_day" <?php echo e($trangThai == 'da_day' ? 'selected' : ''); ?>>Đã dạy</option>
                                    <option value="huy" <?php echo e($trangThai == 'huy' ? 'selected' : ''); ?>>Hủy</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tu_ngay" class="form-label">Từ ngày</label>
                                <input type="date" name="tu_ngay" id="tu_ngay" class="form-control" value="<?php echo e($tuNgay); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="den_ngay" class="form-label">Đến ngày</label>
                                <input type="date" name="den_ngay" id="den_ngay" class="form-control" value="<?php echo e($denNgay); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                    <a href="<?php echo e(route('giangvien.buoi-hoc.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Xóa bộ lọc
                                    </a>
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
                                    <th>Ngày học</th>
                                    <th>Tiết</th>
                                    <th>Giờ</th>
                                    <th>Lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Phòng</th>
                                    <th>Nội dung</th>
                                    <th>Tài liệu</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $buoiHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $buoiHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($buoiHocs->firstItem() + $index); ?></td>
                                        <td>
                                            <strong><?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?></strong><br>
                                            <small class="text-muted"><?php echo e($buoiHoc->ngay_hoc->dayName); ?></small>
                                        </td>
                                        <td><?php echo e($buoiHoc->tiet_bat_dau); ?> - <?php echo e($buoiHoc->tiet_ket_thuc); ?></td>
                                        <td>
                                            <small><?php echo e($buoiHoc->gio_bat_dau); ?> - <?php echo e($buoiHoc->gio_ket_thuc); ?></small>
                                        </td>
                                        <td><?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?></td>
                                        <td><?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></td>
                                        <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($buoiHoc->noi_dung_giang_day): ?>
                                                <small><?php echo e(Str::limit($buoiHoc->noi_dung_giang_day, 30)); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($buoiHoc->tai_lieu_dinh_kem): ?>
                                                <i class="bi bi-file-earmark-check text-success" title="Có tài liệu"></i>
                                            <?php else: ?>
                                                <i class="bi bi-dash text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($buoiHoc->trang_thai == 'chua_day'): ?>
                                                <span class="badge bg-secondary">Chưa dạy</span>
                                            <?php elseif($buoiHoc->trang_thai == 'dang_day'): ?>
                                                <span class="badge bg-warning">Đang dạy</span>
                                            <?php elseif($buoiHoc->trang_thai == 'da_day'): ?>
                                                <span class="badge bg-success">Đã dạy</span>
                                            <?php elseif($buoiHoc->trang_thai == 'huy'): ?>
                                                <span class="badge bg-danger">Hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('giangvien.buoi-hoc.edit', $buoiHoc->id)); ?>" 
                                               class="btn btn-sm btn-primary"
                                               title="Cập nhật">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có buổi học nào.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($buoiHocs->hasPages()): ?>
                        <div class="mt-3">
                            <?php echo e($buoiHocs->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto submit on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select.form-select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/buoi-hoc/index.blade.php ENDPATH**/ ?>