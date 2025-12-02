<header class="mb-3" style="padding-bottom: 4rem;">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>


<!-- Top header: notifications, avatar -->
<div class="top-header d-flex align-items-center justify-content-between px-3 py-2 bg-white shadow-sm mb-3">
    <div class="d-flex align-items-center">
        <!-- Search removed -->
    </div>

    <div class="d-flex align-items-center ms-auto">
        <!-- Dark Mode Toggle -->
        <button class="btn btn-link text-decoration-none text-muted me-2" id="theme-toggle" title="Chuyển chế độ sáng/tối" onclick="toggleTheme()">
            <i class="bi bi-moon" id="theme-icon"></i>
        </button>

        <!-- Notification Bell Dropdown -->
        <div class="dropdown me-2">
            <button class="btn btn-link position-relative text-muted" id="notificationDropdown"
                data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                <i class="bi bi-bell fs-5"></i>
                <?php if(isset($soThongBaoChuaDoc) && $soThongBaoChuaDoc > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo e($soThongBaoChuaDoc > 99 ? '99+' : $soThongBaoChuaDoc); ?>

                    </span>
                <?php endif; ?>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown"
                style="width: 350px; max-height: 500px; overflow-y: auto;">
                <li class="dropdown-header d-flex justify-content-between align-items-center">
                    <strong>Thông báo</strong>
                    <?php if(isset($soThongBaoChuaDoc) && $soThongBaoChuaDoc > 0): ?>
                        <span class="badge bg-primary rounded-pill"><?php echo e($soThongBaoChuaDoc); ?> mới</span>
                    <?php endif; ?>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                <?php if(isset($thongBaoChuaDoc) && $thongBaoChuaDoc->count() > 0): ?>
                    <?php $__currentLoopData = $thongBaoChuaDoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nguoiNhan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($nguoiNhan->thongBao): ?>
                            <?php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $showRoute = in_array('admin', $roles)
                                    ? 'admin.thong-bao.show'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.show'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.show'
                                            : 'sinh-vien.thong-bao.show'));
                            ?>
                            <li>
                                <a class="dropdown-item <?php echo e(!$nguoiNhan->da_doc ? 'bg-light' : ''); ?>"
                                    href="<?php echo e(route($showRoute, $nguoiNhan->thongBao->id)); ?>">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-2">
                                            <i
                                                class="bi bi-<?php echo e($nguoiNhan->thongBao->loai_thong_bao == 'tin_gap'
                                                    ? 'exclamation-circle text-danger'
                                                    : ($nguoiNhan->thongBao->loai_thong_bao == 'tin_tuc'
                                                        ? 'newspaper text-info'
                                                        : 'megaphone text-primary')); ?> fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small"><?php echo e(Str::limit($nguoiNhan->thongBao->tieu_de, 50)); ?>

                                            </h6>
                                            <p class="mb-1 text-muted small">
                                                <?php echo e(Str::limit(strip_tags($nguoiNhan->thongBao->noi_dung), 80)); ?>

                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i>
                                                <?php echo e($nguoiNhan->thongBao->ngay_gui->diffForHumans()); ?>

                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php
                        $indexRoute = in_array('admin', $roles)
                            ? 'admin.thong-bao.index'
                            : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                ? 'dao-tao.thong-bao.index'
                                : (in_array('giang_vien', $roles)
                                    ? 'giangvien.thong-bao.index'
                                    : 'sinh-vien.thong-bao.index'));
                    ?>

                    <li>
                        <a class="dropdown-item text-center small text-primary" href="<?php echo e(route($indexRoute)); ?>">
                            <strong>Xem tất cả thông báo</strong>
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <div class="dropdown-item text-center text-muted py-4">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                            <p class="mb-0">Không có thông báo mới</p>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                    $user = auth()->user();
                    $anhDaiDien = $user->anh_dai_dien;
                    $hoTen = $user->name ?? 'User';
                    
                    // Lấy tên từ bảng tương ứng
                    $roles = $user->vaiTro()->pluck('ma_vai_tro')->toArray();
                    if (in_array('giang_vien', $roles) && $user->giangVien) {
                        $hoTen = $user->giangVien->ho_ten;
                    } elseif (in_array('sinh_vien', $roles) && $user->sinhVien) {
                        $hoTen = $user->sinhVien->ho_ten;
                    } elseif ((in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)) && $user->daoTao) {
                        $hoTen = $user->daoTao->ho_ten;
                    } elseif (in_array('admin', $roles) && $user->admin) {
                        $hoTen = $user->admin->ho_ten;
                    }
                ?>
                <?php if($anhDaiDien): ?>
                    <img src="<?php echo e(asset('storage/' . $anhDaiDien)); ?>" alt="user" width="36" height="36"
                        class="rounded-circle me-2" style="object-fit: cover;">
                <?php else: ?>
                    <img src="<?php echo e(asset('assets/images/faces/1.jpg')); ?>" alt="user" width="36" height="36"
                        class="rounded-circle me-2">
                <?php endif; ?>
                <span class="d-none d-md-inline"><?php echo e($hoTen); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>"><i class="bi bi-person me-2"></i>Hồ Sơ</a></li>
                <li><a class="dropdown-item" href="<?php echo e(route('settings.index')); ?>"><i class="bi bi-gear me-2"></i>Cài Đặt</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>Đăng Xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Form logout ẩn -->
<form action="<?php echo e(route('logout')); ?>" method="POST" id="logout-form-header" style="display: none;">
    <?php echo csrf_field(); ?>
</form>

<!-- Dark Mode JavaScript -->
<script>
// Load theme từ localStorage
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('theme-icon');
    
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeIcon.classList.remove('bi-moon');
        themeIcon.classList.add('bi-sun');
    }
});

// Toggle theme
function toggleTheme() {
    const body = document.body;
    const themeIcon = document.getElementById('theme-icon');
    
    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        themeIcon.classList.remove('bi-sun');
        themeIcon.classList.add('bi-moon');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('bi-moon');
        themeIcon.classList.add('bi-sun');
        localStorage.setItem('theme', 'dark');
    }
}
</script>
<?php /**PATH C:\Users\Admin\DATN_2025\resources\views/layouts/blocks/header.blade.php ENDPATH**/ ?>