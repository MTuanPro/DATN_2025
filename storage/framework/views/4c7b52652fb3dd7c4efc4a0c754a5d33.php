<?php $__env->startSection('title', 'Danh sách Sinh viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Sinh viên</h3>
                    <p class="text-subtitle text-muted">Quản lý sinh viên - PHASE 3</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sinh viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filters & Actions -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="<?php echo e(route('dao-tao.sinh-vien.index')); ?>" method="GET" class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="MSSV, họ tên, email..." value="<?php echo e(request('search')); ?>">
                                </div>
                                <div class="col-md-2">
                                    <select name="khoa_hoc_id" class="form-select form-select-sm">
                                        <option value="">-- Khóa --</option>
                                        <?php $__currentLoopData = $khoaHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kh->id); ?>"
                                                <?php echo e(request('khoa_hoc_id') == $kh->id ? 'selected' : ''); ?>>
                                                <?php echo e($kh->ten_khoa_hoc); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="nganh_id" class="form-select form-select-sm">
                                        <option value="">-- Ngành --</option>
                                        <?php $__currentLoopData = $nganhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($n->id); ?>"
                                                <?php echo e(request('nganh_id') == $n->id ? 'selected' : ''); ?>>
                                                <?php echo e($n->ten_nganh); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="trang_thai_hoc_tap_id" class="form-select form-select-sm">
                                        <option value="">-- Trạng thái --</option>
                                        <?php $__currentLoopData = $trangThais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tt->id); ?>"
                                                <?php echo e(request('trang_thai_hoc_tap_id') == $tt->id ? 'selected' : ''); ?>>
                                                <?php echo e($tt->ten_trang_thai); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" id="btnXoaChon" class="btn btn-danger btn-sm" style="display: none;" onclick="xoaNhieuSinhVien()">
                                    <i class="bi bi-trash"></i> Xóa đã chọn
                                </button>
                                <a href="<?php echo e(route('dao-tao.sinh-vien.show-import-form')); ?>" class="btn btn-info btn-sm">
                                    <i class="bi bi-file-earmark-excel"></i> Import Excel
                                </a>
                                <a href="<?php echo e(route('dao-tao.sinh-vien.create')); ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle"></i> Thêm SV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng dữ liệu -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Ngành</th>
                                    <th>Chuyên ngành</th>
                                    <th>Kỳ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-sinh-vien" value="<?php echo e($sv->id); ?>" onchange="toggleDeleteButton()">
                                        </td>
                                        <td><?php echo e($sinhViens->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($sv->ma_sinh_vien); ?></strong></td>
                                        <td>
                                            <?php echo e($sv->ho_ten); ?>

                                            <br><small class="text-muted"><?php echo e($sv->email); ?></small>
                                        </td>
                                        <td>
                                            <?php if($sv->nganh): ?>
                                                <?php echo e($sv->nganh->ten_nganh); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($sv->chuyenNganh): ?>
                                                <?php echo e($sv->chuyenNganh->ten_chuyen_nganh); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-info">Kỳ <?php echo e($sv->ky_hien_tai); ?></span></td>
                                        <td>
                                            <?php if($sv->trangThaiHocTap): ?>
                                                <span
                                                    class="badge 
                                                    <?php if($sv->trangThaiHocTap->ma_trang_thai == 'DANG_HOC'): ?> bg-success
                                                    <?php elseif($sv->trangThaiHocTap->ma_trang_thai == 'BAO_LUU'): ?> bg-warning
                                                    <?php elseif($sv->trangThaiHocTap->ma_trang_thai == 'THOI_HOC'): ?> bg-danger
                                                    <?php else: ?> bg-secondary <?php endif; ?>">
                                                    <?php echo e($sv->trangThaiHocTap->ten_trang_thai); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo e(route('dao-tao.sinh-vien.show', $sv->id)); ?>"
                                                    class="btn btn-info btn-sm" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.sinh-vien.edit', $sv->id)); ?>"
                                                    class="btn btn-warning btn-sm" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.sinh-vien.destroy', $sv->id)); ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Xóa sinh viên sẽ xóa cả tài khoản. Bạn chắc chắn?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Hiển thị <?php echo e($sinhViens->firstItem() ?? 0); ?> - <?php echo e($sinhViens->lastItem() ?? 0); ?>

                                trong tổng số <?php echo e($sinhViens->total()); ?> sinh viên
                            </small>
                        </div>
                        <div>
                            <?php echo e($sinhViens->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Form ẩn để xóa nhiều -->
    <form id="formXoaNhieu" action="<?php echo e(route('dao-tao.sinh-vien.destroy-multiple')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <input type="hidden" name="ids" id="idsToDelete">
    </form>

    <script>
        function toggleCheckAll() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
            toggleDeleteButton();
        }

        function toggleDeleteButton() {
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien:checked');
            const btnXoaChon = document.getElementById('btnXoaChon');
            if (checkboxes.length > 0) {
                btnXoaChon.style.display = 'inline-block';
            } else {
                btnXoaChon.style.display = 'none';
            }
            // Cập nhật checkbox "Chọn tất cả"
            const allCheckboxes = document.querySelectorAll('.checkbox-sinh-vien');
            const checkAll = document.getElementById('checkAll');
            if (allCheckboxes.length > 0) {
                checkAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }

        function xoaNhieuSinhVien() {
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một sinh viên để xóa!');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const count = ids.length;

            if (!confirm(`Bạn có chắc chắn muốn xóa ${count} sinh viên đã chọn? Hành động này sẽ xóa cả tài khoản và không thể hoàn tác!`)) {
                return;
            }

            document.getElementById('idsToDelete').value = ids.join(',');
            document.getElementById('formXoaNhieu').submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/sinh-vien/index.blade.php ENDPATH**/ ?>