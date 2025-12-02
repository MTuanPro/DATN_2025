<?php $__env->startSection('title', 'Danh sách Lịch thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Lịch thi</h3>
                <p class="text-subtitle text-muted">Danh sách lịch thi các lớp học phần</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('dao-tao.lich-thi.index')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($hocKy->id); ?>" <?php echo e(request('hoc_ky_id') == $hocKy->id ? 'selected' : ''); ?>>
                                            <?php echo e($hocKy->ten_hoc_ky); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Loại thi</label>
                                <select name="loai_thi" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="giua_ky" <?php echo e(request('loai_thi') == 'giua_ky' ? 'selected' : ''); ?>>Giữa kỳ</option>
                                    <option value="cuoi_ky" <?php echo e(request('loai_thi') == 'cuoi_ky' ? 'selected' : ''); ?>>Cuối kỳ</option>
                                    <option value="thi_lai" <?php echo e(request('loai_thi') == 'thi_lai' ? 'selected' : ''); ?>>Thi lại</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Từ ngày</label>
                                <input type="date" name="ngay_thi_from" class="form-control" value="<?php echo e(request('ngay_thi_from')); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Đến ngày</label>
                                <input type="date" name="ngay_thi_to" class="form-control" value="<?php echo e(request('ngay_thi_to')); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tìm kiếm môn học</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã môn, tên môn..." value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="<?php echo e(route('dao-tao.lich-thi.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                            <div class="float-end d-flex gap-2">
                                <a href="<?php echo e(route('dao-tao.lich-thi.show-import-form')); ?>" class="btn btn-info text-white">
                                    <i class="bi bi-upload"></i> Import Excel
                                </a>
                                <a href="<?php echo e(route('dao-tao.lich-thi.create')); ?>" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Thêm lịch thi
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã lớp HP</th>
                                <th>Môn học</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ thi</th>
                                <th>Phòng</th>
                                <th>SL dự thi</th>
                                <th>Giám thị</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $lichThis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lichThi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($lichThis->firstItem() + $index); ?></td>
                                <td><strong><?php echo e($lichThi->lopHocPhan->ma_lop_hp); ?></strong></td>
                                <td>
                                    <?php echo e($lichThi->lopHocPhan->monHoc->ten_mon); ?>

                                    <br><small class="text-muted"><?php echo e($lichThi->lopHocPhan->monHoc->ma_mon); ?></small>
                                </td>
                                <td>
                                    <?php if($lichThi->loai_thi == 'giua_ky'): ?>
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    <?php elseif($lichThi->loai_thi == 'cuoi_ky'): ?>
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Thi lại</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($lichThi->ngay_thi->format('d/m/Y')); ?></td>
                                <td><?php echo e($lichThi->gio_bat_dau); ?> - <?php echo e($lichThi->gio_ket_thuc); ?></td>
                                <td><?php echo e($lichThi->phongThi->ten_phong ?? 'Chưa phân phòng'); ?></td>
                                <td>
                                    <strong><?php echo e($lichThi->lopHocPhan->lopHocPhanSinhViens->count()); ?></strong>
                                    <?php if($lichThi->so_sinh_vien_du_thi && $lichThi->so_sinh_vien_du_thi != $lichThi->lopHocPhan->lopHocPhanSinhViens->count()): ?>
                                        <br><small class="text-muted">(Dự kiến: <?php echo e($lichThi->so_sinh_vien_du_thi); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($lichThi->giamThi1): ?>
                                        <small>1. <?php echo e($lichThi->giamThi1->ho_ten); ?></small><br>
                                    <?php endif; ?>
                                    <?php if($lichThi->giamThi2): ?>
                                        <small>2. <?php echo e($lichThi->giamThi2->ho_ten); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('dao-tao.lich-thi.show', $lichThi)); ?>" class="btn btn-sm btn-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('dao-tao.lich-thi.edit', $lichThi)); ?>" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('dao-tao.lich-thi.destroy', $lichThi)); ?>" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa lịch thi này?')" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    <i class="bi bi-inbox"></i> Không có dữ liệu lịch thi
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($lichThis->links()); ?>

                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-thi/index.blade.php ENDPATH**/ ?>