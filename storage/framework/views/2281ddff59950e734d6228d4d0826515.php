<?php $__env->startSection('title', 'Quản lý Lớp học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Lớp học phần</h3>
                    <p class="text-subtitle text-muted">Danh sách các lớp học phần trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lớp học phần</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Danh sách Lớp học phần</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="button" id="btnXoaChon" class="btn btn-danger btn-sm shadow-sm" style="display: none;" onclick="xoaNhieuLopHocPhan()">
                                    <i class="bi bi-trash"></i> Xóa đã chọn
                                </button>
                                <form method="POST" action="<?php echo e(route('dao-tao.lop-hoc-phan.sync-so-luong')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-warning btn-sm shadow-sm" onclick="return confirm('Bạn có chắc muốn đồng bộ lại số lượng đăng ký?')">
                                        <i class="bi bi-arrow-repeat"></i> Đồng bộ sĩ số
                                    </button>
                                </form>
                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.show-import-form')); ?>" class="btn btn-success btn-sm shadow-sm text-white">
                                    <i class="bi bi-upload"></i> Import Excel
                                </a>
                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.create')); ?>" class="btn btn-primary btn-sm shadow-sm text-white">
                                    <i class="bi bi-plus-circle"></i> Thêm lớp học phần
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả học kỳ --</option>
                                    <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($hocKy->id); ?>"
                                            <?php echo e(request('hoc_ky_id') == $hocKy->id ? 'selected' : ''); ?>>
                                            <?php echo e($hocKy->ten_hoc_ky); ?> - <?php echo e($hocKy->nam_hoc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Môn học</label>
                                <select name="mon_hoc_id" class="form-select">
                                    <option value="">-- Tất cả môn học --</option>
                                    <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($monHoc->id); ?>"
                                            <?php echo e(request('mon_hoc_id') == $monHoc->id ? 'selected' : ''); ?>>
                                            <?php echo e($monHoc->ma_mon); ?> - <?php echo e($monHoc->ten_mon); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã hoặc tên lớp..."
                                    value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                    <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Messages -->
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th style="text-align: center;">Mã lớp HP</th>
                                    <th style="text-align: center;">Tên lớp HP</th>
                                    <th style="text-align: center;">Môn học</th>
                                    <th style="text-align: center;">Học kỳ</th>
                                    <th style="text-align: center;">Giảng viên</th>
                                    <th style="text-align: center;">Sĩ số</th>
                                    <th style="text-align: center;">Hình thức</th>
                                    <th style="text-align: center;">Trạng thái</th>
                                    <th style="text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-lop-hoc-phan" value="<?php echo e($lhp->id); ?>" onchange="toggleDeleteButton()">
                                        </td>
                                        <td><?php echo e($lopHocPhans->firstItem() + $index); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.show', $lhp->id)); ?>" 
                                               class="text-decoration-none fw-bold text-primary fs-6"
                                               style="font-weight: 700 !important; color: #0d6efd !important;">
                                                <?php echo e($lhp->ma_lop_hp); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.show', $lhp->id)); ?>" 
                                               class="text-decoration-none fw-bold text-dark fs-6"
                                               style="font-weight: 700 !important; color: #212529 !important;">
                                                <?php echo e($lhp->ten_lop_hp); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($lhp->monHoc->ten_mon ?? 'N/A'); ?></td>
                                        <td><?php echo e($lhp->hocKy->ten_hoc_ky ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($lhp->lopHocPhanGiangVien->isNotEmpty()): ?>
                                                <?php $__currentLoopData = $lhp->lopHocPhanGiangVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phanCong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($phanCong->giangVien): ?>
                                                        <div class="mb-1">
                                                            <span class="badge 
                                                                <?php if($phanCong->vai_tro == 'giang_vien_chinh'): ?> bg-primary
                                                                <?php elseif($phanCong->vai_tro == 'giang_vien_phu'): ?> bg-info
                                                                <?php else: ?> bg-secondary
                                                                <?php endif; ?>">
                                                                <?php echo e($phanCong->giangVien->ho_ten); ?>

                                                                <?php if($phanCong->vai_tro == 'giang_vien_chinh'): ?>
                                                                    <small>(Chính)</small>
                                                                <?php elseif($phanCong->vai_tro == 'giang_vien_phu'): ?>
                                                                    <small>(Phụ)</small>
                                                                <?php else: ?>
                                                                    <small>(Trợ giảng)</small>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <span class="text-muted"><small>Chưa phân công</small></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e($lhp->so_luong_thuc_te ?? $lhp->so_luong_dang_ky); ?>/<?php echo e($lhp->suc_chua); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($lhp->hinh_thuc == 'offline'): ?>
                                                <span class="badge bg-secondary">Offline</span>
                                            <?php elseif($lhp->hinh_thuc == 'online'): ?>
                                                <span class="badge bg-primary">Online</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Hybrid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($lhp->trang_thai_lop == 'mo_dang_ky'): ?>
                                                <span class="badge bg-success">Mở đăng ký</span>
                                            <?php elseif($lhp->trang_thai_lop == 'dang_hoc'): ?>
                                                <span class="badge bg-primary">Đang học</span>
                                            <?php elseif($lhp->trang_thai_lop == 'ket_thuc'): ?>
                                                <span class="badge bg-secondary">Kết thúc</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh', $lhp->id)); ?>"
                                                    class="btn btn-sm btn-success" title="Lịch cố định (TKB)">
                                                    <i class="bi bi-calendar-week"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lhp->id)); ?>"
                                                    class="btn btn-sm btn-info" title="Lịch chi tiết">
                                                    <i class="bi bi-calendar-check"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.phan-cong', $lhp->id)); ?>"
                                                    class="btn btn-sm btn-secondary" title="Phân công GV">
                                                    <i class="bi bi-person-badge"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.edit', $lhp->id)); ?>"
                                                    class="btn btn-sm btn-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?php echo e($lhp->id); ?>)" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-<?php echo e($lhp->id); ?>"
                                                action="<?php echo e(route('dao-tao.lop-hoc-phan.destroy', $lhp->id)); ?>"
                                                method="POST" style="display: none;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Chưa có lớp học phần nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($lopHocPhans->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Form ẩn để xóa nhiều -->
    <form id="formXoaNhieu" action="<?php echo e(route('dao-tao.lop-hoc-phan.destroy-multiple')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <input type="hidden" name="ids" id="idsToDelete">
    </form>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function confirmDelete(id) {
                if (confirm('Bạn có chắc chắn muốn xóa lớp học phần này?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }

            function toggleCheckAll() {
                const checkAll = document.getElementById('checkAll');
                const checkboxes = document.querySelectorAll('.checkbox-lop-hoc-phan');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
                toggleDeleteButton();
            }

            function toggleDeleteButton() {
                const checkboxes = document.querySelectorAll('.checkbox-lop-hoc-phan:checked');
                const btnXoaChon = document.getElementById('btnXoaChon');
                if (checkboxes.length > 0) {
                    btnXoaChon.style.display = 'inline-block';
                } else {
                    btnXoaChon.style.display = 'none';
                }
                // Cập nhật checkbox "Chọn tất cả"
                const allCheckboxes = document.querySelectorAll('.checkbox-lop-hoc-phan');
                const checkAll = document.getElementById('checkAll');
                if (allCheckboxes.length > 0) {
                    checkAll.checked = checkboxes.length === allCheckboxes.length;
                }
            }

            function xoaNhieuLopHocPhan() {
                const checkboxes = document.querySelectorAll('.checkbox-lop-hoc-phan:checked');
                if (checkboxes.length === 0) {
                    alert('Vui lòng chọn ít nhất một lớp học phần để xóa!');
                    return;
                }

                const ids = Array.from(checkboxes).map(cb => cb.value);
                const count = ids.length;

                if (!confirm(`Bạn có chắc chắn muốn xóa ${count} lớp học phần đã chọn? Hành động này không thể hoàn tác!`)) {
                    return;
                }

                document.getElementById('idsToDelete').value = ids.join(',');
                document.getElementById('formXoaNhieu').submit();
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lop-hoc-phan/index.blade.php ENDPATH**/ ?>