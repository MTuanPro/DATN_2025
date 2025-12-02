<?php $__env->startSection('title', 'Quản lý Khóa học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Khóa học</h3>
                    <p class="text-subtitle text-muted">Quản lý niên khóa đào tạo của trường</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Khóa học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <!-- Header Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="bi bi-calendar3 text-primary"></i> Danh sách Khóa học
                                    </h5>
                                    <p class="text-muted mb-0 small">
                                        Quản lý các niên khóa đào tạo và thời gian học tập
                                    </p>
                                </div>
                                <div class="mt-3 mt-md-0">
                                    <a href="<?php echo e(route('dao-tao.khoa-hoc.create')); ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Thêm Khóa học mới
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                        <i class="bi bi-calendar-range fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Tổng khóa học</h6>
                                    <h3 class="mb-0"><?php echo e($khoaHocs->total()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                                        <i class="bi bi-book fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Đang học</h6>
                                    <h3 class="mb-0"><?php echo e(\App\Models\DaoTao\KhoaHoc::where('trang_thai', 'dang_hoc')->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                        <i class="bi bi-mortarboard fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Đã tốt nghiệp</h6>
                                    <h3 class="mb-0"><?php echo e(\App\Models\DaoTao\KhoaHoc::where('trang_thai', 'da_tot_nghiep')->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                        <i class="bi bi-clock-history fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Năm hiện tại</h6>
                                    <h3 class="mb-0"><?php echo e(date('Y')); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel text-primary"></i> Tìm kiếm & Lọc
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.khoa-hoc.index')); ?>" method="GET" class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small">Tên khóa học</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="keyword" class="form-control" value="<?php echo e(request('keyword')); ?>"
                    placeholder="Tìm theo tên khóa học...">
            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small">Trạng thái</label>
                <select name="trang_thai" class="form-select">
                                <option value="">-- Tất cả --</option>
                    <option value="dang_hoc" <?php echo e(request('trang_thai') == 'dang_hoc' ? 'selected' : ''); ?>>Đang học</option>
                                <option value="da_tot_nghiep" <?php echo e(request('trang_thai') == 'da_tot_nghiep' ? 'selected' : ''); ?>>Đã tốt nghiệp</option>
                </select>
            </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small">Sắp xếp theo</label>
                <select name="sort" class="form-select">
                                <option value="nam_bat_dau" <?php echo e(request('sort') == 'nam_bat_dau' ? 'selected' : ''); ?>>Năm bắt đầu</option>
                                <option value="ten_khoa_hoc" <?php echo e(request('sort') == 'ten_khoa_hoc' ? 'selected' : ''); ?>>Tên khóa học</option>
                </select>
            </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small">Thứ tự</label>
                <select name="direction" class="form-select">
                                <option value="desc" <?php echo e(request('direction') == 'desc' ? 'selected' : ''); ?>>Giảm dần</option>
                    <option value="asc" <?php echo e(request('direction') == 'asc' ? 'selected' : ''); ?>>Tăng dần</option>
                </select>
            </div>
                        <div class="col-lg-3 col-md-12">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                                <a href="<?php echo e(route('dao-tao.khoa-hoc.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-list-ul text-primary"></i> Danh sách Khóa học
                        <span class="badge bg-primary ms-2"><?php echo e($khoaHocs->total()); ?> khóa</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $khoaHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border-bottom <?php echo e($index === 0 ? 'border-top' : ''); ?>">
                            <div class="p-4 hover-bg-light">
                                <div class="row align-items-center">
                                    <!-- Khóa học name & Status -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                                <i class="bi bi-calendar3 text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold"><?php echo e($kh->ten_khoa_hoc); ?></h6>
                            <?php if($kh->trang_thai == 'dang_hoc'): ?>
                                                    <span class="badge bg-success px-2 py-1">
                                                        <i class="bi bi-check-circle"></i> Đang học
                                                    </span>
                            <?php else: ?>
                                                    <span class="badge bg-secondary px-2 py-1">
                                                        <i class="bi bi-mortarboard"></i> Đã tốt nghiệp
                                                    </span>
                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Thời gian -->
                                    <div class="col-lg-4 col-md-6 mt-3 mt-md-0">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="text-center border rounded-3 p-2">
                                                    <small class="text-muted d-block">Năm bắt đầu</small>
                                                    <h6 class="mb-0 text-primary"><?php echo e($kh->nam_bat_dau); ?></h6>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center border rounded-3 p-2">
                                                    <small class="text-muted d-block">Năm kết thúc</small>
                                                    <h6 class="mb-0 text-success"><?php echo e($kh->nam_ket_thuc); ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2">
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                <i class="bi bi-hourglass-split"></i> <?php echo e($kh->so_nam_dao_tao); ?> năm đào tạo
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-lg-4 col-md-12 mt-3 mt-lg-0 text-lg-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('dao-tao.khoa-hoc.edit', $kh->id)); ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil-square"></i> Sửa
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo e($kh->id); ?>)"
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i> Xóa
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-<?php echo e($kh->id); ?>" 
                                              action="<?php echo e(route('dao-tao.khoa-hoc.destroy', $kh->id)); ?>" 
                                              method="POST" 
                                              style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">Chưa có khóa học nào</h5>
                            <p class="text-muted mb-4">Bắt đầu bằng cách thêm khóa học mới</p>
                            <a href="<?php echo e(route('dao-tao.khoa-hoc.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Khóa học đầu tiên
                            </a>
                        </div>
                <?php endif; ?>
                </div>

                <?php if($khoaHocs->hasPages()): ?>
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Hiển thị <?php echo e($khoaHocs->firstItem()); ?> - <?php echo e($khoaHocs->lastItem()); ?> 
                                trong tổng số <?php echo e($khoaHocs->total()); ?> khóa học
                            </div>
                            <div>
            <?php echo e($khoaHocs->appends(request()->query())->links()); ?>

        </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .card {
        border-radius: 10px;
    }
    
    .badge {
        font-weight: 500;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('⚠️ Bạn có chắc chắn muốn xóa khóa học này?\n\nLưu ý: Việc xóa khóa học có thể ảnh hưởng đến các dữ liệu liên quan như lớp học, sinh viên...')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/khoa-hoc/index.blade.php ENDPATH**/ ?>