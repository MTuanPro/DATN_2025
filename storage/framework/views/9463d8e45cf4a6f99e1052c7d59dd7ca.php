

<?php $__env->startSection('title', 'Tạo mẫu thông báo tự động'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tạo mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Tạo mẫu thông báo tự động mới</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.mau-thong-bao.index')); ?>">Mẫu thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin mẫu thông báo</h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.mau-thong-bao.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="loai_thong_bao" class="form-label">
                                        Loại thông báo <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?php $__errorArgs = ['loai_thong_bao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="loai_thong_bao"
                                        name="loai_thong_bao" required>
                                        <option value="">-- Chọn loại thông báo --</option>
                                        <?php $__currentLoopData = $loaiThongBaoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(old('loai_thong_bao') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['loai_thong_bao'];
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="doi_tuong_mac_dinh" class="form-label">Đối tượng mặc định</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['doi_tuong_mac_dinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="doi_tuong_mac_dinh" name="doi_tuong_mac_dinh" value="<?php echo e(old('doi_tuong_mac_dinh')); ?>"
                                        placeholder="VD: sinh_vien, giang_vien">
                                    <?php $__errorArgs = ['doi_tuong_mac_dinh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Để trống nếu áp dụng cho tất cả</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tieu_de_mau" class="form-label">
                                Tiêu đề mẫu <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?php $__errorArgs = ['tieu_de_mau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="tieu_de_mau" name="tieu_de_mau" value="<?php echo e(old('tieu_de_mau')); ?>"
                                placeholder="VD: Thông báo lịch học mới cho {mon_hoc}" required>
                            <?php $__errorArgs = ['tieu_de_mau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Có thể sử dụng biến: {mon_hoc}, {ngay_thi}, {ten_sinh_vien}, ...</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="noi_dung_mau" class="form-label">
                                Nội dung mẫu <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?php $__errorArgs = ['noi_dung_mau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="noi_dung_mau"
                                name="noi_dung_mau" rows="8" required><?php echo e(old('noi_dung_mau')); ?></textarea>
                            <?php $__errorArgs = ['noi_dung_mau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Có thể sử dụng biến: {mon_hoc}, {ngay_thi}, {ten_sinh_vien}, ...</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="muc_do_uu_tien" class="form-label">
                                        Mức độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?php $__errorArgs = ['muc_do_uu_tien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="muc_do_uu_tien"
                                        name="muc_do_uu_tien" required>
                                        <option value="binh_thuong" <?php echo e(old('muc_do_uu_tien') == 'binh_thuong' ? 'selected' : ''); ?>>
                                            Bình thường
                                        </option>
                                        <option value="quan_trong" <?php echo e(old('muc_do_uu_tien') == 'quan_trong' ? 'selected' : ''); ?>>
                                            Quan trọng
                                        </option>
                                        <option value="rat_quan_trong"
                                            <?php echo e(old('muc_do_uu_tien') == 'rat_quan_trong' ? 'selected' : ''); ?>>
                                            Rất quan trọng
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['muc_do_uu_tien'];
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
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_email_mac_dinh"
                                            name="gui_email_mac_dinh" value="1" <?php echo e(old('gui_email_mac_dinh') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="gui_email_mac_dinh">
                                            Gửi email mặc định
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_sms_mac_dinh"
                                            name="gui_sms_mac_dinh" value="1" <?php echo e(old('gui_sms_mac_dinh') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="gui_sms_mac_dinh">
                                            Gửi SMS mặc định
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kich_hoat" name="kich_hoat"
                                            value="1" <?php echo e(old('kich_hoat', true) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="kich_hoat">
                                            Kích hoạt mẫu này
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ghi_chu" name="ghi_chu"
                                rows="3" placeholder="Ghi chú về mẫu thông báo..."><?php echo e(old('ghi_chu')); ?></textarea>
                            <?php $__errorArgs = ['ghi_chu'];
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu mẫu thông báo
                            </button>
                            <a href="<?php echo e(route('admin.mau-thong-bao.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/mau-thong-bao/create.blade.php ENDPATH**/ ?>