<?php $__env->startSection('title', 'Chương trình đào tạo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-mortarboard-fill text-primary"></i> Chương trình đào tạo</h3>
                    <p class="text-subtitle text-muted">Xem chương trình đào tạo và tiến độ học tập của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chương trình đào tạo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin sinh viên -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Thông tin chương trình đào tạo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-item">
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <strong>Sinh viên:</strong>
                                    <span><?php echo e($sinhVien->ho_ten); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-credit-card text-primary"></i>
                                    <strong>Mã sinh viên:</strong>
                                    <code class="bg-light px-2 py-1 rounded"><?php echo e($sinhVien->ma_sinh_vien); ?></code>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-people text-primary"></i>
                                    <strong>Lớp:</strong>
                                    <span><?php echo e($sinhVien->lopHanhChinh->ten_lop ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                    <strong>Khóa học:</strong>
                                    <span><?php echo e($sinhVien->lopHanhChinh->khoaHoc->ten_khoa_hoc ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-item">
                                    <i class="bi bi-building text-primary"></i>
                                    <strong>Khoa:</strong>
                                    <span><?php echo e($chuyenNganh->nganh->khoa->ten_khoa ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-diagram-3 text-primary"></i>
                                    <strong>Ngành:</strong>
                                    <span><?php echo e($chuyenNganh->nganh->ten_nganh ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-award text-primary"></i>
                                    <strong>Chuyên ngành:</strong>
                                    <span class="badge bg-primary"><?php echo e($chuyenNganh->ten_chuyen_nganh ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê tiến độ -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tổng tín chỉ CTĐT</h6>
                                    <h2 class="mb-0 text-primary"><?php echo e($thongKe['tong_tin_chi_ctdt']); ?></h2>
                                    <small class="text-muted">tín chỉ</small>
                                </div>
                                <div class="avatar avatar-xl bg-primary">
                                    <i class="bi bi-book text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tín chỉ đã đạt</h6>
                                    <h2 class="mb-0 text-success"><?php echo e($thongKe['tin_chi_dat']); ?></h2>
                                    <small class="text-success fw-bold"><?php echo e(number_format($tienDo, 1)); ?>% hoàn thành</small>
                                </div>
                                <div class="avatar avatar-xl bg-success">
                                    <i class="bi bi-check-circle text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Môn đã học</h6>
                                    <h2 class="mb-0 text-info"><?php echo e($thongKe['so_mon_da_hoc']); ?></h2>
                                    <small class="text-muted">/ <?php echo e($thongKe['so_mon_ctdt']); ?> môn</small>
                                </div>
                                <div class="avatar avatar-xl bg-info">
                                    <i class="bi bi-list-check text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Tiến độ học tập</h6>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-success" 
                                     role="progressbar" 
                                     style="width: <?php echo e($tienDo); ?>%"
                                     aria-valuenow="<?php echo e($tienDo); ?>" aria-valuemin="0" aria-valuemax="100">
                                    <strong><?php echo e(number_format($tienDo, 1)); ?>%</strong>
                                </div>
                            </div>
                            <small class="text-muted">
                                Còn lại: <?php echo e($thongKe['tong_tin_chi_ctdt'] - $thongKe['tin_chi_dat']); ?> tín chỉ
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chương trình theo học kỳ -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Chương trình đào tạo theo học kỳ</h5>
                </div>
                <div class="card-body">
                    <?php if($chuongTrinhTheoHocKy->isEmpty()): ?>
                        <div class="alert alert-warning border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Thông báo:</strong> Chưa có chương trình đào tạo cho chuyên ngành này.
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="accordionCTDT">
                            <?php $__currentLoopData = $chuongTrinhTheoHocKy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy => $monHocs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $tongTinChiHK = $monHocs->sum(function($m) { return $m->monHoc->so_tin_chi; });
                                    $soMonDaHoc = $monHocs->filter(function($m) use ($ketQuaHocTap) {
                                        $ketQua = $ketQuaHocTap->first(function ($kq) use ($m) {
                                            $monHocId = $kq->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
                                            return $monHocId == $m->mon_hoc_id;
                                        });
                                        return $ketQua && ($ketQua->diem_he_10 ?? 0) >= 4.0;
                                    })->count();
                                    $tienDoHK = $monHocs->count() > 0 ? ($soMonDaHoc / $monHocs->count()) * 100 : 0;
                                ?>
                                <div class="accordion-item border-0 shadow-sm mb-2">
                                    <h2 class="accordion-header" id="heading<?php echo e($hocKy); ?>">
                                        <button class="accordion-button <?php echo e($loop->first ? '' : 'collapsed'); ?> bg-light py-2" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse<?php echo e($hocKy); ?>"
                                                aria-expanded="<?php echo e($loop->first ? 'true' : 'false'); ?>" 
                                                aria-controls="collapse<?php echo e($hocKy); ?>"
                                                style="font-size: 0.95rem;">
                                            <div class="d-flex w-100 align-items-center justify-content-between pe-3">
                                                <div>
                                                    <i class="bi bi-calendar3 text-primary me-2" style="font-size: 0.9rem;"></i>
                                                    <strong style="font-size: 0.95rem;">Học kỳ <?php echo e($hocKy); ?></strong>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-primary" style="font-size: 0.75rem;"><?php echo e($monHocs->count()); ?> môn</span>
                                                    <span class="badge bg-info" style="font-size: 0.75rem;"><?php echo e($tongTinChiHK); ?> TC</span>
                                                    <span class="badge bg-success" style="font-size: 0.75rem;"><?php echo e($soMonDaHoc); ?>/<?php echo e($monHocs->count()); ?> đạt</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo e($hocKy); ?>" 
                                         class="accordion-collapse collapse <?php echo e($loop->first ? 'show' : ''); ?>"
                                         aria-labelledby="heading<?php echo e($hocKy); ?>" 
                                         data-bs-parent="#accordionCTDT">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="40" class="text-center">STT</th>
                                                            <th width="90">Mã môn</th>
                                                            <th>Tên môn học</th>
                                                            <th width="60" class="text-center">TC</th>
                                                            <th width="120">Loại môn</th>
                                                            <th width="90" class="text-center">Yêu cầu</th>
                                                            <th width="110" class="text-center">Trạng thái</th>
                                                            <th width="70" class="text-center">Điểm</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $ketQua = $ketQuaHocTap->first(function ($kq) use ($item) {
                                                                    $monHocId = $kq->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
                                                                    return $monHocId == $item->mon_hoc_id;
                                                                });
                                                                $daDat = $ketQua && ($ketQua->diem_he_10 ?? 0) >= 4.0;
                                                                $loaiLabels = [
                                                                    'dai_cuong' => 'Đại cương',
                                                                    'co_so_nganh' => 'Cơ sở ngành',
                                                                    'chuyen_nganh_bat_buoc' => 'CN bắt buộc',
                                                                    'chuyen_nganh_tu_chon' => 'CN tự chọn',
                                                                    'thuc_tap' => 'Thực tập',
                                                                    'do_an_tot_nghiep' => 'ĐATN',
                                                                ];
                                                                $loaiColors = [
                                                                    'dai_cuong' => 'secondary',
                                                                    'co_so_nganh' => 'info',
                                                                    'chuyen_nganh_bat_buoc' => 'primary',
                                                                    'chuyen_nganh_tu_chon' => 'dark',
                                                                    'thuc_tap' => 'warning',
                                                                    'do_an_tot_nghiep' => 'danger',
                                                                ];
                                                            ?>
                                                            <tr class="<?php echo e($daDat ? 'table-success' : ''); ?>" style="font-size: 0.9rem;">
                                                                <td class="text-center"><?php echo e($loop->iteration); ?></td>
                                                                <td>
                                                                    <code class="text-primary fw-bold" style="font-size: 0.85rem;"><?php echo e($item->monHoc->ma_mon); ?></code>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold"><?php echo e($item->monHoc->ten_mon); ?></div>
                                                                    <?php if($item->monHoc->monTienQuyet->isNotEmpty()): ?>
                                                                        <small class="text-warning">
                                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                                            <?php echo e($item->monHoc->monTienQuyet->count()); ?> môn tiên quyết
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-info" style="font-size: 0.75rem;"><?php echo e($item->monHoc->so_tin_chi); ?></span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo e($loaiColors[$item->loai_mon_hoc] ?? 'secondary'); ?>" style="font-size: 0.7rem;">
                                                                        <?php echo e($loaiLabels[$item->loai_mon_hoc] ?? $item->loai_mon_hoc); ?>

                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if($item->bat_buoc): ?>
                                                                        <span class="badge bg-danger" style="font-size: 0.7rem;">
                                                                            <i class="bi bi-star-fill"></i> Bắt buộc
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">Tự chọn</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if($ketQua): ?>
                                                                        <?php if($daDat): ?>
                                                                            <span class="badge bg-success" style="font-size: 0.7rem;">
                                                                                <i class="bi bi-check-circle-fill"></i> Đã đạt
                                                                            </span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                                                                <i class="bi bi-x-circle-fill"></i> Chưa đạt
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                                                            <i class="bi bi-dash-circle"></i> Chưa học
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if($ketQua && $ketQua->diem_he_10): ?>
                                                                        <strong class="fs-6 <?php echo e($daDat ? 'text-success' : 'text-danger'); ?>">
                                                                            <?php echo e(number_format($ketQua->diem_he_10, 1)); ?>

                                                                        </strong>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr style="font-size: 0.85rem;">
                                                            <th colspan="3" class="text-end">Tổng học kỳ <?php echo e($hocKy); ?>:</th>
                                                            <th class="text-center">
                                                                <span class="badge bg-primary"><?php echo e($tongTinChiHK); ?> TC</span>
                                                            </th>
                                                            <th colspan="2" class="text-center">
                                                                <span class="badge bg-success"><?php echo e($soMonDaHoc); ?>/<?php echo e($monHocs->count()); ?> môn</span>
                                                            </th>
                                                            <th colspan="2" class="text-center">
                                                                <span class="badge bg-info"><?php echo e(number_format($tienDoHK, 1)); ?>%</span>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ghi chú và hướng dẫn -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-gradient-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Chú giải & Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-palette"></i> Ý nghĩa màu sắc:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="badge bg-success me-2">Nền xanh lá</span> Môn đã đạt (điểm >= 4.0)
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-danger me-2"><i class="bi bi-star-fill"></i> Bắt buộc</span> 
                                    Môn học bắt buộc phải hoàn thành
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-secondary me-2">Tự chọn</span> 
                                    Môn tự chọn (đạt đủ số TC yêu cầu)
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Lưu ý quan trọng:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i> 
                                    Môn có ký hiệu này cần hoàn thành môn tiên quyết trước
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-award text-success"></i> 
                                    Xem <a href="<?php echo e(route('sinh-vien.chuong-trinh-dao-tao.dieu-kien-tot-nghiep')); ?>" class="fw-bold">Điều kiện tốt nghiệp</a> để biết thêm chi tiết
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-clock-history text-info"></i> 
                                    Cập nhật thường xuyên để theo dõi tiến độ học tập
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .bg-gradient-dark {
            background: linear-gradient(135deg, #434343 0%, #000000 100%);
        }
        .card {
            border-radius: 12px;
            border: none;
        }
        .shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
        }
        .info-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-left: 3px solid #667eea;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .info-item i {
            font-size: 1.2rem;
        }
        .info-item strong {
            min-width: 130px;
            color: #495057;
        }
        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .accordion-button:not(.collapsed)::after {
            filter: brightness(0) invert(1);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
        }
        .table-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
    </style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/sinhvien/chuong-trinh-dao-tao/index.blade.php ENDPATH**/ ?>