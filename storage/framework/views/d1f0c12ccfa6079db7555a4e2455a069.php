<?php $__env->startSection('title', 'Chi tiết cấu hình điểm'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Cấu hình đầu điểm</h3>
                <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.cau-hinh-diem.index')); ?>">Cấu hình điểm</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Thông tin lớp -->
    <section class="section">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="bi bi-info-circle"></i> Thông tin lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mã lớp:</th>
                                <td><strong><?php echo e($lopHocPhan->ma_lop_hp); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td><?php echo e($lopHocPhan->monHoc->ma_mon); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></td>
                            </tr>
                            <tr>
                                <th>Số tín chỉ:</th>
                                <td><?php echo e($lopHocPhan->monHoc->so_tin_chi); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Học kỳ:</th>
                                <td><?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> - <?php echo e($lopHocPhan->hocKy->nam_hoc); ?></td>
                            </tr>
                            <tr>
                                <th>Tổng tỷ lệ:</th>
                                <td>
                                    <?php if($tongTyLe == 100): ?>
                                        <span class="badge bg-success"><?php echo e($tongTyLe); ?>%</span>
                                    <?php elseif($tongTyLe > 0): ?>
                                        <span class="badge bg-warning"><?php echo e($tongTyLe); ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    <?php if($hoanThien): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoàn thiện</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><i class="bi bi-exclamation-triangle"></i> Chưa hoàn thiện</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Danh sách cấu hình -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Danh sách đầu điểm</h5>
                <?php if($tongTyLe < 100): ?>
                    <a href="<?php echo e(route('giangvien.cau-hinh-diem.create', $lopHocPhan->id)); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm đầu điểm
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($cauHinhs->isEmpty()): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có cấu hình đầu điểm nào. Nhấn "Thêm đầu điểm" để bắt đầu.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">STT</th>
                                    <th>Tên đầu điểm</th>
                                    <th width="120" class="text-center">Tỷ lệ (%)</th>
                                    <th width="120" class="text-center">Số cột</th>
                                    <th width="180" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-center"><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($ch->ten_dau_diem); ?></strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?php echo e($ch->ty_le); ?>%</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?php echo e($ch->so_cot); ?> cột</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo e(route('giangvien.cau-hinh-diem.edit', $ch->id)); ?>" 
                                                class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo e($ch->id); ?>)" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-form-<?php echo e($ch->id); ?>" 
                                                action="<?php echo e(route('giangvien.cau-hinh-diem.destroy', $ch->id)); ?>" 
                                                method="POST" style="display: none;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-end">Tổng cộng:</th>
                                    <th class="text-center">
                                        <span class="badge <?php echo e($hoanThien ? 'bg-success' : 'bg-warning'); ?>">
                                            <?php echo e($tongTyLe); ?>%
                                        </span>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if(!$hoanThien && !$cauHinhs->isEmpty()): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Tổng tỷ lệ hiện tại là <?php echo e($tongTyLe); ?>%. 
                        Cần đủ 100% để hoàn thiện cấu hình. Còn lại: <strong><?php echo e(100 - $tongTyLe); ?>%</strong>
                    </div>
                <?php endif; ?>

                <?php if($hoanThien): ?>
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> 
                        <strong>Hoàn thiện!</strong> Cấu hình đầu điểm đã đủ 100%. Bạn có thể bắt đầu nhập điểm.
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <a href="<?php echo e(route('giangvien.cau-hinh-diem.index')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc muốn xóa cấu hình này không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/cau-hinh-diem/show.blade.php ENDPATH**/ ?>