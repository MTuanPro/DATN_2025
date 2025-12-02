<?php $__env->startSection('title', 'Thêm môn học vào CTĐT'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm môn học vào CTĐT</h3>
                    <p class="text-subtitle text-muted"><?php echo e($chuyenNganh->nganh->khoa->ten_khoa); ?> -
                        <?php echo e($chuyenNganh->nganh->ten_nganh); ?> - <?php echo e($chuyenNganh->ten_chuyen_nganh); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.chuong-trinh-khung.index')); ?>">Chương
                                    trình khung</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm môn học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin môn học trong CTĐT</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.chuong-trinh-khung.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="chuyen_nganh_id" value="<?php echo e($chuyenNganh->id); ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="mon_hoc_id" class="form-label">Môn học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['mon_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="mon_hoc_id"
                                        name="mon_hoc_id" required>
                                        <option value="">-- Chọn môn học --</option>
                                        <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($mon->id); ?>"
                                                <?php echo e(old('mon_hoc_id') == $mon->id ? 'selected' : ''); ?>>
                                                <?php echo e($mon->ma_mon); ?> - <?php echo e($mon->ten_mon); ?> (<?php echo e($mon->so_tin_chi); ?> TC)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['mon_hoc_id'];
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
                                    <label for="hoc_ky_goi_y" class="form-label">Học kỳ gợi ý <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['hoc_ky_goi_y'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="hoc_ky_goi_y" name="hoc_ky_goi_y" required>
                                        <option value="">-- Chọn học kỳ --</option>
                                        <?php for($i = 1; $i <= 8; $i++): ?>
                                            <option value="<?php echo e($i); ?>"
                                                <?php echo e(old('hoc_ky_goi_y') == $i ? 'selected' : ''); ?>>
                                                Học kỳ <?php echo e($i); ?>

                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <?php $__errorArgs = ['hoc_ky_goi_y'];
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
                                    <label for="loai_mon_hoc" class="form-label">Loại môn học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['loai_mon_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="loai_mon_hoc" name="loai_mon_hoc" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="dai_cuong"
                                            <?php echo e(old('loai_mon_hoc') == 'dai_cuong' ? 'selected' : ''); ?>>Đại cương</option>
                                        <option value="co_so_nganh"
                                            <?php echo e(old('loai_mon_hoc') == 'co_so_nganh' ? 'selected' : ''); ?>>Cơ sở ngành
                                        </option>
                                        <option value="chuyen_nganh_bat_buoc"
                                            <?php echo e(old('loai_mon_hoc') == 'chuyen_nganh_bat_buoc' ? 'selected' : ''); ?>>Chuyên
                                            ngành bắt buộc</option>
                                        <option value="chuyen_nganh_tu_chon"
                                            <?php echo e(old('loai_mon_hoc') == 'chuyen_nganh_tu_chon' ? 'selected' : ''); ?>>Chuyên
                                            ngành tự chọn</option>
                                        <option value="thuc_tap" <?php echo e(old('loai_mon_hoc') == 'thuc_tap' ? 'selected' : ''); ?>>
                                            Thực tập</option>
                                        <option value="do_an_tot_nghiep"
                                            <?php echo e(old('loai_mon_hoc') == 'do_an_tot_nghiep' ? 'selected' : ''); ?>>Đồ án tốt
                                            nghiệp</option>
                                    </select>
                                    <?php $__errorArgs = ['loai_mon_hoc'];
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
                                    <label for="bat_buoc" class="form-label">Bắt buộc/Tự chọn <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['bat_buoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="bat_buoc"
                                        name="bat_buoc" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="1" <?php echo e(old('bat_buoc') == '1' ? 'selected' : ''); ?>>Bắt buộc
                                        </option>
                                        <option value="0" <?php echo e(old('bat_buoc') == '0' ? 'selected' : ''); ?>>Tự chọn
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['bat_buoc'];
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
                                    <label for="thu_tu_hoc" class="form-label">Thứ tự học</label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['thu_tu_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="thu_tu_hoc" name="thu_tu_hoc" value="<?php echo e(old('thu_tu_hoc')); ?>" min="1"
                                        placeholder="VD: 1, 2, 3...">
                                    <?php $__errorArgs = ['thu_tu_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Thứ tự ưu tiên học trong học kỳ</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_toi_thieu" class="form-label">Số tín chỉ tối thiểu</label>
                                    <input type="number"
                                        class="form-control <?php $__errorArgs = ['so_tin_chi_toi_thieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_tin_chi_toi_thieu" name="so_tin_chi_toi_thieu"
                                        value="<?php echo e(old('so_tin_chi_toi_thieu')); ?>" min="1">
                                    <?php $__errorArgs = ['so_tin_chi_toi_thieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Chỉ áp dụng cho nhóm môn tự chọn</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                                    <textarea class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ghi_chu" name="ghi_chu" rows="3"
                                        placeholder="Nhập ghi chú..."><?php echo e(old('ghi_chu')); ?></textarea>
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
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu
                            </button>
                            <a href="<?php echo e(route('dao-tao.chuong-trinh-khung.index', ['chuyen_nganh_id' => $chuyenNganh->id])); ?>"
                                class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/chuong-trinh-khung/create.blade.php ENDPATH**/ ?>