

<?php $__env->startSection('title', 'Chi tiết Lớp học phần - ' . $lopHocPhan->ma_lop_hp); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Lớp học phần</h3>
                    <p class="text-subtitle text-muted">
                        <?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->ten_lop_hp); ?>

                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Thông tin lớp học phần
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã lớp HP:</strong> <?php echo e($lopHocPhan->ma_lop_hp); ?></p>
                            <p><strong>Tên lớp HP:</strong> <?php echo e($lopHocPhan->ten_lop_hp); ?></p>
                            <p><strong>Môn học:</strong> <?php echo e($lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></p>
                            <p><strong>Học kỳ:</strong> <?php echo e($lopHocPhan->hocKy->ten_hoc_ky ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sĩ số:</strong> 
                                <span class="badge bg-info">
                                    <?php echo e($lopHocPhan->so_luong_thuc_te ?? $lopHocPhan->so_luong_dang_ky); ?>/<?php echo e($lopHocPhan->suc_chua); ?>

                                </span>
                            </p>
                            <p><strong>Hình thức:</strong> 
                                <?php if($lopHocPhan->hinh_thuc == 'offline'): ?>
                                    <span class="badge bg-secondary">Offline</span>
                                <?php elseif($lopHocPhan->hinh_thuc == 'online'): ?>
                                    <span class="badge bg-primary">Online</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Hybrid</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                <?php if($lopHocPhan->trang_thai_lop == 'mo_dang_ky'): ?>
                                    <span class="badge bg-success">Mở đăng ký</span>
                                <?php elseif($lopHocPhan->trang_thai_lop == 'dang_hoc'): ?>
                                    <span class="badge bg-primary">Đang học</span>
                                <?php elseif($lopHocPhan->trang_thai_lop == 'ket_thuc'): ?>
                                    <span class="badge bg-secondary">Kết thúc</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hủy</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> Lịch theo Phòng học
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($lichTheoPhong->count() > 0): ?>
                    <?php $__currentLoopData = $lichTheoPhong; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phongGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-door-open"></i> Phòng: <strong><?php echo e($phongGroup['phong']); ?></strong>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày học</th>
                                            <th>Thứ</th>
                                            <th>Ca</th>
                                            <th>Giờ</th>
                                            <th>Tiết</th>
                                            <th>Giảng viên</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $phongGroup['lich_hocs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y')); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($lich->caHoc): ?>
                                                        <span class="badge bg-primary"><?php echo e($lich->caHoc->ten_ca); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> -
                                                    <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        Tiết <?php echo e($lich->tiet_bat_dau); ?>

                                                        <?php if($lich->tiet_ket_thuc != $lich->tiet_bat_dau): ?>
                                                            - <?php echo e($lich->tiet_ket_thuc); ?>

                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo e($lich->giangVien->ho_ten ?? 'Chưa phân công'); ?></td>
                                                <td>
                                                    <?php if($lich->trang_thai == 'chua_day'): ?>
                                                        <span class="badge bg-secondary">Chưa dạy</span>
                                                    <?php elseif($lich->trang_thai == 'dang_day'): ?>
                                                        <span class="badge bg-info">Đang dạy</span>
                                                    <?php elseif($lich->trang_thai == 'da_day'): ?>
                                                        <span class="badge bg-success">Đã dạy</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Hủy</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form
                                                        action="<?php echo e(route('dao-tao.lich-chi-tiet.destroy', $lich->id)); ?>"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa buổi học này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Chưa có lịch học theo phòng</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge"></i> Lịch theo Giảng viên
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($lichTheoGiangVien->count() > 0): ?>
                    <?php $__currentLoopData = $lichTheoGiangVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gvGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="bi bi-person"></i> Giảng viên: <strong><?php echo e($gvGroup['giang_vien']); ?></strong>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày học</th>
                                            <th>Thứ</th>
                                            <th>Ca</th>
                                            <th>Giờ</th>
                                            <th>Tiết</th>
                                            <th>Phòng</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $gvGroup['lich_hocs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y')); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($lich->caHoc): ?>
                                                        <span class="badge bg-primary"><?php echo e($lich->caHoc->ten_ca); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> -
                                                    <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        Tiết <?php echo e($lich->tiet_bat_dau); ?>

                                                        <?php if($lich->tiet_ket_thuc != $lich->tiet_bat_dau): ?>
                                                            - <?php echo e($lich->tiet_ket_thuc); ?>

                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo e($lich->phongHoc->ten_phong ?? 'Chưa phân phòng'); ?></td>
                                                <td>
                                                    <?php if($lich->trang_thai == 'chua_day'): ?>
                                                        <span class="badge bg-secondary">Chưa dạy</span>
                                                    <?php elseif($lich->trang_thai == 'dang_day'): ?>
                                                        <span class="badge bg-info">Đang dạy</span>
                                                    <?php elseif($lich->trang_thai == 'da_day'): ?>
                                                        <span class="badge bg-success">Đã dạy</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Hủy</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form
                                                        action="<?php echo e(route('dao-tao.lich-chi-tiet.destroy', $lich->id)); ?>"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa buổi học này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Chưa có lịch học theo giảng viên</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lop-hoc-phan/show.blade.php ENDPATH**/ ?>