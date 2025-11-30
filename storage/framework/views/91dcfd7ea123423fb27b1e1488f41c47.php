<?php $__env->startSection('title', 'Lịch học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học</h3>
                    <p class="text-subtitle text-muted">
                        <?php if($hocKy): ?>
                            Học kỳ: <?php echo e($hocKy->ten_hoc_ky . ' - ' . $hocKy->nam_hoc); ?>

                        <?php else: ?>
                            Chưa có học kỳ
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.thoi-khoa-bieu.index')); ?>">Thời khóa biểu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('sinh-vien.thoi-khoa-bieu.lich-hoc')); ?>" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Học kỳ
                        </label>
                        <select name="hoc_ky_id" class="form-select form-select-lg">
                            <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hk->id); ?>" <?php echo e($hocKy && $hocKy->id == $hk->id ? 'selected' : ''); ?>>
                                    <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clock-history"></i> Thời gian
                        </label>
                        <select name="thoi_gian" class="form-select form-select-lg" onchange="this.form.submit()">
                            <optgroup label="Tương lai">
                                <option value="7_ngay_toi" <?php echo e($thoiGianFilter == '7_ngay_toi' ? 'selected' : ''); ?>>7 ngày tới</option>
                                <option value="14_ngay_toi" <?php echo e($thoiGianFilter == '14_ngay_toi' ? 'selected' : ''); ?>>14 ngày tới</option>
                                <option value="30_ngay_toi" <?php echo e($thoiGianFilter == '30_ngay_toi' ? 'selected' : ''); ?>>30 ngày tới</option>
                                <option value="60_ngay_toi" <?php echo e($thoiGianFilter == '60_ngay_toi' ? 'selected' : ''); ?>>60 ngày tới</option>
                                <option value="90_ngay_toi" <?php echo e($thoiGianFilter == '90_ngay_toi' ? 'selected' : ''); ?>>90 ngày tới</option>
                            </optgroup>
                            <optgroup label="Quá khứ">
                                <option value="7_ngay_truoc" <?php echo e($thoiGianFilter == '7_ngay_truoc' ? 'selected' : ''); ?>>7 ngày trước</option>
                                <option value="14_ngay_truoc" <?php echo e($thoiGianFilter == '14_ngay_truoc' ? 'selected' : ''); ?>>14 ngày trước</option>
                                <option value="30_ngay_truoc" <?php echo e($thoiGianFilter == '30_ngay_truoc' ? 'selected' : ''); ?>>30 ngày trước</option>
                                <option value="60_ngay_truoc" <?php echo e($thoiGianFilter == '60_ngay_truoc' ? 'selected' : ''); ?>>60 ngày trước</option>
                                <option value="90_ngay_truoc" <?php echo e($thoiGianFilter == '90_ngay_truoc' ? 'selected' : ''); ?>>90 ngày trước</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search me-1"></i> Xem lịch học
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if(!$hocKy): ?>
            <div class="alert alert-warning">
                <h4 class="alert-heading">Thông báo</h4>
                <p>Không tìm thấy học kỳ hiện tại.</p>
            </div>
        <?php else: ?>
            <!-- Bảng lịch học -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3"></i> Danh sách lịch học
                        <?php if(isset($startDate) && isset($endDate)): ?>
                            <small class="ms-2">(<?php echo e($startDate->format('d/m/Y')); ?> - <?php echo e($endDate->format('d/m/Y')); ?>)</small>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if($lichHocList->isEmpty()): ?>
                        <div class="alert alert-info m-4 mb-0">
                            <i class="bi bi-info-circle"></i> Không có lịch học trong khoảng thời gian đã chọn.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th width="150">Thứ</th>
                                        <th width="120">Ngày</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th width="150" class="text-center">Thời gian</th>
                                        <th width="100" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lichHocList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $ngayHoc = \Carbon\Carbon::parse($lich->ngay_hoc);
                                            $thuTrongTuan = $ngayHoc->dayOfWeek; // 0 = CN, 1 = T2, ..., 6 = T7
                                            $thuNames = [
                                                0 => 'Chủ nhật',
                                                1 => 'Thứ Hai',
                                                2 => 'Thứ Ba',
                                                3 => 'Thứ Tư',
                                                4 => 'Thứ Năm',
                                                5 => 'Thứ Sáu',
                                                6 => 'Thứ Bảy',
                                            ];
                                            $tenThu = $thuNames[$thuTrongTuan] ?? 'N/A';
                                            $lopHocPhan = $lich->lopHocPhan;
                                            $monHoc = $lopHocPhan ? $lopHocPhan->monHoc : null;
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo e($lichHocList->firstItem() + $index); ?></td>
                                            <td class="align-middle">
                                                <span class="badge bg-info"><?php echo e($tenThu); ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?php echo e($ngayHoc->format('d/m/Y')); ?></strong>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($monHoc): ?>
                                                    <code class="bg-light px-2 py-1 rounded"><?php echo e($monHoc->ma_mon); ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($monHoc): ?>
                                                    <strong class="text-primary"><?php echo e($monHoc->ten_mon); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($lich->phongHoc): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-geo-alt"></i> <?php echo e($lich->phongHoc->ten_phong); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa xếp</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if($lich->giangVien): ?>
                                                    <i class="bi bi-person-fill text-primary"></i> <?php echo e($lich->giangVien->ho_ten); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Chưa phân công</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if($lich->gio_bat_dau && $lich->gio_ket_thuc): ?>
                                                    <span class="badge bg-primary">
                                                        <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> - 
                                                        <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                    </span>
                                                <?php elseif($lich->caHoc): ?>
                                                    <span class="badge bg-primary">
                                                        <?php echo e(\Carbon\Carbon::parse($lich->caHoc->gio_bat_dau)->format('H:i')); ?> - 
                                                        <?php echo e(\Carbon\Carbon::parse($lich->caHoc->gio_ket_thuc)->format('H:i')); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if($lopHocPhan): ?>
                                                    <a href="<?php echo e(route('sinh-vien.lop-hoc-phan.show', $lopHocPhan->id)); ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Chi tiết">
                                                        <i class="bi bi-eye"></i>
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
                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">
                                        Đang xem <?php echo e($lichHocList->firstItem() ?? 0); ?> đến <?php echo e($lichHocList->lastItem() ?? 0); ?> 
                                        trong tổng số <?php echo e($lichHocList->total()); ?> mục
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div class="btn-group">
                                            <?php if($lichHocList->onFirstPage()): ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled>Trước</button>
                                            <?php else: ?>
                                                <a href="<?php echo e($lichHocList->previousPageUrl()); ?>" class="btn btn-sm btn-outline-primary">Trước</a>
                                            <?php endif; ?>
                                            
                                            <?php
                                                $currentPage = $lichHocList->currentPage();
                                                $lastPage = $lichHocList->lastPage();
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($lastPage, $currentPage + 2);
                                            ?>
                                            
                                            <?php if($startPage > 1): ?>
                                                <a href="<?php echo e($lichHocList->url(1)); ?>" class="btn btn-sm btn-outline-primary">1</a>
                                                <?php if($startPage > 2): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>...</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <?php for($page = $startPage; $page <= $endPage; $page++): ?>
                                                <?php if($page == $currentPage): ?>
                                                    <button class="btn btn-sm btn-primary"><?php echo e($page); ?></button>
                                                <?php else: ?>
                                                    <a href="<?php echo e($lichHocList->url($page)); ?>" class="btn btn-sm btn-outline-primary"><?php echo e($page); ?></a>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            
                                            <?php if($endPage < $lastPage): ?>
                                                <?php if($endPage < $lastPage - 1): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>...</button>
                                                <?php endif; ?>
                                                <a href="<?php echo e($lichHocList->url($lastPage)); ?>" class="btn btn-sm btn-outline-primary"><?php echo e($lastPage); ?></a>
                                            <?php endif; ?>
                                            
                                            <?php if($lichHocList->hasMorePages()): ?>
                                                <a href="<?php echo e($lichHocList->nextPageUrl()); ?>" class="btn btn-sm btn-outline-primary">Tiếp</a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled>Tiếp</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/sinhvien/thoi-khoa-bieu/lich-hoc.blade.php ENDPATH**/ ?>