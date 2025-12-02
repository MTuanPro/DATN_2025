<?php $__env->startSection('title', 'Điều kiện tốt nghiệp'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Điều kiện tốt nghiệp</h3>
                    <p class="text-subtitle text-muted">Kiểm tra điều kiện tốt nghiệp của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.chuong-trinh-dao-tao.index')); ?>">Chương trình đào tạo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Điều kiện tốt nghiệp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin sinh viên -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Thông tin sinh viên</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Sinh viên:</strong></td>
                                    <td><?php echo e($sinhVien->ho_ten); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Mã sinh viên:</strong></td>
                                    <td><?php echo e($sinhVien->ma_sinh_vien); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Lớp:</strong></td>
                                    <td><?php echo e($sinhVien->lopHanhChinh->ten_lop); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Khoa:</strong></td>
                                    <td><?php echo e($chuyenNganh->nganh->khoa->ten_khoa); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Ngành:</strong></td>
                                    <td><?php echo e($chuyenNganh->nganh->ten_nganh); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Chuyên ngành:</strong></td>
                                    <td><?php echo e($chuyenNganh->ten_chuyen_nganh); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kết quả tổng quan -->
            <div class="card">
                <div class="card-header <?php echo e($dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bg-success' : 'bg-warning'); ?> text-white">
                    <h5 class="mb-0">
                        <i class="bi <?php echo e($dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bi-check-circle' : 'bi-exclamation-triangle'); ?> me-2"></i>
                        Kết quả tổng quan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h4 class="mb-3">
                                <?php if($dieuKienTotNghiep['tong_quat']['du_dieu_kien']): ?>
                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill"></i> Bạn đã đủ điều kiện tốt nghiệp!
                                    </span>
                                <?php else: ?>
                                    <span class="text-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Bạn chưa đủ điều kiện tốt nghiệp
                                    </span>
                                <?php endif; ?>
                            </h4>
                            <p class="lead">
                                Đã đạt <strong><?php echo e($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat']); ?>/<?php echo e($dieuKienTotNghiep['tong_quat']['tong_dieu_kien']); ?></strong> điều kiện
                            </p>
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar <?php echo e($dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bg-success' : 'bg-warning'); ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo e(($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] / $dieuKienTotNghiep['tong_quat']['tong_dieu_kien']) * 100); ?>%"
                                     aria-valuenow="<?php echo e($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat']); ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?php echo e($dieuKienTotNghiep['tong_quat']['tong_dieu_kien']); ?>">
                                    <?php echo e(number_format(($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] / $dieuKienTotNghiep['tong_quat']['tong_dieu_kien']) * 100, 1)); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết từng điều kiện -->
            <div class="row">
                <!-- Điều kiện 1: Tín chỉ tích lũy -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'border-success' : 'border-warning'); ?>">
                        <div class="card-header <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bg-success' : 'bg-warning'); ?> text-white">
                            <h5 class="mb-0">
                                <i class="bi <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle'); ?> me-2"></i>
                                1. Tín chỉ tích lũy
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4><?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['yeu_cau']); ?> TC</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Đã đạt:</p>
                                    <h4 class="<?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'text-success' : 'text-warning'); ?>">
                                        <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['da_dat']); ?> TC
                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bg-success' : 'bg-warning'); ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['phan_tram']); ?>%">
                                    <?php echo e(number_format($dieuKienTotNghiep['dieu_kien']['tin_chi']['phan_tram'], 1)); ?>%
                                </div>
                            </div>
                            <?php if(!$dieuKienTotNghiep['dieu_kien']['tin_chi']['dat']): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Còn thiếu: <strong><?php echo e($dieuKienTotNghiep['dieu_kien']['tin_chi']['con_thieu']); ?> tín chỉ</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 2: Môn bắt buộc -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'border-success' : 'border-warning'); ?>">
                        <div class="card-header <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bg-success' : 'bg-warning'); ?> text-white">
                            <h5 class="mb-0">
                                <i class="bi <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle'); ?> me-2"></i>
                                2. Môn học bắt buộc
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4><?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['yeu_cau']); ?> TC</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Đã đạt:</p>
                                    <h4 class="<?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'text-success' : 'text-warning'); ?>">
                                        <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['da_dat']); ?> TC
                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bg-success' : 'bg-warning'); ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['phan_tram']); ?>%">
                                    <?php echo e(number_format($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['phan_tram'], 1)); ?>%
                                </div>
                            </div>
                            <?php if(!$dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat']): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Còn thiếu: <strong><?php echo e($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['con_thieu']); ?> tín chỉ bắt buộc</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 3: Điểm trung bình -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 <?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'border-success' : 'border-warning'); ?>">
                        <div class="card-header <?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bg-success' : 'bg-warning'); ?> text-white">
                            <h5 class="mb-0">
                                <i class="bi <?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle'); ?> me-2"></i>
                                3. Điểm trung bình tích lũy
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4>≥ <?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['yeu_cau']); ?></h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Hiện tại:</p>
                                    <h4 class="<?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'text-success' : 'text-warning'); ?>">
                                        <?php echo e(number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['da_dat'], 2)); ?>

                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar <?php echo e($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bg-success' : 'bg-warning'); ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo e(min($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['phan_tram'], 100)); ?>%">
                                    <?php echo e(number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['phan_tram'], 1)); ?>%
                                </div>
                            </div>
                            <?php if(!$dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat']): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Cần cải thiện thêm: <strong><?php echo e(number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['con_thieu'], 2)); ?> điểm</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 4: Không có môn nợ -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 <?php echo e($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'border-success' : 'border-danger'); ?>">
                        <div class="card-header <?php echo e($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'bg-success' : 'bg-danger'); ?> text-white">
                            <h5 class="mb-0">
                                <i class="bi <?php echo e($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'bi-check-circle' : 'bi-x-circle'); ?> me-2"></i>
                                4. Không còn môn nợ (bắt buộc)
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat']): ?>
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Tuyệt vời!</strong> Bạn đã hoàn thành tất cả môn học bắt buộc.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Còn <strong><?php echo e($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['so_mon_no']); ?> môn</strong> chưa đạt hoặc chưa học
                                </div>
                                
                                <?php if(count($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach']) > 0): ?>
                                    <h6 class="mb-3">Danh sách môn cần hoàn thành:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã môn</th>
                                                    <th>Tên môn</th>
                                                    <th>TC</th>
                                                    <th>HK</th>
                                                    <th>Trạng thái</th>
                                                    <th>Điểm</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($loop->iteration); ?></td>
                                                        <td><strong><?php echo e($mon['ma_mon']); ?></strong></td>
                                                        <td><?php echo e($mon['ten_mon']); ?></td>
                                                        <td><?php echo e($mon['so_tin_chi']); ?></td>
                                                        <td><?php echo e($mon['hoc_ky_goi_y']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo e($mon['trang_thai'] == 'Chưa học' ? 'secondary' : 'warning'); ?>">
                                                                <?php echo e($mon['trang_thai']); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if($mon['diem']): ?>
                                                                <span class="text-danger"><?php echo e(number_format($mon['diem'], 1)); ?></span>
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
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ghi chú quan trọng</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Điều kiện tốt nghiệp này là <strong>tham khảo</strong>, điều kiện chính thức do phòng Đào tạo quyết định.</li>
                        <li>Điểm trung bình tích lũy yêu cầu tối thiểu: <strong>5.0/10</strong></li>
                        <li>Tất cả môn học bắt buộc phải <strong>đạt điểm ≥ 4.0</strong></li>
                        <li>Ngoài điều kiện học tập, sinh viên cần hoàn thành các yêu cầu về:
                            <ul>
                                <li>Học phí (không còn nợ)</li>
                                <li>Chứng chỉ ngoại ngữ, tin học (theo quy định)</li>
                                <li>Giáo dục thể chất, giáo dục quốc phòng</li>
                            </ul>
                        </li>
                        <li>Liên hệ phòng Đào tạo để biết thêm chi tiết: <strong>daotao@university.edu.vn</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="text-center mt-4">
                <a href="<?php echo e(route('sinh-vien.chuong-trinh-dao-tao.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại CTĐT
                </a>
                <a href="<?php echo e(route('sinh-vien.diem.bang-diem')); ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-2"></i>Xem bảng điểm
                </a>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/chuong-trinh-dao-tao/dieu-kien-tot-nghiep.blade.php ENDPATH**/ ?>