<?php $__env->startSection('title', 'Chi tiết lớp học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Chi tiết lớp học phần</h3>
                <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->ten_lop_hp); ?></p>
            </div>
            <a href="<?php echo e(route('giangvien.lop-giang-day.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin lớp học phần -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Mã lớp HP:</th>
                                    <td><strong><?php echo e($lopHocPhan->ma_lop_hp); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp HP:</th>
                                    <td><?php echo e($lopHocPhan->ten_lop_hp); ?></td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>
                                        <?php echo e($lopHocPhan->monHoc->ma_mon ?? ''); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?><br>
                                        <small class="text-muted">
                                            <?php echo e($lopHocPhan->monHoc->so_tin_chi ?? 0); ?> TC
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Học kỳ:</th>
                                    <td><?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> (<?php echo e($lopHocPhan->hocKy->nam_hoc); ?>)</td>
                                </tr>
                                <tr>
                                    <th>Vai trò của bạn:</th>
                                    <td>
                                        <?php if($phanCong->vai_tro == 'giang_vien_chinh'): ?>
                                            <span class="badge bg-primary">Giảng viên chính</span>
                                        <?php elseif($phanCong->vai_tro == 'giang_vien_phu'): ?>
                                            <span class="badge bg-info">Giảng viên phụ</span>
                                        <?php elseif($phanCong->vai_tro == 'tro_giang'): ?>
                                            <span class="badge bg-secondary">Trợ giảng</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Nhóm lớp:</th>
                                    <td><?php echo e($lopHocPhan->nhom_lop ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Sức chứa:</th>
                                    <td><?php echo e($lopHocPhan->suc_chua); ?> sinh viên</td>
                                </tr>
                                <tr>
                                    <th>Số lượng đăng ký:</th>
                                    <td>
                                        <strong><?php echo e($lopHocPhan->so_luong_dang_ky); ?></strong> / <?php echo e($lopHocPhan->suc_chua); ?>

                                        <?php if($lopHocPhan->so_luong_dang_ky >= $lopHocPhan->suc_chua): ?>
                                            <span class="badge bg-danger ms-2">Đầy</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hình thức:</th>
                                    <td>
                                        <?php if($lopHocPhan->hinh_thuc == 'truc_tiep'): ?>
                                            <span class="badge bg-success">Trực tiếp</span>
                                        <?php elseif($lopHocPhan->hinh_thuc == 'online'): ?>
                                            <span class="badge bg-info">Online</span>
                                        <?php elseif($lopHocPhan->hinh_thuc == 'hybrid'): ?>
                                            <span class="badge bg-warning">Hybrid</span>
                                        <?php endif; ?>
                                        <?php if($lopHocPhan->link_online): ?>
                                            <br><small><a href="<?php echo e($lopHocPhan->link_online); ?>" target="_blank"><?php echo e($lopHocPhan->link_online); ?></a></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        <?php if($lopHocPhan->trang_thai_lop == 'mo_dang_ky'): ?>
                                            <span class="badge bg-warning">Mở đăng ký</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'dang_hoc'): ?>
                                            <span class="badge bg-success">Đang học</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'ket_thuc'): ?>
                                            <span class="badge bg-secondary">Kết thúc</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'huy'): ?>
                                            <span class="badge bg-danger">Hủy</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Giảng viên phụ trách -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Giảng viên phụ trách</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Phân công</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $lopHocPhan->lopHocPhanGiangVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($pc->giangVien->ma_giang_vien); ?></td>
                                        <td><?php echo e($pc->giangVien->ho_ten); ?></td>
                                        <td><?php echo e($pc->giangVien->email); ?></td>
                                        <td>
                                            <?php if($pc->vai_tro == 'giang_vien_chinh'): ?>
                                                <span class="badge bg-primary">GV Chính</span>
                                            <?php elseif($pc->vai_tro == 'giang_vien_phu'): ?>
                                                <span class="badge bg-info">GV Phụ</span>
                                            <?php elseif($pc->vai_tro == 'tro_giang'): ?>
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($pc->phan_cong_giang_day ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lịch học cố định -->
            <?php if($lichHocCoDinh->isNotEmpty()): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Lịch học cố định</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Thứ</th>
                                        <th>Tiết bắt đầu</th>
                                        <th>Số tiết</th>
                                        <th>Phòng học</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lichHocCoDinh; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>Thứ <?php echo e($lich->thu); ?></td>
                                            <td>Tiết <?php echo e($lich->tiet_bat_dau); ?></td>
                                            <td><?php echo e($lich->so_tiet); ?> tiết</td>
                                            <td><?php echo e($lich->phong_hoc_id); ?></td>
                                            <td><?php echo e($lich->ghi_chu ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Danh sách sinh viên -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Danh sách sinh viên (<?php echo e($sinhViens->count()); ?>)</h4>
                        <div>
                            <a href="<?php echo e(route('giangvien.lop-giang-day.export-students', $lopHocPhan->id)); ?>" 
                               class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                            </a>
                            <a href="<?php echo e(route('giangvien.lop-giang-day.export-students-pdf', $lopHocPhan->id)); ?>" 
                               class="btn btn-danger btn-sm"
                               target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Lớp hành chính</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đăng ký</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhpsv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></strong></td>
                                        <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                        <td><?php echo e($lhpsv->sinhVien->email); ?></td>
                                        <td><?php echo e($lhpsv->sinhVien->so_dien_thoai ?? 'N/A'); ?></td>
                                        <td><?php echo e($lhpsv->sinhVien->lopHanhChinh->ma_lop ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($lhpsv->trang_thai == 'da_xep_lop'): ?>
                                                <span class="badge bg-info">Đã xếp lớp</span>
                                            <?php elseif($lhpsv->trang_thai == 'dang_hoc'): ?>
                                                <span class="badge bg-success">Đang học</span>
                                            <?php elseif($lhpsv->trang_thai == 'da_hoan_thanh'): ?>
                                                <span class="badge bg-primary">Đã hoàn thành</span>
                                            <?php elseif($lhpsv->trang_thai == 'bo_hoc'): ?>
                                                <span class="badge bg-danger">Bỏ học</span>
                                            <?php elseif($lhpsv->trang_thai == 'huy_dang_ky'): ?>
                                                <span class="badge bg-secondary">Hủy đăng ký</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($lhpsv->ngay_dang_ky ? $lhpsv->ngay_dang_ky->format('d/m/Y H:i') : 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Chưa có sinh viên nào trong lớp.
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

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/lop-giang-day/show.blade.php ENDPATH**/ ?>