<?php $__env->startSection('title', 'Sửa Môn học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Môn học</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin môn học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.mon-hoc.index')); ?>">Môn học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Môn học</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.mon-hoc.update', $monHoc->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ma_mon" class="form-label">Mã môn học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['ma_mon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ma_mon" name="ma_mon" value="<?php echo e(old('ma_mon', $monHoc->ma_mon)); ?>"
                                        placeholder="VD: IT101" required>
                                    <?php $__errorArgs = ['ma_mon'];
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

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ten_mon" class="form-label">Tên môn học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['ten_mon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ten_mon" name="ten_mon" value="<?php echo e(old('ten_mon', $monHoc->ten_mon)); ?>"
                                        placeholder="VD: Lập trình căn bản" required>
                                    <?php $__errorArgs = ['ten_mon'];
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
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi" class="form-label">Tổng số tín chỉ <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['so_tin_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_tin_chi" name="so_tin_chi"
                                        value="<?php echo e(old('so_tin_chi', $monHoc->so_tin_chi)); ?>" min="1" max="5"
                                        required>
                                    <?php $__errorArgs = ['so_tin_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Phải bằng Lý thuyết + Thực hành</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_ly_thuyet" class="form-label">Tín chỉ lý thuyết <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control <?php $__errorArgs = ['so_tin_chi_ly_thuyet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_tin_chi_ly_thuyet" name="so_tin_chi_ly_thuyet"
                                        value="<?php echo e(old('so_tin_chi_ly_thuyet', $monHoc->so_tin_chi_ly_thuyet)); ?>"
                                        min="0" max="5" required>
                                    <?php $__errorArgs = ['so_tin_chi_ly_thuyet'];
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

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_thuc_hanh" class="form-label">Tín chỉ thực hành <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control <?php $__errorArgs = ['so_tin_chi_thuc_hanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_tin_chi_thuc_hanh" name="so_tin_chi_thuc_hanh"
                                        value="<?php echo e(old('so_tin_chi_thuc_hanh', $monHoc->so_tin_chi_thuc_hanh)); ?>"
                                        min="0" max="5" required>
                                    <?php $__errorArgs = ['so_tin_chi_thuc_hanh'];
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
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="loai_mon" class="form-label">Loại môn học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['loai_mon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="loai_mon"
                                        name="loai_mon" required>
                                        <option value="">-- Chọn loại môn --</option>
                                        <option value="dai_cuong"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'dai_cuong' ? 'selected' : ''); ?>>Đại
                                            cương</option>
                                        <option value="co_so_nganh"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'co_so_nganh' ? 'selected' : ''); ?>>Cơ
                                            sở ngành</option>
                                        <option value="chuyen_nganh_bat_buoc"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'chuyen_nganh_bat_buoc' ? 'selected' : ''); ?>>
                                            Chuyên ngành bắt buộc</option>
                                        <option value="chuyen_nganh_tu_chon"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'chuyen_nganh_tu_chon' ? 'selected' : ''); ?>>
                                            Chuyên ngành tự chọn</option>
                                        <option value="thuc_tap"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'thuc_tap' ? 'selected' : ''); ?>>Thực
                                            tập</option>
                                        <option value="do_an_tot_nghiep"
                                            <?php echo e(old('loai_mon', $monHoc->loai_mon) == 'do_an_tot_nghiep' ? 'selected' : ''); ?>>
                                            Đồ án tốt nghiệp</option>
                                    </select>
                                    <?php $__errorArgs = ['loai_mon'];
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

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="khoa_id" class="form-label">Khoa quản lý <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['khoa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="khoa_id"
                                        name="khoa_id" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <?php $__currentLoopData = $khoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $khoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($khoa->id); ?>"
                                                <?php echo e(old('khoa_id', $monHoc->khoa_id) == $khoa->id ? 'selected' : ''); ?>>
                                                <?php echo e($khoa->ten_khoa); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['khoa_id'];
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

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="hinh_thuc_day" class="form-label">Hình thức dạy <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['hinh_thuc_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="hinh_thuc_day" name="hinh_thuc_day" required>
                                        <option value="">-- Chọn hình thức --</option>
                                        <option value="offline"
                                            <?php echo e(old('hinh_thuc_day', $monHoc->hinh_thuc_day) == 'offline' ? 'selected' : ''); ?>>
                                            Offline (Trực tiếp)</option>
                                        <option value="online"
                                            <?php echo e(old('hinh_thuc_day', $monHoc->hinh_thuc_day) == 'online' ? 'selected' : ''); ?>>
                                            Online (Trực tuyến)</option>
                                        <option value="hybrid"
                                            <?php echo e(old('hinh_thuc_day', $monHoc->hinh_thuc_day) == 'hybrid' ? 'selected' : ''); ?>>
                                            Hybrid (Kết hợp)</option>
                                    </select>
                                    <?php $__errorArgs = ['hinh_thuc_day'];
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
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="thoi_luong_hoc" class="form-label">Thời lượng học (giờ)</label>
                                    <input type="number"
                                        class="form-control <?php $__errorArgs = ['thoi_luong_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="thoi_luong_hoc" name="thoi_luong_hoc"
                                        value="<?php echo e(old('thoi_luong_hoc', $monHoc->thoi_luong_hoc)); ?>" min="1">
                                    <?php $__errorArgs = ['thoi_luong_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Tối thiểu = Số tín chỉ × 15</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="so_buoi_hoc" class="form-label">Số buổi học</label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['so_buoi_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_buoi_hoc" name="so_buoi_hoc"
                                        value="<?php echo e(old('so_buoi_hoc', $monHoc->so_buoi_hoc)); ?>" min="1">
                                    <?php $__errorArgs = ['so_buoi_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Số buổi học dự kiến (≥ 10 buổi)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control <?php $__errorArgs = ['mo_ta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="mo_ta" name="mo_ta" rows="4"
                                        placeholder="Nhập mô tả chi tiết về môn học..."><?php echo e(old('mo_ta', $monHoc->mo_ta)); ?></textarea>
                                    <?php $__errorArgs = ['mo_ta'];
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="<?php echo e(route('dao-tao.mon-hoc.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/mon-hoc/edit.blade.php ENDPATH**/ ?>