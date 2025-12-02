<?php $__env->startSection('title', 'Sửa Sinh viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Sinh viên</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.sinh-vien.index')); ?>">Sinh viên</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5>Sửa thông tin sinh viên: <?php echo e($sinhVien->ma_sinh_vien); ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.sinh-vien.update', $sinhVien->id)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Thông tin cơ bản -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Thông tin cơ bản</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ma_sinh_vien" class="form-label">MSSV <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ma_sinh_vien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ma_sinh_vien" name="ma_sinh_vien"
                                    value="<?php echo e(old('ma_sinh_vien', $sinhVien->ma_sinh_vien)); ?>" required>
                                <?php $__errorArgs = ['ma_sinh_vien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ho_ten" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ho_ten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ho_ten" name="ho_ten" value="<?php echo e(old('ho_ten', $sinhVien->ho_ten)); ?>" required>
                                <?php $__errorArgs = ['ho_ten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="email" name="email" value="<?php echo e(old('email', $sinhVien->email)); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="so_dien_thoai" class="form-label">SĐT</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['so_dien_thoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="so_dien_thoai" name="so_dien_thoai"
                                    value="<?php echo e(old('so_dien_thoai', $sinhVien->so_dien_thoai)); ?>">
                                <?php $__errorArgs = ['so_dien_thoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['ngay_sinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ngay_sinh" name="ngay_sinh"
                                    value="<?php echo e(old('ngay_sinh', $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['ngay_sinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                <select class="form-select <?php $__errorArgs = ['gioi_tinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="gioi_tinh"
                                    name="gioi_tinh">
                                    <option value="">-- Chọn --</option>
                                    <option value="nam"
                                        <?php echo e(old('gioi_tinh', $sinhVien->gioi_tinh) == 'nam' ? 'selected' : ''); ?>>Nam
                                    </option>
                                    <option value="nu"
                                        <?php echo e(old('gioi_tinh', $sinhVien->gioi_tinh) == 'nu' ? 'selected' : ''); ?>>Nữ</option>
                                    <option value="khac"
                                        <?php echo e(old('gioi_tinh', $sinhVien->gioi_tinh) == 'khac' ? 'selected' : ''); ?>>Khác
                                    </option>
                                </select>
                                <?php $__errorArgs = ['gioi_tinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Giấy tờ tùy thân -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Giấy tờ tùy thân</h6>
                                <hr>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="can_cuoc_cong_dan" class="form-label">CCCD <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['can_cuoc_cong_dan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="can_cuoc_cong_dan" name="can_cuoc_cong_dan"
                                    value="<?php echo e(old('can_cuoc_cong_dan', $sinhVien->can_cuoc_cong_dan)); ?>" required>
                                <?php $__errorArgs = ['can_cuoc_cong_dan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ngay_cap_cccd" class="form-label">Ngày cấp</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['ngay_cap_cccd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ngay_cap_cccd" name="ngay_cap_cccd"
                                    value="<?php echo e(old('ngay_cap_cccd', $sinhVien->ngay_cap_cccd ? $sinhVien->ngay_cap_cccd->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['ngay_cap_cccd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="noi_cap_cccd" class="form-label">Nơi cấp</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['noi_cap_cccd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="noi_cap_cccd" name="noi_cap_cccd"
                                    value="<?php echo e(old('noi_cap_cccd', $sinhVien->noi_cap_cccd)); ?>">
                                <?php $__errorArgs = ['noi_cap_cccd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Địa chỉ</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="so_nha_duong" class="form-label">Số nhà, đường</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['so_nha_duong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="so_nha_duong" name="so_nha_duong"
                                    value="<?php echo e(old('so_nha_duong', $sinhVien->so_nha_duong)); ?>">
                                <?php $__errorArgs = ['so_nha_duong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phuong_xa" class="form-label">Phường/Xã</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['phuong_xa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="phuong_xa" name="phuong_xa"
                                    value="<?php echo e(old('phuong_xa', $sinhVien->phuong_xa)); ?>">
                                <?php $__errorArgs = ['phuong_xa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quan_huyen" class="form-label">Quận/Huyện</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['quan_huyen'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="quan_huyen" name="quan_huyen"
                                    value="<?php echo e(old('quan_huyen', $sinhVien->quan_huyen)); ?>">
                                <?php $__errorArgs = ['quan_huyen'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tinh_thanh" class="form-label">Tỉnh/Thành phố</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['tinh_thanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="tinh_thanh" name="tinh_thanh"
                                    value="<?php echo e(old('tinh_thanh', $sinhVien->tinh_thanh)); ?>">
                                <?php $__errorArgs = ['tinh_thanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Ảnh đại diện -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Ảnh đại diện</h6>
                                <hr>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>
                                <?php if($sinhVien->anh_dai_dien): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo e(Storage::url($sinhVien->anh_dai_dien)); ?>" alt="Avatar"
                                            class="img-thumbnail" style="max-width: 150px;">
                                        <small class="d-block text-muted">Ảnh hiện tại</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control <?php $__errorArgs = ['anh_dai_dien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="anh_dai_dien" name="anh_dai_dien" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Chọn file mới để thay đổi ảnh. Định dạng: JPG, PNG. Kích thước
                                    tối đa: 2MB</small>
                                <?php $__errorArgs = ['anh_dai_dien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Thông tin học vụ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Thông tin học vụ</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="khoa_hoc_id" class="form-label">Khóa học <span
                                        class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['khoa_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="khoa_hoc_id"
                                    name="khoa_hoc_id" required>
                                    <option value="">-- Chọn khóa học --</option>
                                    <?php $__currentLoopData = $khoaHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($kh->id); ?>"
                                            <?php echo e(old('khoa_hoc_id', $sinhVien->khoa_hoc_id) == $kh->id ? 'selected' : ''); ?>>
                                            <?php echo e($kh->ten_khoa_hoc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['khoa_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lop_hanh_chinh_id" class="form-label">Lớp hành chính <span
                                        class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['lop_hanh_chinh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="lop_hanh_chinh_id" name="lop_hanh_chinh_id" required>
                                    <option value="">-- Chọn lớp --</option>
                                    <?php $__currentLoopData = $lopHanhChinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lop->id); ?>" data-nganh-id="<?php echo e($lop->nganh_id); ?>"
                                            data-khoa-hoc-id="<?php echo e($lop->khoa_hoc_id); ?>"
                                            <?php echo e(old('lop_hanh_chinh_id', $sinhVien->lop_hanh_chinh_id) == $lop->id ? 'selected' : ''); ?>>
                                            <?php echo e($lop->ma_lop); ?> - <?php echo e($lop->ten_lop); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-info">⚠️ Chuyển lớp sẽ tự động cập nhật sĩ số</small>
                                <?php $__errorArgs = ['lop_hanh_chinh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nganh_id" class="form-label">Ngành <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['nganh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nganh_id"
                                    name="nganh_id" required>
                                    <option value="">-- Chọn ngành --</option>
                                    <?php $__currentLoopData = $nganhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nganh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($nganh->id); ?>"
                                            <?php echo e(old('nganh_id', $sinhVien->nganh_id) == $nganh->id ? 'selected' : ''); ?>>
                                            <?php echo e($nganh->ma_nganh); ?> - <?php echo e($nganh->ten_nganh); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['nganh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="chuyen_nganh_id" class="form-label">Chuyên ngành</label>
                                <select class="form-select <?php $__errorArgs = ['chuyen_nganh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="chuyen_nganh_id" name="chuyen_nganh_id">
                                    <option value="">-- Chọn chuyên ngành (nếu có) --</option>
                                    <?php $__currentLoopData = $chuyenNganhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cn->id); ?>" data-nganh-id="<?php echo e($cn->nganh_id); ?>"
                                            <?php echo e(old('chuyen_nganh_id', $sinhVien->chuyen_nganh_id) == $cn->id ? 'selected' : ''); ?>>
                                            <?php echo e($cn->ma_chuyen_nganh); ?> - <?php echo e($cn->ten_chuyen_nganh); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['chuyen_nganh_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ky_hien_tai" class="form-label">Kỳ hiện tại <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control <?php $__errorArgs = ['ky_hien_tai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ky_hien_tai" name="ky_hien_tai" min="1" max="8"
                                    value="<?php echo e(old('ky_hien_tai', $sinhVien->ky_hien_tai)); ?>" required>
                                <?php $__errorArgs = ['ky_hien_tai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="trang_thai_hoc_tap_id" class="form-label">Trạng thái học tập <span
                                        class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['trang_thai_hoc_tap_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="trang_thai_hoc_tap_id" name="trang_thai_hoc_tap_id" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    <?php $__currentLoopData = $trangThais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tt->id); ?>"
                                            <?php echo e(old('trang_thai_hoc_tap_id', $sinhVien->trang_thai_hoc_tap_id) == $tt->id ? 'selected' : ''); ?>>
                                            <?php echo e($tt->ten_trang_thai); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['trang_thai_hoc_tap_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('dao-tao.sinh-vien.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php
        $oldChuyenNganhId = old('chuyen_nganh_id', $sinhVien->chuyen_nganh_id ?? '');
        $oldLopHanhChinhId = old('lop_hanh_chinh_id', $sinhVien->lop_hanh_chinh_id ?? '');
        
        // Chuẩn bị dữ liệu cho JavaScript
        $chuyenNganhsData = $chuyenNganhs->map(function($cn) {
            return [
                'value' => $cn->id,
                'text' => $cn->ma_chuyen_nganh . ' - ' . $cn->ten_chuyen_nganh,
                'nganhId' => (string)$cn->nganh_id
            ];
        })->toArray();
        
        $lopHanhChinhsData = $lopHanhChinhs->map(function($lop) {
            return [
                'value' => $lop->id,
                'text' => $lop->ma_lop . ' - ' . $lop->ten_lop,
                'nganhId' => (string)$lop->nganh_id,
                'khoaHocId' => (string)$lop->khoa_hoc_id
            ];
        })->toArray();
    ?>
    <script>
        $(document).ready(function() {
            // Đợi Select2 khởi tạo xong
            setTimeout(function() {
                const $nganhSelect = $('#nganh_id');
                const $chuyenNganhSelect = $('#chuyen_nganh_id');
                const $khoaHocSelect = $('#khoa_hoc_id');
                const $lopHanhChinhSelect = $('#lop_hanh_chinh_id');

                // Lưu trữ dữ liệu từ PHP
                const allChuyenNganhs = <?php echo json_encode($chuyenNganhsData, 15, 512) ?>;
                const allLopHanhChinhs = <?php echo json_encode($lopHanhChinhsData, 15, 512) ?>;

                const currentChuyenNganhId = '<?php echo e($oldChuyenNganhId); ?>';
                const currentLopHanhChinhId = '<?php echo e($oldLopHanhChinhId); ?>';

                // Hàm lọc chuyên ngành theo ngành
                function filterChuyenNganh(nganhId) {
                    nganhId = String(nganhId || '').trim();

                    // Xóa tất cả options trừ option đầu tiên
                    $chuyenNganhSelect.find('option:not(:first)').remove();

                    if (!nganhId) {
                        $chuyenNganhSelect.prop('disabled', true);
                        $chuyenNganhSelect.html('<option value="">-- Vui lòng chọn ngành trước --</option>');
                    } else {
                        $chuyenNganhSelect.prop('disabled', false);
                        $chuyenNganhSelect.html('<option value="">-- Chọn chuyên ngành (nếu có) --</option>');

                        let hasOptions = false;
                        allChuyenNganhs.forEach(chuyenNganh => {
                            if (chuyenNganh.nganhId === nganhId) {
                                $chuyenNganhSelect.append(
                                    $('<option></option>')
                                        .attr('value', chuyenNganh.value)
                                        .attr('data-nganh-id', chuyenNganh.nganhId)
                                        .text(chuyenNganh.text)
                                );
                                hasOptions = true;
                            }
                        });

                        if (!hasOptions) {
                            $chuyenNganhSelect.append('<option value="">-- Không có chuyên ngành --</option>');
                        }
                    }
                    
                    // Trigger Select2 để cập nhật UI
                    $chuyenNganhSelect.trigger('change.select2');
                }

                // Hàm lọc lớp hành chính theo ngành và khóa học
                function filterLopHanhChinh() {
                    let nganhId = String($nganhSelect.val() || '').trim();
                    let khoaHocId = String($khoaHocSelect.val() || '').trim();

                    // Lưu giá trị hiện tại trước khi xóa
                    const currentValue = $lopHanhChinhSelect.val();

                    // Xóa tất cả options trừ option đầu tiên
                    $lopHanhChinhSelect.find('option:not(:first)').remove();

                    if (!nganhId || !khoaHocId) {
                        $lopHanhChinhSelect.prop('disabled', true);
                        $lopHanhChinhSelect.html('<option value="">-- Chọn khóa học và ngành trước --</option>');
                    } else {
                        $lopHanhChinhSelect.prop('disabled', false);
                        $lopHanhChinhSelect.html('<option value="">-- Chọn lớp hành chính --</option>');

                        let hasOptions = false;
                        allLopHanhChinhs.forEach(lop => {
                            if (lop.nganhId === nganhId && lop.khoaHocId === khoaHocId) {
                                $lopHanhChinhSelect.append(
                                    $('<option></option>')
                                        .attr('value', lop.value)
                                        .attr('data-nganh-id', lop.nganhId)
                                        .attr('data-khoa-hoc-id', lop.khoaHocId)
                                        .text(lop.text)
                                );
                                hasOptions = true;
                            }
                        });

                        if (!hasOptions) {
                            $lopHanhChinhSelect.append('<option value="">-- Không có lớp phù hợp --</option>');
                        }
                    }
                    
                    // Trigger Select2 để cập nhật UI
                    $lopHanhChinhSelect.trigger('change.select2');

                    // Khôi phục giá trị đã chọn nếu còn tồn tại
                    if (currentValue) {
                        setTimeout(() => {
                            if ($lopHanhChinhSelect.find(`option[value="${currentValue}"]`).length > 0) {
                                $lopHanhChinhSelect.val(currentValue).trigger('change.select2');
                            }
                        }, 300);
                    }
                }

                // Lắng nghe sự kiện thay đổi ngành (Select2 event)
                $nganhSelect.on('change.select2', function() {
                    filterChuyenNganh($(this).val());
                    filterLopHanhChinh();
                });

                // Lắng nghe sự kiện thay đổi khóa học (Select2 event)
                $khoaHocSelect.on('change.select2', function() {
                    filterLopHanhChinh();
                });

                // Khởi tạo trạng thái ban đầu
                const initialNganhId = String($nganhSelect.val() || '').trim();
                const initialKhoaHocId = String($khoaHocSelect.val() || '').trim();

                // Luôn gọi filter để khởi tạo đúng trạng thái
                filterChuyenNganh(initialNganhId);
                filterLopHanhChinh();

                // Khôi phục giá trị chuyên ngành đã chọn nếu có
                if (currentChuyenNganhId && initialNganhId) {
                    setTimeout(() => {
                        if ($chuyenNganhSelect.find(`option[value="${currentChuyenNganhId}"]`).length > 0) {
                            $chuyenNganhSelect.val(currentChuyenNganhId).trigger('change.select2');
                        }
                    }, 300);
                }

                // Khôi phục giá trị lớp đã chọn nếu có
                if (currentLopHanhChinhId && initialNganhId && initialKhoaHocId) {
                    setTimeout(() => {
                        if ($lopHanhChinhSelect.find(`option[value="${currentLopHanhChinhId}"]`).length > 0) {
                            $lopHanhChinhSelect.val(currentLopHanhChinhId).trigger('change.select2');
                        }
                    }, 300);
                }
            }, 500); // Đợi Select2 khởi tạo xong
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/sinh-vien/edit.blade.php ENDPATH**/ ?>