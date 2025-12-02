<?php $__env->startSection('title', 'Chi tiết thông báo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết thông báo</h3>
                    <p class="text-subtitle text-muted">Xem thông tin chi tiết thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <?php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $dashboardRoute = in_array('admin', $roles)
                                    ? 'admin.dashboard'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.dashboard'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.dashboard'
                                            : 'sinh-vien.dashboard'));
                                $indexRoute = in_array('admin', $roles)
                                    ? 'admin.thong-bao.index'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.index'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.index'
                                            : 'sinh-vien.thong-bao.index'));
                            ?>
                            <li class="breadcrumb-item"><a href="<?php echo e(route($dashboardRoute)); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route($indexRoute)); ?>">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?php echo e($thongBao->tieu_de); ?></h5>
                        <div>
                            <?php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $isAdmin = in_array('admin', $roles);
                                $indexRoute = $isAdmin
                                    ? 'admin.thong-bao.index'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.index'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.index'
                                            : 'sinh-vien.thong-bao.index'));
                            ?>

                            <?php if($isAdmin): ?>
                                <a href="<?php echo e(route('admin.thong-bao.edit', $thongBao)); ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo e(route($indexRoute)); ?>" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            
                            <div class="mb-4">
                                <h6>Nội dung:</h6>
                                <div class="border p-3 rounded bg-light">
                                    <?php echo nl2br(e($thongBao->noi_dung)); ?>

                                </div>
                            </div>

                            
                            <?php if($thongBao->anh_dai_dien): ?>
                                <div class="mb-4">
                                    <h6>Ảnh đại diện:</h6>
                                    <img src="<?php echo e(asset('storage/' . $thongBao->anh_dai_dien)); ?>" alt="Ảnh đại diện"
                                        class="img-fluid rounded shadow" style="max-height: 400px;">
                                </div>
                            <?php endif; ?>

                            
                            <?php if($thongBao->file_dinh_kem): ?>
                                <div class="mb-4">
                                    <h6>File đính kèm:</h6>
                                    <a href="<?php echo e(route('admin.thong-bao.download', $thongBao)); ?>"
                                        class="btn btn-info btn-sm">
                                        <i class="bi bi-download"></i> Tải xuống file
                                        (<?php echo e(basename($thongBao->file_dinh_kem)); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
<!-- sửa giao diện -->
                            
                            <div class="mb-4">
                                <h6>Thống kê người nhận:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Tổng số người nhận</h6>
                                                <h3><?php echo e($thongBao->nguoiNhan->count()); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Đã xem</h6>
                                                <h3><?php echo e($thongBao->nguoiNhan->where('da_doc', true)->count()); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Chưa xem</h6>
                                                <h3><?php echo e($thongBao->nguoiNhan->where('da_doc', false)->count()); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Thông tin</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Loại thông báo:</strong></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($thongBao->getLoaiColor()); ?>">
                                                    <?php echo e(str_replace('_', ' ', ucfirst($thongBao->loai_thong_bao))); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mức độ:</strong></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($thongBao->getMucDoColor()); ?>">
                                                    <?php echo e(str_replace('_', ' ', ucfirst($thongBao->muc_do_quan_trong))); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Đối tượng:</strong></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo e(str_replace('_', ' ', ucfirst($thongBao->doi_tuong))); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Người gửi:</strong></td>
                                            <td><?php echo e($thongBao->nguoiGui->name ?? 'Hệ thống'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày gửi:</strong></td>
                                            <td><?php echo e($thongBao->ngay_gui->format('d/m/Y H:i')); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                <?php if($thongBao->trang_thai == 'cong_khai'): ?>
                                                    <span class="badge bg-success">Công khai</span>
                                                <?php elseif($thongBao->trang_thai == 'nhap'): ?>
                                                    <span class="badge bg-warning">Nháp</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã xóa</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lượt xem:</strong></td>
                                            <td><?php echo e($thongBao->so_luot_xem); ?></td>
                                        </tr>
                                        <?php if($thongBao->ghim_dau_trang): ?>
                                            <tr>
                                                <td colspan="2">
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-pin-angle-fill"></i> Đã ghim
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if($thongBao->hien_thi_tu_ngay): ?>
                                            <tr>
                                                <td><strong>Hiển thị từ:</strong></td>
                                                <td><?php echo e($thongBao->hien_thi_tu_ngay->format('d/m/Y H:i')); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if($thongBao->ngay_het_han): ?>
                                            <tr>
                                                <td><strong>Hết hạn:</strong></td>
                                                <td><?php echo e($thongBao->ngay_het_han->format('d/m/Y H:i')); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.' . $layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/thong-bao-show.blade.php ENDPATH**/ ?>