<?php $__env->startSection('title', 'Chi tiết Lịch thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Lịch thi</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lich-thi.index')); ?>">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Thông tin lịch thi</h4>
                <div>
                    <a href="<?php echo e(route('dao-tao.lich-thi.phan-phong', $lichThi)); ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-door-open"></i> Phân phòng thi
                    </a>
                    <a href="<?php echo e(route('dao-tao.lich-thi.edit', $lichThi)); ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <form action="<?php echo e(route('dao-tao.lich-thi.destroy', $lichThi)); ?>" method="POST" class="d-inline" 
                          onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Lớp học phần:</th>
                                <td><strong><?php echo e($lichThi->lopHocPhan->ma_lop_hp); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td><?php echo e($lichThi->lopHocPhan->monHoc->ten_mon); ?></td>
                            </tr>
                            <tr>
                                <th>Mã môn:</th>
                                <td><?php echo e($lichThi->lopHocPhan->monHoc->ma_mon); ?></td>
                            </tr>
                            <tr>
                                <th>Học kỳ:</th>
                                <td><?php echo e($lichThi->hocKy->ten_hoc_ky); ?></td>
                            </tr>
                            <tr>
                                <th>Loại thi:</th>
                                <td>
                                    <?php if($lichThi->loai_thi == 'giua_ky'): ?>
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    <?php elseif($lichThi->loai_thi == 'cuoi_ky'): ?>
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Thi lại</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Ngày thi:</th>
                                <td><strong><?php echo e($lichThi->ngay_thi->format('d/m/Y')); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Giờ thi:</th>
                                <td>
                                    <?php if($lichThi->caHoc): ?>
                                        <strong><?php echo e($lichThi->caHoc->ten_ca); ?></strong><br>
                                        <small class="text-muted"><?php echo e($lichThi->gio_bat_dau); ?> - <?php echo e($lichThi->gio_ket_thuc); ?></small>
                                    <?php else: ?>
                                        <?php echo e($lichThi->gio_bat_dau); ?> - <?php echo e($lichThi->gio_ket_thuc); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Phòng thi:</th>
                                <td><?php echo e($lichThi->phongThi->ten_phong ?? 'Chưa phân phòng'); ?></td>
                            </tr>
                            <tr>
                                <th>Số SV dự thi:</th>
                                <td>
                                    <strong><?php echo e($lichThi->lopHocPhan->lopHocPhanSinhViens->count()); ?> sinh viên</strong>
                                    <?php if($lichThi->so_sinh_vien_du_thi && $lichThi->so_sinh_vien_du_thi != $lichThi->lopHocPhan->lopHocPhanSinhViens->count()): ?>
                                        <br><small class="text-muted">(Dự kiến: <?php echo e($lichThi->so_sinh_vien_du_thi); ?>)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Hình thức thi:</th>
                                <td>
                                    <?php if($lichThi->hinh_thuc == 'offline'): ?>
                                        <span class="badge bg-secondary">Thi tại trường</span>
                                    <?php elseif($lichThi->hinh_thuc == 'online'): ?>
                                        <span class="badge bg-primary">Thi trực tuyến</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Kết hợp</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if($lichThi->link_online && $lichThi->hinh_thuc != 'offline'): ?>
                            <tr>
                                <th>Link thi online:</th>
                                <td><a href="<?php echo e($lichThi->link_online); ?>" target="_blank"><?php echo e($lichThi->link_online); ?></a></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <hr>

                <h5>Giám thị</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Giám thị 1:</strong> <?php echo e($lichThi->giamThi1->ho_ten ?? 'Chưa phân công'); ?>

                    </div>
                    <div class="col-md-6">
                        <strong>Giám thị 2:</strong> <?php echo e($lichThi->giamThi2->ho_ten ?? 'Chưa phân công'); ?>

                    </div>
                </div>

                <hr>

                <h5>Tài liệu</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Đề thi:</strong> 
                        <?php if($lichThi->de_thi_file): ?>
                            <div class="mt-2">
                                <a href="<?php echo e(route('dao-tao.lich-thi.download-de-thi', $lichThi)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Tải xuống
                                </a>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-file-earmark-pdf"></i> <?php echo e(basename($lichThi->de_thi_file)); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">Chưa upload</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Đáp án:</strong> 
                        <?php if($lichThi->dap_an_file): ?>
                            <div class="mt-2">
                                <a href="<?php echo e(route('dao-tao.lich-thi.download-dap-an', $lichThi)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Tải xuống
                                </a>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-file-earmark-pdf"></i> <?php echo e(basename($lichThi->dap_an_file)); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">Chưa upload</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($lichThi->ghi_chu): ?>
                <hr>
                <h5>Ghi chú</h5>
                <p><?php echo e($lichThi->ghi_chu); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Điều kiện đi thi -->
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> Điều kiện đi thi:</h5>
            <ul class="mb-0">
                <li>Tỷ lệ có mặt phải đạt tối thiểu <strong>75%</strong> (không vắng quá 25% số buổi học)</li>
                <li>Điểm trung bình các đầu điểm phải đạt tối thiểu <strong>5.0 điểm</strong></li>
            </ul>
            <p class="mb-0 mt-2"><strong>Lưu ý:</strong> Sinh viên không đạt một trong hai điều kiện trên sẽ <span class="text-danger"><strong>KHÔNG ĐƯỢC ĐI THI</strong></span>.</p>
        </div>

        <!-- Danh sách sinh viên dự thi -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Danh sách sinh viên dự thi (<?php echo e(count($danhSachSinhVienDiThi)); ?> sinh viên)</h5>
            </div>
            <div class="card-body">
                <?php if(empty($danhSachSinhVienDiThi)): ?>
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có sinh viên nào trong lớp học phần.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Lớp</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Điều kiện</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $danhSachSinhVienDiThi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $lhpsv = $item['lop_hoc_phan_sinh_vien'];
                                ?>
                                <tr class="<?php echo e($item['khong_duoc_di_thi'] ? 'table-danger' : ''); ?>">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->lopHanhChinh->ten_lop ?? 'N/A'); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->email); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->so_dien_thoai); ?></td>
                                    <td>
                                        <?php if($item['khong_duoc_di_thi']): ?>
                                            <span class="badge bg-danger" title="<?php echo e($item['ly_do']); ?>">
                                                <i class="bi bi-x-circle"></i> Không đủ điều kiện
                                            </span>
                                            <br>
                                            <small class="text-danger"><?php echo e($item['ly_do']); ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Đủ điều kiện
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                Chuyên cần: <?php echo e($item['ty_le_co_mat']); ?>%
                                                <?php if($item['diem_trung_binh'] !== null): ?>
                                                    | Điểm: <?php echo e(number_format($item['diem_trung_binh'], 2)); ?>

                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Thống kê -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Được đi thi</h5>
                                    <h2 class="mb-0"><?php echo e(count(array_filter($danhSachSinhVienDiThi, fn($sv) => !$sv['khong_duoc_di_thi']))); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Không được đi thi</h5>
                                    <h2 class="mb-0"><?php echo e(count(array_filter($danhSachSinhVienDiThi, fn($sv) => $sv['khong_duoc_di_thi']))); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng số</h5>
                                    <h2 class="mb-0"><?php echo e(count($danhSachSinhVienDiThi)); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-thi/show.blade.php ENDPATH**/ ?>