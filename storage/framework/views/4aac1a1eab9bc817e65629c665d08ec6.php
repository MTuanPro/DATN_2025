<?php $__env->startSection('title', 'Danh sách Giảng viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Giảng viên</h3>
                    <p class="text-subtitle text-muted">Quản lý thông tin giảng viên trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Giảng viên</li>
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
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách Giảng viên</h5>
                        <div>
                            <button type="button" id="btnXoaChon" class="btn btn-danger me-1" style="display: none;"
                                onclick="xoaNhieuGiangVien()">
                                <i class="bi bi-trash"></i> Xóa đã chọn
                            </button>
                            <a href="<?php echo e(route('dao-tao.giang-vien.show-import-form')); ?>" class="btn btn-success me-1">
                                <i class="bi bi-upload"></i> Import Excel
                            </a>
                            <a href="<?php echo e(route('dao-tao.giang-vien.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Giảng viên
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form action="<?php echo e(route('dao-tao.giang-vien.index')); ?>" method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm theo mã, tên, email, SĐT..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="khoa_id" class="form-select">
                                    <option value="">-- Tất cả khoa --</option>
                                    <?php $__currentLoopData = $khoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $khoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($khoa->id); ?>"
                                            <?php echo e(request('khoa_id') == $khoa->id ? 'selected' : ''); ?>>
                                            <?php echo e($khoa->ten_khoa); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="trinh_do_id" class="form-select">
                                    <option value="">-- Tất cả trình độ --</option>
                                    <?php $__currentLoopData = $trinhDos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trinhDo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($trinhDo->id); ?>"
                                            <?php echo e(request('trinh_do_id') == $trinhDo->id ? 'selected' : ''); ?>>
                                            <?php echo e($trinhDo->ten_trinh_do); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Khoa</th>
                                    <th>Trình độ</th>
                                    <th>Môn học</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $giangVien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-giang-vien" value="<?php echo e($giangVien->id); ?>"
                                                onchange="toggleDeleteButton()">
                                        </td>
                                        <td><?php echo e($giangViens->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($giangVien->ma_giang_vien); ?></strong></td>
                                        <td><?php echo e($giangVien->ho_ten); ?></td>
                                        <td><?php echo e($giangVien->email); ?></td>
                                        <td><?php echo e($giangVien->so_dien_thoai); ?></td>
                                        <td><?php echo e($giangVien->khoa->ten_khoa ?? '-'); ?></td>
                                        <td><?php echo e($giangVien->trinhDo->ten_trinh_do ?? '-'); ?></td>
                                        <td>
                                            <?php if($giangVien->monHocs->count() > 0): ?>
                                                <span class="badge bg-primary"><?php echo e($giangVien->monHocs->count()); ?>

                                                    môn</span>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.giang-vien.show', $giangVien->id)); ?>"
                                                    class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.giang-vien.edit', $giangVien->id)); ?>"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.giang-vien.destroy', $giangVien->id)); ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này?')">
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
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có giảng viên nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-3">
                        <?php echo e($giangViens->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Form ẩn để xóa nhiều -->
    <form id="formXoaNhieu" action="<?php echo e(route('dao-tao.giang-vien.destroy-multiple')); ?>" method="POST"
        style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <input type="hidden" name="ids" id="idsToDelete">
    </form>

    <script>
        function toggleCheckAll() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.checkbox-giang-vien');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
            toggleDeleteButton();
        }

        function toggleDeleteButton() {
            const checkboxes = document.querySelectorAll('.checkbox-giang-vien:checked');
            const btnXoaChon = document.getElementById('btnXoaChon');
            if (checkboxes.length > 0) {
                btnXoaChon.style.display = 'inline-block';
            } else {
                btnXoaChon.style.display = 'none';
            }
            // Cập nhật checkbox "Chọn tất cả"
            const allCheckboxes = document.querySelectorAll('.checkbox-giang-vien');
            const checkAll = document.getElementById('checkAll');
            if (allCheckboxes.length > 0) {
                checkAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }

        function xoaNhieuGiangVien() {
            const checkboxes = document.querySelectorAll('.checkbox-giang-vien:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một giảng viên để xóa!');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const count = ids.length;

            if (!confirm(
                    `Bạn có chắc chắn muốn xóa ${count} giảng viên đã chọn? Hành động này sẽ xóa cả tài khoản và không thể hoàn tác!`
                    )) {
                return;
            }

            document.getElementById('idsToDelete').value = ids.join(',');
            document.getElementById('formXoaNhieu').submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/giang-vien/index.blade.php ENDPATH**/ ?>