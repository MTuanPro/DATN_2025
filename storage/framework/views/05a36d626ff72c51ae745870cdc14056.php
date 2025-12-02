<?php $__env->startSection('title', 'Chi tiết lớp học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Chi tiết lớp học phần</h3>
                <p class="text-subtitle text-muted">
                    <strong><?php echo e($lopHocPhan->ma_lop_hp); ?></strong> - <?php echo e($lopHocPhan->ten_lop_hp); ?>

                </p>
            </div>
            <div class="d-flex gap-2">
                <?php if(!isset($lopHocPhan->da_ket_thuc) || !$lopHocPhan->da_ket_thuc): ?>
                    <a href="<?php echo e(route('giangvien.nhap-diem.show', $lopHocPhan->id)); ?>" 
                       class="btn btn-success">
                        <i class="bi bi-pencil-square"></i> Nhập điểm
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('giangvien.lop-giang-day.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="classTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" 
                            id="info-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#info" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-info-circle"></i> Thông tin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="results-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#results" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-trophy"></i> Kết quả học tập
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="classTabContent">
                <!-- Tab Thông tin -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <!-- Thông tin tổng quan -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0"><?php echo e($sinhViens->count()); ?></h5>
                                    <p class="text-muted mb-0">Sinh viên</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-check text-success" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0"><?php echo e($lopHocPhan->suc_chua); ?></h5>
                                    <p class="text-muted mb-0">Sức chứa</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-book text-info" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0"><?php echo e($lopHocPhan->monHoc->so_tin_chi ?? 0); ?></h5>
                                    <p class="text-muted mb-0">Tín chỉ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0">
                                        <?php if($lopHocPhan->trang_thai_lop == 'mo_dang_ky'): ?>
                                            <span class="badge bg-warning">Mở đăng ký</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'dang_hoc'): ?>
                                            <span class="badge bg-success">Đang học</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'ket_thuc'): ?>
                                            <span class="badge bg-secondary">Kết thúc</span>
                                        <?php elseif($lopHocPhan->trang_thai_lop == 'da_khoa_diem'): ?>
                                            <span class="badge bg-danger">Đã khóa điểm</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><?php echo e($lopHocPhan->trang_thai_lop); ?></span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="text-muted mb-0 mt-1">Trạng thái</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin lớp học phần -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Thông tin lớp học phần
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 40%;" class="text-muted">Mã lớp HP:</th>
                                            <td><strong class="text-primary"><?php echo e($lopHocPhan->ma_lop_hp); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Tên lớp HP:</th>
                                            <td><?php echo e($lopHocPhan->ten_lop_hp); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Môn học:</th>
                                            <td>
                                                <strong><?php echo e($lopHocPhan->monHoc->ma_mon ?? ''); ?></strong> - <?php echo e($lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?><br>
                                                <span class="badge bg-info"><?php echo e($lopHocPhan->monHoc->so_tin_chi ?? 0); ?> tín chỉ</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Học kỳ:</th>
                                            <td>
                                                <i class="bi bi-calendar3"></i> <?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?><br>
                                                <small class="text-muted"><?php echo e($lopHocPhan->hocKy->nam_hoc); ?></small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Vai trò của bạn:</th>
                                            <td>
                                                <?php if($phanCong->vai_tro == 'giang_vien_chinh'): ?>
                                                    <span class="badge bg-primary"><i class="bi bi-star-fill"></i> Giảng viên chính</span>
                                                <?php elseif($phanCong->vai_tro == 'giang_vien_phu'): ?>
                                                    <span class="badge bg-info"><i class="bi bi-person"></i> Giảng viên phụ</span>
                                                <?php elseif($phanCong->vai_tro == 'tro_giang'): ?>
                                                    <span class="badge bg-secondary"><i class="bi bi-person-check"></i> Trợ giảng</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 40%;" class="text-muted">Nhóm lớp:</th>
                                            <td>
                                                <?php if($lopHocPhan->nhom_lop): ?>
                                                    <span class="badge bg-secondary"><?php echo e($lopHocPhan->nhom_lop); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Sức chứa:</th>
                                            <td>
                                                <i class="bi bi-people"></i> <?php echo e($lopHocPhan->suc_chua); ?> sinh viên
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Số lượng đăng ký:</th>
                                            <td>
                                                <strong class="text-primary"><?php echo e($lopHocPhan->so_luong_dang_ky); ?></strong> / <?php echo e($lopHocPhan->suc_chua); ?>

                                                <?php
                                                    $tyLeDangKy = $lopHocPhan->suc_chua > 0 ? round(($lopHocPhan->so_luong_dang_ky / $lopHocPhan->suc_chua) * 100, 1) : 0;
                                                ?>
                                                <span class="badge <?php echo e($tyLeDangKy >= 100 ? 'bg-danger' : ($tyLeDangKy >= 80 ? 'bg-warning' : 'bg-success')); ?> ms-2">
                                                    <?php echo e($tyLeDangKy); ?>%
                                                </span>
                                                <?php if($lopHocPhan->so_luong_dang_ky >= $lopHocPhan->suc_chua): ?>
                                                    <span class="badge bg-danger ms-2">Đầy</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Hình thức:</th>
                                            <td>
                                                <?php if($lopHocPhan->hinh_thuc == 'truc_tiep'): ?>
                                                    <span class="badge bg-success"><i class="bi bi-building"></i> Trực tiếp</span>
                                                <?php elseif($lopHocPhan->hinh_thuc == 'online'): ?>
                                                    <span class="badge bg-info"><i class="bi bi-camera-video"></i> Online</span>
                                                <?php elseif($lopHocPhan->hinh_thuc == 'hybrid'): ?>
                                                    <span class="badge bg-warning"><i class="bi bi-laptop"></i> Hybrid</span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                                <?php if($lopHocPhan->link_online): ?>
                                                    <br><small><a href="<?php echo e($lopHocPhan->link_online); ?>" target="_blank" class="text-primary">
                                                        <i class="bi bi-link-45deg"></i> <?php echo e($lopHocPhan->link_online); ?>

                                                    </a></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Ngày bắt đầu:</th>
                                            <td>
                                                <?php if($lopHocPhan->ngay_bat_dau): ?>
                                                    <i class="bi bi-calendar-event"></i> <?php echo e(\Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau)->format('d/m/Y')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Chưa có</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Ngày kết thúc:</th>
                                            <td>
                                                <?php if($lopHocPhan->ngay_ket_thuc): ?>
                                                    <i class="bi bi-calendar-x"></i> <?php echo e(\Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Chưa có</span>
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
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-person-badge"></i> Giảng viên phụ trách
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã GV</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>Số điện thoại</th>
                                            <th>Vai trò</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $lopHocPhan->lopHocPhanGiangVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><strong><?php echo e($pc->giangVien->ma_giang_vien); ?></strong></td>
                                                <td>
                                                    <i class="bi bi-person-circle"></i> <?php echo e($pc->giangVien->ho_ten); ?>

                                                </td>
                                                <td>
                                                    <i class="bi bi-envelope"></i> 
                                                    <a href="mailto:<?php echo e($pc->giangVien->email); ?>"><?php echo e($pc->giangVien->email); ?></a>
                                                </td>
                                                <td>
                                                    <?php if($pc->giangVien->so_dien_thoai): ?>
                                                        <i class="bi bi-telephone"></i> <?php echo e($pc->giangVien->so_dien_thoai); ?>

                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($pc->vai_tro == 'giang_vien_chinh'): ?>
                                                        <span class="badge bg-primary"><i class="bi bi-star-fill"></i> GV Chính</span>
                                                    <?php elseif($pc->vai_tro == 'giang_vien_phu'): ?>
                                                        <span class="badge bg-info"><i class="bi bi-person"></i> GV Phụ</span>
                                                    <?php elseif($pc->vai_tro == 'tro_giang'): ?>
                                                        <span class="badge bg-secondary"><i class="bi bi-person-check"></i> Trợ giảng</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark"><?php echo e($pc->vai_tro); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Chưa có giảng viên được phân công</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch học cố định -->
                    <?php if($lichHocCoDinh->isNotEmpty()): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h4 class="card-title mb-0">
                                    <i class="bi bi-calendar-week"></i> Lịch học cố định
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Thứ</th>
                                                <th>Tiết</th>
                                                <th>Giờ học</th>
                                                <th>Phòng học</th>
                                                <th>Ghi chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $lichHocCoDinh; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php
                                                                $thuMap = [2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7', 8 => 'Chủ nhật'];
                                                                $thu = $thuMap[$lich->thu_trong_tuan] ?? 'Thứ ' . $lich->thu_trong_tuan;
                                                            ?>
                                                            <?php echo e($thu); ?>

                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            Tiết <?php echo e($lich->tiet_bat_dau); ?>-<?php echo e($lich->tiet_ket_thuc); ?>

                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if($lich->gio_bat_dau && $lich->gio_ket_thuc): ?>
                                                            <i class="bi bi-clock"></i> 
                                                            <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> - 
                                                            <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($lich->phongHoc): ?>
                                                            <i class="bi bi-building"></i> <?php echo e($lich->phongHoc->ten_phong); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">Chưa phân phòng</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($lich->link_online): ?>
                                                            <a href="<?php echo e($lich->link_online); ?>" target="_blank" class="text-primary">
                                                                <i class="bi bi-link-45deg"></i> Link online
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted"><?php echo e($lich->ghi_chu ?? '-'); ?></span>
                                                        <?php endif; ?>
                                                    </td>
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
                        <div class="card-header bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    <i class="bi bi-people"></i> Danh sách sinh viên 
                                    <span class="badge bg-light text-dark"><?php echo e($sinhViens->count()); ?></span>
                                </h4>
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
                            <?php if($sinhViens->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 10%;">Mã SV</th>
                                                <th style="width: 20%;">Họ tên</th>
                                                <th style="width: 20%;">Email</th>
                                                <th style="width: 12%;">Số điện thoại</th>
                                                <th style="width: 12%;">Lớp hành chính</th>
                                                <th style="width: 10%;">Trạng thái</th>
                                                <th style="width: 11%;">Ngày đăng ký</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhpsv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($index + 1); ?></td>
                                                    <td><strong class="text-primary"><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></strong></td>
                                                    <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                                    <td>
                                                        <a href="mailto:<?php echo e($lhpsv->sinhVien->email); ?>">
                                                            <i class="bi bi-envelope"></i> <?php echo e($lhpsv->sinhVien->email); ?>

                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php if($lhpsv->sinhVien->so_dien_thoai): ?>
                                                            <i class="bi bi-telephone"></i> <?php echo e($lhpsv->sinhVien->so_dien_thoai); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($lhpsv->sinhVien->lopHanhChinh): ?>
                                                            <span class="badge bg-secondary"><?php echo e($lhpsv->sinhVien->lopHanhChinh->ma_lop); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($lhpsv->trang_thai == 'da_xep_lop'): ?>
                                                            <span class="badge bg-info"><i class="bi bi-check-circle"></i> Đã xếp lớp</span>
                                                        <?php elseif($lhpsv->trang_thai == 'dang_hoc'): ?>
                                                            <span class="badge bg-success"><i class="bi bi-person-check"></i> Đang học</span>
                                                        <?php elseif($lhpsv->trang_thai == 'da_hoan_thanh'): ?>
                                                            <span class="badge bg-primary"><i class="bi bi-trophy"></i> Đã hoàn thành</span>
                                                        <?php elseif($lhpsv->trang_thai == 'bo_hoc'): ?>
                                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Bỏ học</span>
                                                        <?php elseif($lhpsv->trang_thai == 'huy_dang_ky'): ?>
                                                            <span class="badge bg-secondary"><i class="bi bi-x-octagon"></i> Hủy đăng ký</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($lhpsv->ngay_dang_ky): ?>
                                                            <small>
                                                                <i class="bi bi-calendar"></i> <?php echo e($lhpsv->ngay_dang_ky->format('d/m/Y')); ?><br>
                                                                <i class="bi bi-clock"></i> <?php echo e($lhpsv->ngay_dang_ky->format('H:i')); ?>

                                                            </small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">Chưa có sinh viên nào trong lớp.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab Kết quả học tập -->
                <div class="tab-pane fade" id="results" role="tabpanel">
                    <?php if(isset($cauHinhs) && $cauHinhs->isNotEmpty() && isset($danhSachSinhVienKetQua)): ?>
                        <!-- Thống kê tổng quan -->
                        <?php if(isset($thongKe)): ?>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon purple mb-2">
                                                    <i class="bi bi-people-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                                    <h3 class="font-extrabold mb-0"><?php echo e($thongKe['tong_sv']); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon green mb-2">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Qua môn</h6>
                                                    <h3 class="font-extrabold mb-0"><?php echo e($thongKe['sv_qua_mon']); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon red mb-2">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Trượt</h6>
                                                    <h3 class="font-extrabold mb-0"><?php echo e($thongKe['sv_truot']); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="bi bi-graph-up"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Điểm TB</h6>
                                                    <h3 class="font-extrabold mb-0"><?php echo e(number_format($thongKe['diem_trung_binh'], 2)); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Bảng điểm -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="bi bi-table"></i> Bảng điểm tổng kết
                                    </h4>
                                    <div>
                                        <a href="<?php echo e(route('giangvien.ket-qua-hoc-tap.export-excel', $lopHocPhan->id)); ?>" 
                                           class="btn btn-light btn-sm">
                                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                                        </a>
                                        <a href="<?php echo e(route('giangvien.ket-qua-hoc-tap.export-pdf', $lopHocPhan->id)); ?>" 
                                           class="btn btn-light btn-sm"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th rowspan="2" class="align-middle text-center">STT</th>
                                                <th rowspan="2" class="align-middle">Mã SV</th>
                                                <th rowspan="2" class="align-middle">Họ tên</th>
                                                <th rowspan="2" class="align-middle">Lớp HC</th>
                                                <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th colspan="<?php echo e($ch->so_cot); ?>" class="text-center">
                                                        <?php echo e($ch->ten_dau_diem); ?><br>
                                                        <small class="text-muted">(<?php echo e($ch->ty_le); ?>%)</small>
                                                    </th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <th rowspan="2" class="align-middle text-center">Hệ 10</th>
                                                <th rowspan="2" class="align-middle text-center">Hệ 4</th>
                                                <th rowspan="2" class="align-middle text-center">Chữ</th>
                                                <th rowspan="2" class="align-middle text-center">Kết quả</th>
                                            </tr>
                                            <tr>
                                                <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php for($i = 1; $i <= $ch->so_cot; $i++): ?>
                                                        <th class="text-center">
                                                            <?php if($ch->so_cot > 1): ?> Cột <?php echo e($i); ?> <?php else: ?> - <?php endif; ?>
                                                        </th>
                                                    <?php endfor; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $danhSachSinhVienKetQua; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhpsv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                                    <td><strong class="text-primary"><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></strong></td>
                                                    <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                                    <td>
                                                        <?php if($lhpsv->sinhVien->lopHanhChinh): ?>
                                                            <span class="badge bg-secondary"><?php echo e($lhpsv->sinhVien->lopHanhChinh->ma_lop); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    
                                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php for($cot = 1; $cot <= $ch->so_cot; $cot++): ?>
                                                            <?php
                                                                $diem = $lhpsv->danh_sach_diem->where('cau_hinh_id', $ch->id)->where('cot_diem', $cot)->first();
                                                            ?>
                                                            <td class="text-center">
                                                                <?php if($diem): ?>
                                                                    <strong><?php echo e(number_format($diem->diem_so, 2)); ?></strong>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php endfor; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    
                                                    <td class="text-center">
                                                        <?php if($lhpsv->diem_tong_ket): ?>
                                                            <strong class="text-primary"><?php echo e(number_format($lhpsv->diem_tong_ket, 2)); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if($lhpsv->diem_he_4): ?>
                                                            <?php echo e(number_format($lhpsv->diem_he_4, 2)); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if($lhpsv->diem_chu): ?>
                                                            <span class="badge bg-info"><?php echo e($lhpsv->diem_chu); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if($lhpsv->diem_tong_ket): ?>
                                                            <?php if($lhpsv->diem_tong_ket >= 4): ?>
                                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Qua môn</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Trượt</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Chưa có</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="20" class="text-center text-muted py-4">
                                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                        <p class="mt-2 mb-0">Chưa có dữ liệu điểm</p>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Lớp học phần chưa có cấu hình đầu điểm hoặc chưa có dữ liệu điểm.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Auto switch to results tab if hash is #results
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#results') {
                const resultsTab = document.getElementById('results-tab');
                if (resultsTab) {
                    const tab = new bootstrap.Tab(resultsTab);
                    tab.show();
                }
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/lop-giang-day/show.blade.php ENDPATH**/ ?>