<?php $__env->startSection('title', 'Chi tiết Lớp hành chính'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Lớp hành chính</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hanh-chinh.index')); ?>">Lớp hành
                                    chính</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo e($lopHanhChinh->ten_lop); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Mã lớp:</th>
                                    <td><strong><?php echo e($lopHanhChinh->ma_lop); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp:</th>
                                    <td><?php echo e($lopHanhChinh->ten_lop); ?></td>
                                </tr>
                                <tr>
                                    <th>Khóa học:</th>
                                    <td>
                                        <?php if($lopHanhChinh->khoaHoc): ?>
                                            <span class="badge bg-primary"><?php echo e($lopHanhChinh->khoaHoc->ten_khoa_hoc); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngành:</th>
                                    <td>
                                        <?php if($lopHanhChinh->nganh): ?>
                                            <?php echo e($lopHanhChinh->nganh->ma_nganh); ?> - <?php echo e($lopHanhChinh->nganh->ten_nganh); ?>

                                            <?php if($lopHanhChinh->nganh->khoa): ?>
                                                <br><small class="text-muted">Khoa:
                                                    <?php echo e($lopHanhChinh->nganh->khoa->ten_khoa); ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>GVCN:</th>
                                    <td>
                                        <?php if($lopHanhChinh->giangVienChuNhiem): ?>
                                            <?php echo e($lopHanhChinh->giangVienChuNhiem->ho_ten); ?>

                                            <br><small
                                                class="text-muted"><?php echo e($lopHanhChinh->giangVienChuNhiem->email); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa có</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><span class="badge bg-info fs-6"><?php echo e($lopHanhChinh->si_so); ?> sinh viên</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Danh sách sinh viên -->
                    <?php if($lopHanhChinh->sinhVien->count() > 0): ?>
                        <hr>
                        <h6 class="mt-3 mb-3">Danh sách sinh viên (<?php echo e($lopHanhChinh->sinhVien->count()); ?>)</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>MSSV</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Giới tính</th>
                                        <th>SĐT</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lopHanhChinh->sinhVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><strong><?php echo e($sv->ma_sinh_vien); ?></strong></td>
                                            <td><?php echo e($sv->ho_ten); ?></td>
                                            <td><?php echo e($sv->email); ?></td>
                                            <td>
                                                <?php if($sv->gioi_tinh == 'nam'): ?>
                                                    <span class="badge bg-info">Nam</span>
                                                <?php elseif($sv->gioi_tinh == 'nu'): ?>
                                                    <span class="badge bg-warning">Nữ</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Khác</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($sv->so_dien_thoai); ?></td>
                                            <td>
                                                <?php if($sv->trangThaiHocTap): ?>
                                                    <span
                                                        class="badge bg-success"><?php echo e($sv->trangThaiHocTap->ten_trang_thai); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> Lớp chưa có sinh viên nào.
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="<?php echo e(route('dao-tao.lop-hanh-chinh.index')); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <a href="<?php echo e(route('dao-tao.lop-hanh-chinh.edit', $lopHanhChinh->id)); ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Sửa
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lop-hanh-chinh/show.blade.php ENDPATH**/ ?>