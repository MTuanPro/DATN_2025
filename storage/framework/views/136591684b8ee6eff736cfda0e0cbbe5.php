<?php $__env->startSection('title', 'Hồ Sơ Cá Nhân'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hồ Sơ Cá Nhân</h3>
                    <p class="text-subtitle text-muted">Quản lý thông tin cá nhân của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hồ sơ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info"
                                        role="tab">
                                        <i class="bi bi-person"></i> Thông Tin Cá Nhân
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password"
                                        role="tab">
                                        <i class="bi bi-key"></i> Đổi Mật Khẩu
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <div class="tab-content">
                                    
                                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-3 text-center mb-4">
                                                <div class="mb-3">
                                                    <img id="preview-avatar"
                                                        src="<?php echo e($daoTao->anh_dai_dien ? asset('storage/' . $daoTao->anh_dai_dien) . '?t=' . time() : asset('assets/images/faces/3.jpg')); ?>"
                                                        alt="Avatar" class="img-fluid rounded-circle"
                                                        style="width: 150px; height: 150px; object-fit: cover;">
                                                </div>
                                                <label for="anh_dai_dien" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-upload"></i> Chọn Ảnh
                                                </label>
                                                <input type="file" class="d-none" id="anh_dai_dien" name="anh_dai_dien"
                                                    accept="image/*">
                                            </div>

                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mã Nhân Viên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            value="<?php echo e($daoTao->ma_dao_tao); ?>" disabled>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Họ và Tên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="ho_ten"
                                                            value="<?php echo e(old('ho_ten', $daoTao->ho_ten)); ?>" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" name="email"
                                                            value="<?php echo e(old('email', $daoTao->email)); ?>" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Số Điện Thoại</label>
                                                        <input type="text" class="form-control" name="so_dien_thoai"
                                                            value="<?php echo e(old('so_dien_thoai', $daoTao->so_dien_thoai)); ?>">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Ngày Sinh</label>
                                                        <input type="date" class="form-control" name="ngay_sinh"
                                                            value="<?php echo e(old('ngay_sinh', $daoTao->ngay_sinh instanceof \Carbon\Carbon ? $daoTao->ngay_sinh->format('Y-m-d') : $daoTao->ngay_sinh)); ?>">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Giới Tính</label>
                                                        <select class="form-select" name="gioi_tinh">
                                                            <option value="">-- Chọn --</option>
                                                            <option value="Nam"
                                                                <?php echo e(old('gioi_tinh', $daoTao->gioi_tinh) == 'Nam' ? 'selected' : ''); ?>>
                                                                Nam</option>
                                                            <option value="Nữ"
                                                                <?php echo e(old('gioi_tinh', $daoTao->gioi_tinh) == 'Nữ' ? 'selected' : ''); ?>>
                                                                Nữ</option>
                                                            <option value="Khác"
                                                                <?php echo e(old('gioi_tinh', $daoTao->gioi_tinh) == 'Khác' ? 'selected' : ''); ?>>
                                                                Khác</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Địa Chỉ</label>
                                                        <textarea class="form-control" name="dia_chi" rows="2"><?php echo e(old('dia_chi', $daoTao->dia_chi)); ?></textarea>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Ghi Chú</label>
                                                        <textarea class="form-control" name="ghi_chu" rows="2"><?php echo e(old('ghi_chu', $daoTao->ghi_chu)); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save"></i> Lưu Thay Đổi
                                            </button>
                                        </div>
                                    </div>

                                    
                                    <div class="tab-pane fade" id="password" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 mx-auto">
                                                <div class="mb-3">
                                                    <label class="form-label">Mật Khẩu Hiện Tại</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="current_password"
                                                            name="current_password" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('current_password')">
                                                            <i class="bi bi-eye" id="current_password_icon"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Chỉ điền nếu muốn đổi mật khẩu</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Mật Khẩu Mới</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="new_password"
                                                            name="new_password" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('new_password')">
                                                            <i class="bi bi-eye" id="new_password_icon"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Xác Nhận Mật Khẩu Mới</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="new_password_confirmation"
                                                            name="new_password_confirmation" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('new_password_confirmation')">
                                                            <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-key"></i> Đổi Mật Khẩu
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Preview avatar
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('anh_dai_dien');
                const previewImg = document.getElementById('preview-avatar');
                
                if (fileInput && previewImg) {
                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Kiểm tra loại file
                            if (!file.type.match('image.*')) {
                                alert('Vui lòng chọn file ảnh!');
                                e.target.value = '';
                                return;
                            }
                            
                            // Kiểm tra kích thước file (max 2MB)
                            if (file.size > 2048 * 1024) {
                                alert('Kích thước ảnh không được vượt quá 2MB!');
                                e.target.value = '';
                                return;
                            }
                            
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                            };
                            reader.onerror = function() {
                                alert('Có lỗi xảy ra khi đọc file!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Toggle password visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '_icon');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/profile/dao-tao.blade.php ENDPATH**/ ?>