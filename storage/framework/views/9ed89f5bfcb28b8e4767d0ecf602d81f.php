<?php $__env->startSection('title', 'Lịch sử giảng dạy'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Lịch sử giảng dạy</h3>
                <p class="text-subtitle text-muted">Danh sách các buổi học đã hoàn thành</p>
            </div>
            <a href="<?php echo e(route('giangvien.buoi-hoc.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Tổng buổi đã dạy</h6>
                                    <h3 class="mb-0 text-success"><?php echo e($tongBuoiDay); ?></h3>
                                </div>
                                <div class="avatar bg-success">
                                    <i class="bi bi-check-circle text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Có tài liệu</h6>
                                    <h3 class="mb-0 text-primary"><?php echo e($buoiCoTaiLieu); ?></h3>
                                </div>
                                <div class="avatar bg-primary">
                                    <i class="bi bi-file-earmark-text text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Lớp học phần</h6>
                                    <h3 class="mb-0 text-info"><?php echo e($soLopHocPhan); ?></h3>
                                </div>
                                <div class="avatar bg-info">
                                    <i class="bi bi-book text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Tỷ lệ hoàn thành</h6>
                                    <h3 class="mb-0 text-warning"><?php echo e($tyLeHoanThanh); ?>%</h3>
                                </div>
                                <div class="avatar bg-warning">
                                    <i class="bi bi-graph-up text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('giangvien.buoi-hoc.history')); ?>" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <?php $__currentLoopData = $danhSachLopHocPhan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lop->id); ?>" <?php echo e(request('lop_hoc_phan_id') == $lop->id ? 'selected' : ''); ?>>
                                            <?php echo e($lop->ma_lop_hp); ?> - <?php echo e($lop->monHoc->ten_mon ?? 'N/A'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                            <div class="col-md-3">
                                <label class="form-label">Tài liệu</label>
                                <select name="co_tai_lieu" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <option value="1" <?php echo e(request('co_tai_lieu') == '1' ? 'selected' : ''); ?>>Có tài liệu</option>
                                    <option value="0" <?php echo e(request('co_tai_lieu') == '0' ? 'selected' : ''); ?>>Không có tài liệu</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách lịch sử -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Danh sách buổi học đã dạy
                        <span class="badge bg-success"><?php echo e($buoiHocList->total()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($buoiHocList->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày dạy</th>
                                        <th>Tiết</th>
                                        <th>Giờ</th>
                                        <th>Lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Phòng</th>
                                        <th>Nội dung</th>
                                        <th>Tài liệu</th>
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
                                            <td>
                                                <small>
                                                    <?php echo e($buoiHoc->gio_bat_dau); ?><br>
                                                    <?php echo e($buoiHoc->gio_ket_thuc); ?>

                                                </small>
                                            </td>
                                            <td><?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?></td>
                                            <td>
                                                <strong><?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></strong><br>
                                                <small class="text-muted"><?php echo e($buoiHoc->lopHocPhan->monHoc->ma_mon ?? ''); ?></small>
                                            </td>
                                            <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if($buoiHoc->noi_dung_giang_day): ?>
                                                    <div style="max-width: 200px;">
                                                        <?php echo e(Str::limit($buoiHoc->noi_dung_giang_day, 50)); ?>

                                                        <?php if(strlen($buoiHoc->noi_dung_giang_day) > 50): ?>
                                                            <button type="button" class="btn btn-link btn-sm p-0" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#contentModal<?php echo e($buoiHoc->id); ?>">
                                                                <small>Xem thêm</small>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Modal nội dung -->
                                                    <div class="modal fade" id="contentModal<?php echo e($buoiHoc->id); ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Nội dung giảng dạy</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Ngày:</strong> <?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?></p>
                                                                    <p><strong>Môn:</strong> <?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></p>
                                                                    <hr>
                                                                    <p><?php echo e($buoiHoc->noi_dung_giang_day); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($buoiHoc->tai_lieu_dinh_kem): ?>
                                                    <a href="<?php echo e(route('giangvien.buoi-hoc.download-tai-lieu', $buoiHoc->id)); ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank" 
                                                       title="Tải xuống tài liệu">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
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
                            Không tìm thấy buổi học nào đã dạy.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/buoi-hoc/history.blade.php ENDPATH**/ ?>