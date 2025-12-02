<?php $__env->startSection('title', 'Chi tiết lớp học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết lớp học phần</h3>
                    <p class="text-subtitle text-muted">
                        <?php echo e($lopHocPhanSinhVien->lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhanSinhVien->lopHocPhan->ten_lop_hp); ?>

                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.lop-hoc-phan.index')); ?>">Lớp học phần</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="mb-3">
                <a href="<?php echo e(route('sinh-vien.lop-hoc-phan.lich-su-diem-danh', $lopHocPhanSinhVien->id)); ?>" class="btn btn-primary">
                    <i class="bi bi-calendar-check"></i> Xem lịch sử điểm danh
                </a>
                <a href="<?php echo e(route('sinh-vien.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> Thông tin lớp học phần
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Mã lớp HP:</th>
                                    <td><strong><?php echo e($lopHocPhanSinhVien->lopHocPhan->ma_lop_hp); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp HP:</th>
                                    <td><?php echo e($lopHocPhanSinhVien->lopHocPhan->ten_lop_hp); ?></td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>
                                        <strong><?php echo e($lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Số tín chỉ:</th>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo e($lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi); ?> TC
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Học kỳ:</th>
                                    <td>
                                        <?php echo e($lopHocPhanSinhVien->lopHocPhan->hocKy->ten_hoc_ky); ?>

                                        <br>
                                        <small class="text-muted"><?php echo e($lopHocPhanSinhVien->lopHocPhan->hocKy->nam_hoc); ?></small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Giảng viên:</th>
                                    <td>
                                        <?php if($lopHocPhanSinhVien->lopHocPhan->giangVienChinh && $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien): ?>
                                            <?php echo e($lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien->ho_ten); ?>

                                            <br>
                                            <small class="text-muted"><?php echo e($lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien->email); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa phân công</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hình thức:</th>
                                    <td>
                                        <?php if($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'offline'): ?>
                                            <span class="badge bg-success">Trực tiếp</span>
                                        <?php elseif($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'online'): ?>
                                            <span class="badge bg-info">Online</span>
                                        <?php elseif($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'hybrid'): ?>
                                            <span class="badge bg-warning">Hybrid</span>
                                        <?php endif; ?>
                                        <?php if($lopHocPhanSinhVien->lopHocPhan->link_online): ?>
                                            <br>
                                            <a href="<?php echo e($lopHocPhanSinhVien->lopHocPhan->link_online); ?>" target="_blank" class="text-primary">
                                                <i class="bi bi-link-45deg"></i> Link lớp học
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        <?php if($lopHocPhanSinhVien->trang_thai == 'da_xep_lop'): ?>
                                            <span class="badge bg-info">Đã xếp lớp</span>
                                        <?php elseif($lopHocPhanSinhVien->trang_thai == 'dang_hoc'): ?>
                                            <span class="badge bg-success">Đang học</span>
                                        <?php elseif($lopHocPhanSinhVien->trang_thai == 'da_hoan_thanh'): ?>
                                            <span class="badge bg-primary">Đã hoàn thành</span>
                                        <?php elseif($lopHocPhanSinhVien->trang_thai == 'bo_hoc'): ?>
                                            <span class="badge bg-danger">Bỏ học</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo e($lopHocPhanSinhVien->trang_thai); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng ký:</th>
                                    <td><?php echo e($lopHocPhanSinhVien->ngay_dang_ky ? $lopHocPhanSinhVien->ngay_dang_ky->format('d/m/Y H:i') : 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày xếp lớp:</th>
                                    <td><?php echo e($lopHocPhanSinhVien->ngay_xep_lop ? $lopHocPhanSinhVien->ngay_xep_lop->format('d/m/Y H:i') : 'N/A'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($ketQuaHocTap): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy"></i> Kết quả học tập
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm hệ 10</h6>
                                <h2 class="text-primary mb-0"><?php echo e(number_format($ketQuaHocTap->diem_he_10, 2)); ?></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm hệ 4</h6>
                                <h2 class="text-info mb-0"><?php echo e(number_format($ketQuaHocTap->diem_he_4, 2)); ?></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm chữ</h6>
                                <h2 class="text-success mb-0"><?php echo e($ketQuaHocTap->diem_chu); ?></h2>
                                <?php if($ketQuaHocTap->qua_mon): ?>
                                    <span class="badge bg-success mt-2">Qua môn</span>
                                <?php else: ?>
                                    <span class="badge bg-danger mt-2">Không qua môn</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($lichHocCoDinh->isNotEmpty()): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-calendar-week"></i> Lịch học cố định
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th>Tiết</th>
                                    <th>Giờ học</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $lichHocCoDinh; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($lich->ten_thu ?? 'N/A'); ?></strong>
                                        </td>
                                        <td>
                                            Tiết <?php echo e($lich->tiet_bat_dau); ?>

                                            <?php if($lich->tiet_ket_thuc != $lich->tiet_bat_dau): ?>
                                                - <?php echo e($lich->tiet_ket_thuc); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($lich->gio_bat_dau && $lich->gio_ket_thuc): ?>
                                                <?php echo e($lich->gio_bat_dau->format('H:i')); ?> - <?php echo e($lich->gio_ket_thuc->format('H:i')); ?>

                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e($lich->phongHoc->ten_phong ?? 'TBA'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($lich->giangVien->ho_ten ?? 'TBA'); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock"></i> Thời gian học
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Ngày bắt đầu:</th>
                                    <td><?php echo e($lopHocPhanSinhVien->lopHocPhan->ngay_bat_dau ? \Carbon\Carbon::parse($lopHocPhanSinhVien->lopHocPhan->ngay_bat_dau)->format('d/m/Y') : 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày kết thúc:</th>
                                    <td><?php echo e($lopHocPhanSinhVien->lopHocPhan->ngay_ket_thuc ? \Carbon\Carbon::parse($lopHocPhanSinhVien->lopHocPhan->ngay_ket_thuc)->format('d/m/Y') : 'N/A'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <?php if($lopHocPhanSinhVien->lopHocPhan->ghi_chu): ?>
                            <div>
                                <strong>Ghi chú:</strong>
                                <p class="text-muted"><?php echo e($lopHocPhanSinhVien->lopHocPhan->ghi_chu); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="d-flex justify-content-between gap-2">
                <a href="<?php echo e(route('sinh-vien.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <div class="btn-group">
                    <a href="<?php echo e(route('sinh-vien.diem.show', $lopHocPhanSinhVien->lopHocPhan->id)); ?>" class="btn btn-primary">
                        <i class="bi bi-clipboard-check"></i> Xem điểm
                    </a>
                    <a href="<?php echo e(route('sinh-vien.thoi-khoa-bieu.index', ['hoc_ky_id' => $lopHocPhanSinhVien->lopHocPhan->hoc_ky_id])); ?>" class="btn btn-info">
                        <i class="bi bi-calendar-week"></i> Thời khóa biểu
                    </a>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/lop-hoc-phan/show.blade.php ENDPATH**/ ?>