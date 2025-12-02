<?php $__env->startSection('title', 'Chi tiết Học phí'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết học phí sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Thông tin sinh viên</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>MSSV:</strong></td>
                                    <td><?php echo e($hocPhi->sinhVien->ma_sinh_vien); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td><?php echo e($hocPhi->sinhVien->ho_ten); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ:</strong></td>
                                    <td><?php echo e($hocPhi->hocKy->ten_hoc_ky); ?> - <?php echo e($hocPhi->hocKy->nam_hoc); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Hạn đóng:</strong></td>
                                    <td><span class="badge bg-warning"><?php echo e($hocPhi->han_dong->format('d/m/Y')); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <?php if($hocPhi->trang_thai == 'da_nop_du'): ?>
                                            <span class="badge bg-success">Đã nộp đủ</span>
                                        <?php elseif($hocPhi->trang_thai == 'qua_han'): ?>
                                            <span class="badge bg-danger">Quá hạn</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa nộp đủ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Chi tiết học phí từng môn</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã môn</th>
                                            <th>Tên môn</th>
                                            <th>Số tín chỉ</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $hocPhi->chiTietHocPhiMon ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($ct->monHoc->ma_mon ?? 'N/A'); ?></td>
                                                <td><?php echo e($ct->monHoc->ten_mon ?? 'N/A'); ?></td>
                                                <td><?php echo e($ct->so_tin_chi); ?></td>
                                                <td><?php echo e(number_format($ct->don_gia_tin_chi, 0, ',', '.')); ?> đ</td>
                                                <td><?php echo e(number_format($ct->thanh_tien, 0, ',', '.')); ?> đ</td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Chưa có chi tiết học phí</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

<!-- 
                <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Thông tin sinh viên</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>MSSV:</strong></td>
                                    <td><?php echo e($hocPhi->sinhVien->ma_sinh_vien); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td><?php echo e($hocPhi->sinhVien->ho_ten); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ:</strong></td>
                                    <td><?php echo e($hocPhi->hocKy->ten_hoc_ky); ?> - <?php echo e($hocPhi->hocKy->nam_hoc); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Hạn đóng:</strong></td>
                                    <td><span class="badge bg-warning"><?php echo e($hocPhi->han_dong->format('d/m/Y')); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <?php if($hocPhi->trang_thai == 'da_nop_du'): ?>
                                            <span class="badge bg-success">Đã nộp đủ</span>
                                        <?php elseif($hocPhi->trang_thai == 'qua_han'): ?>
                                            <span class="badge bg-danger">Quá hạn</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa nộp đủ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div> -->

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Tổng hợp</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tổng học phí:</strong></td>
                                    <td class="text-end"><strong><?php echo e(number_format($hocPhi->tong_so_tien, 0, ',', '.')); ?> đ</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Đã đóng:</strong></td>
                                    <td class="text-end text-success"><strong><?php echo e(number_format($hocPhi->so_tien_da_dong, 0, ',', '.')); ?> đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td><strong>Còn lại:</strong></td>
                                    <td class="text-end text-danger"><h4><?php echo e(number_format($hocPhi->so_tien_con_lai, 0, ',', '.')); ?> đ</h4></td>
                                </tr>
                            </table>

                            <?php if($hocPhi->so_tien_con_lai > 0): ?>
                                <a href="<?php echo e(route('dao-tao.hoc-phi.payment', $hocPhi->id)); ?>" class="btn btn-success w-100 mt-3">
                                    <i class="bi bi-cash"></i> Ghi nhận thanh toán
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Lịch sử đóng học phí</h4>
                        </div>
                        <div class="card-body">
                            <?php $__empty_1 = true; $__currentLoopData = $hocPhi->lichSuDongHocPhi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="mb-3 pb-3" style="border-bottom: 1px solid #ddd;">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success"><?php echo e(number_format($ls->so_tien, 0, ',', '.')); ?> đ</strong>
                                        <small class="text-muted"><?php echo e($ls->ngay_dong->format('d/m/Y')); ?></small>
                                    </div>
                                    <small><?php echo e($ls->phuong_thuc_thanh_toan); ?></small>
                                    <?php if($ls->ghi_chu): ?>
                                        <p class="mb-0 small text-muted"><?php echo e($ls->ghi_chu); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted">Chưa có lịch sử đóng học phí</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <?php if(session('show_timetable') && session('sinh_vien_id') && session('hoc_ky_id')): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-calendar-check"></i> Thời khóa biểu của sinh viên
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill"></i> 
                                    <strong>Thanh toán thành công!</strong> Dưới đây là thời khóa biểu của sinh viên.
                                </div>
                                
                                <?php
                                    $sinhVienId = session('sinh_vien_id');
                                    $hocKyId = session('hoc_ky_id');
                                    
                                    // Lấy thời khóa biểu của sinh viên
                                    $lopHocPhanSinhViens = \App\Models\LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                                        ->whereHas('lopHocPhan', function($q) use ($hocKyId) {
                                            $q->where('hoc_ky_id', $hocKyId);
                                        })
                                        ->with(['lopHocPhan.monHoc', 'lopHocPhan.lichHocCoDinhs'])
                                        ->get();
                                    
                                    // Tạo lịch theo thứ
                                    $lichTheoThu = [];
                                    foreach ($lopHocPhanSinhViens as $lhpSv) {
                                        foreach ($lhpSv->lopHocPhan->lichHocCoDinhs as $lich) {
                                            $thu = $lich->thu_trong_tuan;
                                            if (!isset($lichTheoThu[$thu])) {
                                                $lichTheoThu[$thu] = [];
                                            }
                                            $lichTheoThu[$thu][] = [
                                                'mon_hoc' => $lhpSv->lopHocPhan->monHoc,
                                                'lop_hp' => $lhpSv->lopHocPhan,
                                                'lich' => $lich
                                            ];
                                        }
                                    }
                                    ksort($lichTheoThu);
                                    
                                    $thuTrongTuan = [
                                        2 => 'Thứ 2',
                                        3 => 'Thứ 3',
                                        4 => 'Thứ 4',
                                        5 => 'Thứ 5',
                                        6 => 'Thứ 6',
                                        7 => 'Thứ 7',
                                        8 => 'Chủ nhật'
                                    ];
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Thứ</th>
                                                <th>Môn học</th>
                                                <th>Mã lớp</th>
                                                <th>Tiết</th>
                                                <th>Phòng</th>
                                                <th>Ghi chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $lichTheoThu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thu => $cacLich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php $__currentLoopData = $cacLich; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <?php if($index === 0): ?>
                                                            <td rowspan="<?php echo e(count($cacLich)); ?>" class="align-middle">
                                                                <strong><?php echo e($thuTrongTuan[$thu] ?? 'N/A'); ?></strong>
                                                            </td>
                                                        <?php endif; ?>
                                                        <td>
                                                            <strong><?php echo e($item['mon_hoc']->ten_mon); ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo e($item['mon_hoc']->ma_mon); ?></small>
                                                        </td>
                                                        <td><?php echo e($item['lop_hp']->ma_lop_hp); ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                Tiết <?php echo e($item['lich']->tiet_bat_dau); ?> - <?php echo e($item['lich']->tiet_ket_thuc); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if($item['lich']->phongHoc): ?>
                                                                <?php echo e($item['lich']->phongHoc->ten_phong); ?>

                                                            <?php else: ?>
                                                                <span class="text-muted">Chưa xếp phòng</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($item['lich']->ghi_chu ?? '-'); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        Chưa có lịch học
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-info-circle"></i> 
                                        Sinh viên có thể xem thời khóa biểu chi tiết tại trang cá nhân của mình.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-phi/show.blade.php ENDPATH**/ ?>