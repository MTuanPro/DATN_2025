<?php $__env->startSection('title', 'Tra cứu học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tra cứu học phần</h3>
                    <p class="text-subtitle text-muted">Tìm kiếm và xem thông tin các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tra cứu học phần</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('sinh-vien.tra-cuu.hoc-phan')); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Mã lớp HP, tên lớp, mã môn, tên môn...">
                        </div>
                        <div class="col-md-2">
                            <label for="hoc_ky_id" class="form-label">Học kỳ</label>
                            <select class="form-select" id="hoc_ky_id" name="hoc_ky_id">
                                <option value="">-- Tất cả --</option>
                                <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($hk->id); ?>" <?php echo e(request('hoc_ky_id') == $hk->id ? 'selected' : ''); ?>>
                                        <?php echo e($hk->ten_hoc_ky); ?> (<?php echo e($hk->nam_hoc); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="mon_hoc_id" class="form-label">Môn học</label>
                            <select class="form-select" id="mon_hoc_id" name="mon_hoc_id">
                                <option value="">-- Tất cả --</option>
                                <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($mh->id); ?>" <?php echo e(request('mon_hoc_id') == $mh->id ? 'selected' : ''); ?>>
                                        <?php echo e($mh->ten_mon); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="trang_thai_lop" class="form-label">Trạng thái</label>
                            <select class="form-select" id="trang_thai_lop" name="trang_thai_lop">
                                <option value="">-- Tất cả --</option>
                                <option value="mo_dang_ky" <?php echo e(request('trang_thai_lop') == 'mo_dang_ky' ? 'selected' : ''); ?>>Mở đăng ký</option>
                                <option value="dang_hoc" <?php echo e(request('trang_thai_lop') == 'dang_hoc' ? 'selected' : ''); ?>>Đang học</option>
                                <option value="ket_thuc" <?php echo e(request('trang_thai_lop') == 'ket_thuc' ? 'selected' : ''); ?>>Kết thúc</option>
                                <option value="huy" <?php echo e(request('trang_thai_lop') == 'huy' ? 'selected' : ''); ?>>Hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="hinh_thuc" class="form-label">Hình thức</label>
                            <select class="form-select" id="hinh_thuc" name="hinh_thuc">
                                <option value="">-- Tất cả --</option>
                                <option value="truc_tiep" <?php echo e(request('hinh_thuc') == 'truc_tiep' ? 'selected' : ''); ?>>Trực tiếp</option>
                                <option value="online" <?php echo e(request('hinh_thuc') == 'online' ? 'selected' : ''); ?>>Online</option>
                                <option value="blended" <?php echo e(request('hinh_thuc') == 'blended' ? 'selected' : ''); ?>>Kết hợp</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Tên lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Giảng viên</th>
                                    <th>Tín chỉ</th>
                                    <th>Số lượng</th>
                                    <th>Hình thức</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($lopHocPhans->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($lhp->ma_lop_hp); ?></strong></td>
                                        <td><?php echo e($lhp->ten_lop_hp); ?></td>
                                        <td>
                                            <strong><?php echo e($lhp->monHoc->ten_mon); ?></strong>
                                            <br><small class="text-muted"><?php echo e($lhp->monHoc->ma_mon); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($lhp->hocKy->ten_hoc_ky); ?>

                                            <br><small class="text-muted"><?php echo e($lhp->hocKy->nam_hoc); ?></small>
                                        </td>
                                        <td>
                                            <?php if($lhp->giangVienChinh && $lhp->giangVienChinh->giangVien): ?>
                                                <?php echo e($lhp->giangVienChinh->giangVien->ho_ten); ?>

                                            <?php else: ?>
                                                <span class="text-muted">Chưa phân công</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($lhp->monHoc->so_tin_chi); ?> TC</td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($lhp->so_luong_dang_ky); ?>/<?php echo e($lhp->suc_chua); ?></span>
                                        </td>
                                        <td>
                                            <?php if($lhp->hinh_thuc == 'truc_tiep'): ?>
                                                <span class="badge bg-primary">Trực tiếp</span>
                                            <?php elseif($lhp->hinh_thuc == 'online'): ?>
                                                <span class="badge bg-success">Online</span>
                                            <?php elseif($lhp->hinh_thuc == 'blended'): ?>
                                                <span class="badge bg-warning">Kết hợp</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($lhp->trang_thai_lop == 'mo_dang_ky' ? 'success' : ($lhp->trang_thai_lop == 'dang_hoc' ? 'primary' : ($lhp->trang_thai_lop == 'ket_thuc' ? 'info' : 'danger'))); ?>">
                                                <?php echo e($lhp->ten_trang_thai); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không tìm thấy lớp học phần nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị <?php echo e($lopHocPhans->firstItem() ?? 0); ?> - <?php echo e($lopHocPhans->lastItem() ?? 0); ?>

                                trong tổng số <?php echo e($lopHocPhans->total()); ?> lớp học phần
                            </small>
                        </div>
                        <div>
                            <?php echo e($lopHocPhans->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/tra-cuu/hoc-phan.blade.php ENDPATH**/ ?>