<?php $__env->startSection('title', 'Chi tiết Giảng viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Giảng viên</h3>
                    <p class="text-subtitle text-muted">Thông tin chi tiết giảng viên: <?php echo e($giangVien->ho_ten); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.giang-vien.index')); ?>">Giảng viên</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-4">
                    
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if($giangVien->anh_dai_dien): ?>
                                <img src="<?php echo e(asset('storage/' . $giangVien->anh_dai_dien)); ?>" alt="Avatar"
                                    class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 150px; height: 150px; font-size: 48px;">
                                    <?php echo e(strtoupper(substr($giangVien->ho_ten, 0, 1))); ?>

                                </div>
                            <?php endif; ?>

                            <h4 class="mb-1"><?php echo e($giangVien->ho_ten); ?></h4>
                            <p class="text-muted mb-2"><?php echo e($giangVien->ma_giang_vien); ?></p>
                            <span class="badge bg-light-primary">Giảng viên</span>

                            <div class="d-grid gap-2 mt-3">
                                <a href="<?php echo e(route('dao-tao.giang-vien.edit', $giangVien->id)); ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Sửa thông tin
                                </a>
                                <form action="<?php echo e(route('dao-tao.giang-vien.destroy', $giangVien->id)); ?>" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                                <a href="<?php echo e(route('dao-tao.giang-vien.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Thông tin chi tiết</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Mã giảng viên:</div>
                                <div class="col-md-8"><?php echo e($giangVien->ma_giang_vien); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Họ tên:</div>
                                <div class="col-md-8"><?php echo e($giangVien->ho_ten); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Email:</div>
                                <div class="col-md-8">
                                    <a href="mailto:<?php echo e($giangVien->email); ?>"><?php echo e($giangVien->email); ?></a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Số điện thoại:</div>
                                <div class="col-md-8">
                                    <a href="tel:<?php echo e($giangVien->so_dien_thoai); ?>"><?php echo e($giangVien->so_dien_thoai); ?></a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Ngày sinh:</div>
                                <div class="col-md-8">
                                    <?php if($giangVien->ngay_sinh): ?>
                                        <?php if($giangVien->ngay_sinh instanceof \Carbon\Carbon): ?>
                                            <?php echo e($giangVien->ngay_sinh->format('d/m/Y')); ?>

                                        <?php else: ?>
                                            <?php echo e(\Carbon\Carbon::parse($giangVien->ngay_sinh)->format('d/m/Y')); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Giới tính:</div>
                                <div class="col-md-8">
                                    <?php if($giangVien->gioi_tinh == 'Nam'): ?>
                                        <span class="badge bg-info"><?php echo e($giangVien->gioi_tinh); ?></span>
                                    <?php elseif($giangVien->gioi_tinh == 'Nữ'): ?>
                                        <span class="badge bg-pink"><?php echo e($giangVien->gioi_tinh); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e($giangVien->gioi_tinh ?? 'N/A'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Địa chỉ:</div>
                                <div class="col-md-8"><?php echo e($giangVien->dia_chi ?? 'N/A'); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Trình độ:</div>
                                <div class="col-md-8">
                                    <span
                                        class="badge bg-light-info"><?php echo e($giangVien->trinhDo->ten_trinh_do ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Chuyên môn:</div>
                                <div class="col-md-8"><?php echo e($giangVien->chuyen_mon); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Khoa:</div>
                                <div class="col-md-8">
                                    <span class="badge bg-light-primary"><?php echo e($giangVien->khoa->ten_khoa ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Ngày vào trường:</div>
                                <div class="col-md-8">
                                    <?php echo e(\Carbon\Carbon::parse($giangVien->ngay_vao_truong)->format('d/m/Y')); ?>

                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Tài khoản liên kết:</div>
                                <div class="col-md-8">
                                    <?php if($giangVien->user): ?>
                                        <span class="badge bg-light-success">
                                            <i class="bi bi-check-circle"></i> <?php echo e($giangVien->user->email); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light-danger">
                                            <i class="bi bi-x-circle"></i> Chưa liên kết
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Ngày tạo:</div>
                                <div class="col-md-8">
                                    <?php echo e(\Carbon\Carbon::parse($giangVien->created_at)->format('d/m/Y H:i')); ?>

                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Cập nhật lần cuối:</div>
                                <div class="col-md-8">
                                    <?php echo e(\Carbon\Carbon::parse($giangVien->updated_at)->format('d/m/Y H:i')); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Thống kê giảng dạy</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <h6 class="text-muted font-semibold">Lớp HP hiện tại</h6>
                                    <h6 class="font-extrabold mb-0">0</h6>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h6 class="text-muted font-semibold">Số sinh viên</h6>
                                    <h6 class="font-extrabold mb-0">0</h6>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <h6 class="text-muted font-semibold">Buổi dạy/tuần</h6>
                                    <h6 class="font-extrabold mb-0">0</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/giang-vien/show.blade.php ENDPATH**/ ?>