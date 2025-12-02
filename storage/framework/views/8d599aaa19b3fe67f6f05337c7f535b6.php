<?php $__env->startSection('title', 'Mẫu thông báo tự động'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Quản lý các mẫu thông báo tự động của hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mẫu thông báo tự động</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Danh sách mẫu thông báo</h4>
                        <a href="<?php echo e(route('dao-tao.mau-thong-bao.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tạo mẫu mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Loại thông báo</th>
                                    <th>Tiêu đề mẫu</th>
                                    <th>Đối tượng</th>
                                    <th>Mức độ</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $mauThongBaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mau): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e(\App\Models\MauThongBaoTuDong::getLoaiThongBaoOptions()[$mau->loai_thong_bao] ?? $mau->loai_thong_bao); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($mau->tieu_de_mau); ?></td>
                                        <td><?php echo e($mau->doi_tuong_mac_dinh ?? 'Tất cả'); ?></td>
                                        <td>
                                            <?php if($mau->muc_do_uu_tien == 'rat_quan_trong'): ?>
                                                <span class="badge bg-danger">Rất quan trọng</span>
                                            <?php elseif($mau->muc_do_uu_tien == 'quan_trong'): ?>
                                                <span class="badge bg-warning">Quan trọng</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Bình thường</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($mau->gui_email_mac_dinh): ?>
                                                <i class="bi bi-check-circle text-success"></i> Có
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-muted"></i> Không
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="<?php echo e(route('dao-tao.mau-thong-bao.toggle', $mau->id)); ?>"
                                                method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-sm btn-link p-0">
                                                    <?php if($mau->kich_hoat): ?>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-toggle-on"></i> Bật
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-toggle-off"></i> Tắt
                                                        </span>
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.mau-thong-bao.show', $mau->id)); ?>"
                                                    class="btn btn-sm btn-info" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('dao-tao.mau-thong-bao.edit', $mau->id)); ?>"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.mau-thong-bao.destroy', $mau->id)); ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Chưa có mẫu thông báo nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn sử dụng biến -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">📝 Hướng dẫn sử dụng biến trong template</h5>
                </div>
                <div class="card-body">
                    <p>Bạn có thể sử dụng các biến sau trong tiêu đề và nội dung mẫu:</p>
                    <ul>
                        <li><code>{ho_ten}</code> - Họ tên người nhận</li>
                        <li><code>{mon_hoc}</code> - Tên môn học</li>
                        <li><code>{ngay_hoc}</code> - Ngày học</li>
                        <li><code>{phong_hoc}</code> - Phòng học</li>
                        <li><code>{ngay_thi}</code> - Ngày thi</li>
                        <li><code>{so_tien}</code> - Số tiền học phí</li>
                        <li><code>{han_dong}</code> - Hạn đóng học phí</li>
                        <li><code>{diem}</code> - Điểm số</li>
                    </ul>
                    <p class="text-muted mb-0"><small><i class="bi bi-info-circle"></i> Hệ thống sẽ tự động thay thế các
                            biến này bằng giá trị thực tế khi gửi thông báo.</small></p>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/mau-thong-bao/index.blade.php ENDPATH**/ ?>