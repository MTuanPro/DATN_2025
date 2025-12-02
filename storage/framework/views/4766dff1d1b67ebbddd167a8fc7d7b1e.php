<?php $__env->startSection('title', 'Chi tiết điểm môn học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết điểm môn học</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết điểm các thành phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.diem.index')); ?>">Kết quả học tập</a>
                            </li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> <?php echo e($lhpsv->lopHocPhan->monHoc->ten_mon); ?>

                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mã môn:</strong></td>
                                    <td><?php echo e($lhpsv->lopHocPhan->monHoc->ma_mon); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Lớp học phần:</strong></td>
                                    <td><?php echo e($lhpsv->lopHocPhan->ten_lop_hp); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ:</strong></td>
                                    <td><?php echo e($lhpsv->lopHocPhan->hocKy->ten_hoc_ky); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Số tín chỉ:</strong></td>
                                    <td><?php echo e($lhpsv->lopHocPhan->monHoc->so_tin_chi); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Giảng viên:</strong></td>
<td><?php echo e($lhpsv->lopHocPhan->giangVienChinh->giangVien->user->ho_ten ?? '-'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Điểm thành phần</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">STT</th>
                                    <th>Loại điểm</th>
                                    <th width="100" class="text-center">Trọng số (%)</th>
                                    <th width="150" class="text-center">Điểm</th>
                                    <th width="150" class="text-center">Điểm sau trọng số</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tongDiem = 0;
                                ?>
                                <?php $__currentLoopData = $lhpsv->lopHocPhan->cauHinhDauDiem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cauHinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        // Lấy điểm của đầu điểm này (có thể có nhiều cột)
                                        $diems = $diemThanhPhan->get($cauHinh->id);
                                        
                                        // Tính trung bình nếu có nhiều cột
                                        if ($diems && $diems->count() > 0) {
                                            $diemGoc = $diems->avg('diem_so');
                                        } else {
                                            $diemGoc = null;
                                        }
                                        
                                        // Tính điểm sau trọng số
                                        $diemSauTrongSo = $diemGoc ? ($diemGoc * $cauHinh->ty_le) / 100 : null;

                                        if ($diemSauTrongSo) {
                                            $tongDiem += $diemSauTrongSo;
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo e($index + 1); ?></td>
                                        <td>
                                            <strong><?php echo e($cauHinh->ten_dau_diem); ?></strong>
                                        </td>
                                        <td class="text-center"><?php echo e($cauHinh->ty_le); ?>%</td>
                                        <td class="text-center">
                                            <?php if($diemGoc !== null): ?>
                                                <strong class="text-primary"><?php echo e(number_format($diemGoc, 2)); ?></strong>
                                            <?php else: ?>
<span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($diemSauTrongSo !== null): ?>
                                                <?php echo e(number_format($diemSauTrongSo, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Tổng điểm (Hệ 10):</strong></td>
                                    <td class="text-center">
                                        <h5 class="mb-0 text-primary">
                                            <?php echo e($lhpsv->ketQuaHocTap ? number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) : '0.00'); ?>

                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            
            <?php if($lhpsv->ketQuaHocTap): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Kết quả tổng hợp</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm hệ 10</h6>
                                    <h2 class="text-primary mb-0"><?php echo e(number_format($lhpsv->ketQuaHocTap->diem_he_10, 2)); ?>

                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm hệ 4</h6>
                                    <h2 class="text-success mb-0"><?php echo e(number_format($lhpsv->ketQuaHocTap->diem_he_4, 2)); ?>

                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm chữ</h6>
                                    <h2 class="mb-0">
<span class="badge bg-<?php echo e($lhpsv->ketQuaHocTap->diem_chu_badge); ?> fs-4">
                                            <?php echo e($lhpsv->ketQuaHocTap->diem_chu); ?>

                                        </span>
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Kết quả</h6>
                                    <h2 class="mb-0">
                                        <?php if($lhpsv->ketQuaHocTap->qua_mon): ?>
                                            <span class="badge bg-success fs-5">Đạt</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger fs-5">Không đạt</span>
                                        <?php endif; ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="text-center">
                <a href="<?php echo e(route('sinh-vien.diem.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/diem/show.blade.php ENDPATH**/ ?>