<?php $__env->startSection('title', 'Quản lý Thông báo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        <i class="bi bi-megaphone-fill text-success"></i> Quản lý Thông báo Học vụ
                    </h3>
                    <p class="text-subtitle text-muted">
                        <span class="badge bg-light-success">Đào tạo</span>
                        Danh sách thông báo của bạn và thông báo tự động
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thông báo học vụ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thống kê nhanh -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light-success">
                        <div class="card-body text-center">
                            <h5 class="text-success mb-0"><?php echo e($thongBaos->total()); ?></h5>
                            <small class="text-muted">Tổng thông báo</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-info">
                        <div class="card-body text-center">
                            <h5 class="text-info mb-0">
                                <?php echo e($thongBaos->where('loai_nguon', 'thu_cong')->count()); ?>

                            </h5>
                            <small class="text-muted">Bạn tạo</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-primary">
                        <div class="card-body text-center">
                            <h5 class="text-primary mb-0">
                                <?php echo e($thongBaos->where('loai_nguon', 'tu_dong')->count()); ?>

                            </h5>
                            <small class="text-muted">Tự động</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-warning">
                        <div class="card-body text-center">
                            <h5 class="text-warning mb-0">
                                <?php echo e($thongBaos->where('ghim_dau_trang', true)->count()); ?>

                            </h5>
                            <small class="text-muted">Đã ghim</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="card border-success mb-4">
                <div class="card-header bg-light-success">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel-fill"></i> Bộ lọc thông báo
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.thong-bao.index')); ?>" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Loại thông báo</label>
                                    <select name="loai_thong_bao" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="tin_tuc"
                                            <?php echo e(request('loai_thong_bao') == 'tin_tuc' ? 'selected' : ''); ?>>Tin tức</option>
                                        <option value="thong_bao_chung"
                                            <?php echo e(request('loai_thong_bao') == 'thong_bao_chung' ? 'selected' : ''); ?>>Thông báo
                                            chung</option>
                                        <option value="tin_gap"
                                            <?php echo e(request('loai_thong_bao') == 'tin_gap' ? 'selected' : ''); ?>>Tin gấp</option>
                                        <option value="lich_hoc"
                                            <?php echo e(request('loai_thong_bao') == 'lich_hoc' ? 'selected' : ''); ?>>Lịch học
                                        </option>
                                        <option value="lich_thi"
                                            <?php echo e(request('loai_thong_bao') == 'lich_thi' ? 'selected' : ''); ?>>Lịch thi
                                        </option>
                                        <option value="hoc_phi"
                                            <?php echo e(request('loai_thong_bao') == 'hoc_phi' ? 'selected' : ''); ?>>Học phí</option>
                                        <option value="diem" <?php echo e(request('loai_thong_bao') == 'diem' ? 'selected' : ''); ?>>
                                            Điểm</option>
                                        <option value="dang_ky_mon"
                                            <?php echo e(request('loai_thong_bao') == 'dang_ky_mon' ? 'selected' : ''); ?>>Đăng ký môn
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Mức độ</label>
                                    <select name="muc_do" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="rat_quan_trong"
                                            <?php echo e(request('muc_do') == 'rat_quan_trong' ? 'selected' : ''); ?>>Rất quan trọng
                                        </option>
                                        <option value="quan_trong"
                                            <?php echo e(request('muc_do') == 'quan_trong' ? 'selected' : ''); ?>>Quan trọng</option>
                                        <option value="binh_thuong"
                                            <?php echo e(request('muc_do') == 'binh_thuong' ? 'selected' : ''); ?>>Bình thường</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Đối tượng</label>
                                    <select name="doi_tuong" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="all" <?php echo e(request('doi_tuong') == 'all' ? 'selected' : ''); ?>>Tất cả
                                        </option>
                                        <option value="sinh_vien"
                                            <?php echo e(request('doi_tuong') == 'sinh_vien' ? 'selected' : ''); ?>>Sinh viên</option>
                                        <option value="giang_vien"
                                            <?php echo e(request('doi_tuong') == 'giang_vien' ? 'selected' : ''); ?>>Giảng viên
                                        </option>
                                        <option value="lop_hanh_chinh"
                                            <?php echo e(request('doi_tuong') == 'lop_hanh_chinh' ? 'selected' : ''); ?>>Lớp hành chính
                                        </option>
                                        <option value="lop_hoc_phan"
                                            <?php echo e(request('doi_tuong') == 'lop_hoc_phan' ? 'selected' : ''); ?>>Lớp học phần
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tiêu đề, nội dung..." value="<?php echo e(request('search')); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách thông báo -->
            <div class="card border-success">
                <div class="card-header bg-gradient-success d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white">
                        <i class="bi bi-list-ul"></i> Danh sách thông báo học vụ
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('dao-tao.mau-thong-bao.index')); ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-file-earmark-text"></i> Mẫu thông báo
                        </a>
                        <a href="<?php echo e(route('dao-tao.thong-bao.create')); ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus-circle-fill"></i> Tạo thông báo mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th style="width: 40%">Tiêu đề</th>
                                    <th>Loại</th>
                                    <th>Đối tượng</th>
                                    <th>Mức độ</th>
                                    <th>Người gửi</th>
                                    <th>Ngày gửi</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $thongBaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="<?php echo e($tb->ghim_dau_trang ? 'table-warning' : ''); ?>">
                                        <td><?php echo e($thongBaos->firstItem() + $index); ?></td>
                                        <td>
                                            <?php if($tb->ghim_dau_trang): ?>
                                                <i class="bi bi-pin-angle-fill text-warning" title="Đã ghim"></i>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('dao-tao.thong-bao.show', $tb->id)); ?>" class="fw-bold">
                                                <?php echo e($tb->tieu_de); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($tb->loai_badge); ?>">
                                                <?php echo e($tb->loai_label); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <?php if($tb->doi_tuong == 'all'): ?>
                                                    Tất cả
                                                <?php elseif($tb->doi_tuong == 'sinh_vien'): ?>
                                                    Sinh viên
                                                <?php elseif($tb->doi_tuong == 'giang_vien'): ?>
                                                    Giảng viên
                                                <?php elseif($tb->doi_tuong == 'lop_hanh_chinh'): ?>
                                                    Lớp HC
                                                <?php else: ?>
                                                    Lớp HP
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($tb->muc_do_badge); ?>">
                                                <?php if($tb->muc_do_quan_trong == 'rat_quan_trong'): ?>
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                <?php endif; ?>
                                                <?php echo e($tb->muc_do_label); ?>

                                            </span>
                                        </td>
                                        <td><small><?php echo e($tb->nguoiGui->name ?? 'Hệ thống'); ?></small></td>
                                        <td><small><?php echo e($tb->ngay_gui->format('d/m/Y H:i')); ?></small></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo e(route('dao-tao.thong-bao.show', $tb->id)); ?>"
                                                    class="btn btn-info" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.thong-bao.edit', $tb->id)); ?>"
                                                    class="btn btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.thong-bao.destroy', $tb->id)); ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Chưa có thông báo nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($thongBaos->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/thong-bao/index.blade.php ENDPATH**/ ?>