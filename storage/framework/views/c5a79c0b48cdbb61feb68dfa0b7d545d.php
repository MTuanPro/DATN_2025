<?php $__env->startSection('title', 'Thời khóa biểu chi tiết'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thời khóa biểu chi tiết</h3>
                    <p class="text-subtitle text-muted">Lịch học theo tuần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.thoi-khoa-bieu.index')); ?>">Thời khóa
                                    biểu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc tuần -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('sinh-vien.thoi-khoa-bieu.chi-tiet')); ?>"
                    class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Học kỳ
                        </label>
                        <select name="hoc_ky_id" class="form-select form-select-lg" id="selectHocKy">
                            <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hk->id); ?>" <?php echo e($selectedHocKy->id == $hk->id ? 'selected' : ''); ?>>
                                    <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-week"></i> Tuần
                        </label>
                        <input type="number" name="tuan" class="form-control form-control-lg"
                            value="<?php echo e($tuan); ?>" min="1" max="20" placeholder="Nhập số tuần (1-20)">
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-1"></i> Xem lịch học
                            </button>
                            <a href="<?php echo e(route('sinh-vien.thoi-khoa-bieu.index')); ?>" class="btn btn-secondary btn-lg px-4">
                                <i class="bi bi-arrow-left me-1"></i> TKB tổng quan
                            </a>
                            <a href="<?php echo e(route('sinh-vien.thoi-khoa-bieu.export-pdf', ['hoc_ky_id' => $selectedHocKy->id, 'tuan' => $tuan])); ?>"
                                class="btn btn-danger btn-lg px-4">
                                <i class="bi bi-file-pdf me-1"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thông tin tuần học -->
        <div class="card mb-4 shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="row text-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-calendar-range fs-3 mb-2"></i>
                            <h6 class="mb-1">Học kỳ</h6>
                            <p class="mb-0 fs-5 fw-bold"><?php echo e($selectedHocKy->ten_hoc_ky); ?> - <?php echo e($selectedHocKy->nam_hoc); ?>

                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-calendar-week fs-3 mb-2"></i>
                            <h6 class="mb-1">Tuần hiện tại</h6>
                            <p class="mb-0 fs-5 fw-bold">Tuần <?php echo e($tuan); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-clock-history fs-3 mb-2"></i>
                            <h6 class="mb-1">Thời gian</h6>
                            <?php
                                $startDate = $selectedHocKy->ngay_bat_dau->copy()->addWeeks($tuan - 1);
                                $endDate = $startDate->copy()->addDays(6);
                            ?>
                            <p class="mb-0 fs-5 fw-bold"><?php echo e($startDate->format('d/m/Y')); ?> -
                                <?php echo e($endDate->format('d/m/Y')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch học chi tiết theo ngày -->
        <?php
            $weekDays = [
                2 => 'Thứ Hai',
                3 => 'Thứ Ba',
                4 => 'Thứ Tư',
                5 => 'Thứ Năm',
                6 => 'Thứ Sáu',
                7 => 'Thứ Bảy',
                8 => 'Chủ Nhật',
            ];
        ?>

        <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thu => $tenThu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $lichTrongNgay = $lichHocTheoThu[$thu] ?? collect();
            ?>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header <?php echo e($lichTrongNgay->isEmpty() ? 'bg-light text-muted' : ''); ?>"
                    style="<?php echo e($lichTrongNgay->isNotEmpty() ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' : ''); ?>">
                    <h5 class="mb-0 <?php echo e($lichTrongNgay->isNotEmpty() ? 'text-white' : ''); ?>">
                        <i class="bi bi-calendar-day me-2"></i>
                        <?php echo e($tenThu); ?> - <?php echo e($startDate->copy()->addDays($thu - 2)->format('d/m/Y')); ?>

                        <?php if($lichTrongNgay->isNotEmpty()): ?>
                            <span class="badge bg-warning text-dark float-end">
                                <i class="bi bi-book"></i> <?php echo e($lichTrongNgay->count()); ?> buổi học
                            </span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($lichTrongNgay->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0 fs-5">Không có lịch học trong ngày này</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="100" class="text-center">Tiết</th>
                                        <th>Môn học</th>
                                        <th>Lớp học phần</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th width="100" class="text-center">Loại lớp</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lichTrongNgay->sortBy('gio_bat_dau'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $lopHocPhan = $lich->lopHocPhan;
                                            $loaiLop = $lopHocPhan->loai_lop ?? null;
                                        ?>
                                        <tr
                                            style="<?php echo e($loaiLop == 'ly_thuyet' ? 'background-color: #e3f2fd;' : 'background-color: #fff3e0;'); ?>">
                                            <td class="text-center align-middle">
                                                <?php if($lich->tiet_bat_dau && $lich->tiet_ket_thuc): ?>
                                                    <div class="badge bg-dark fs-6 px-3 py-2">
                                                        <?php echo e($lich->tiet_bat_dau); ?> -
                                                        <?php echo e($lich->tiet_ket_thuc); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div class="text-muted small mt-1">
                                                    <?php if($lich->gio_bat_dau && $lich->gio_ket_thuc): ?>
                                                        <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> - 
                                                        <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                    <?php elseif($lich->caHoc): ?>
                                                        <?php echo e(\Carbon\Carbon::parse($lich->caHoc->gio_bat_dau)->format('H:i')); ?> - 
                                                        <?php echo e(\Carbon\Carbon::parse($lich->caHoc->gio_ket_thuc)->format('H:i')); ?>

                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-bold text-primary">
                                                    <?php echo e($lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></div>
                                                <small class="text-muted">
                                                    <i class="bi bi-code-square"></i>
                                                    <?php echo e($lopHocPhan->monHoc->ma_mon ?? 'N/A'); ?>

                                                </small>
                                            </td>
                                            <td class="align-middle">
                                                <code
                                                    class="bg-white px-2 py-1 rounded"><?php echo e($lopHocPhan->ma_lop_hp ?? 'N/A'); ?></code>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($lich->phongHoc): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <?php echo e($lich->phongHoc->ten_phong); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted"><i class="bi bi-question-circle"></i> Chưa
                                                        xếp</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($lich->giangVien): ?>
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                    <?php echo e($lich->giangVien->ho_ten); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Chưa phân công</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if($loaiLop == 'ly_thuyet'): ?>
                                                    <span class="badge bg-primary"><i class="bi bi-book"></i> Lý
                                                        thuyết</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-laptop"></i>
                                                        Thực hành</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($lich->ghi_chu): ?>
                                                    <small><?php echo e($lich->ghi_chu); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function() {
                // Tự động submit khi đổi học kỳ
                $('#selectHocKy').change(function() {
                    $(this).closest('form').submit();
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/thoi-khoa-bieu/chi-tiet.blade.php ENDPATH**/ ?>