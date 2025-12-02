<?php $__env->startSection('title', 'Sửa Lớp học phần'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Lớp học phần</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Lớp học phần</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.lop-hoc-phan.update', $lopHocPhan->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <!-- Mã lớp học phần -->
                            <div class="col-md-6 mb-3">
                                <label for="ma_lop_hp" class="form-label">Mã lớp học phần <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ma_lop_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ma_lop_hp" name="ma_lop_hp" value="<?php echo e(old('ma_lop_hp', $lopHocPhan->ma_lop_hp)); ?>"
                                    placeholder="VD: CNTT101.01">
                                <?php $__errorArgs = ['ma_lop_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">Mã lớp học phần phải duy nhất</small>
                            </div>

                            <!-- Tên lớp học phần -->
                            <div class="col-md-6 mb-3">
                                <label for="ten_lop_hp" class="form-label">Tên lớp học phần <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ten_lop_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ten_lop_hp" name="ten_lop_hp"
                                    value="<?php echo e(old('ten_lop_hp', $lopHocPhan->ten_lop_hp)); ?>"
                                    placeholder="VD: Lập trình web - Nhóm 1">
                                <?php $__errorArgs = ['ten_lop_hp'];
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

                        <div class="row">
                            <!-- Môn học -->
                            <div class="col-md-6 mb-3">
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
                                    name="mon_hoc_id">
                                    <option value="">-- Chọn môn học --</option>
                                    <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($monHoc->id); ?>"
                                            <?php echo e(old('mon_hoc_id', $lopHocPhan->mon_hoc_id) == $monHoc->id ? 'selected' : ''); ?>>
                                            <?php echo e($monHoc->ma_mon); ?> - <?php echo e($monHoc->ten_mon); ?>

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

                            <!-- Học kỳ -->
                            <div class="col-md-6 mb-3">
                                <label for="hoc_ky_id" class="form-label">Học kỳ <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['hoc_ky_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="hoc_ky_id"
                                    name="hoc_ky_id">
                                    <option value="">-- Chọn học kỳ --</option>
                                    <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($hocKy->id); ?>"
                                            <?php echo e(old('hoc_ky_id', $lopHocPhan->hoc_ky_id) == $hocKy->id ? 'selected' : ''); ?>>
                                            <?php echo e($hocKy->ten_hoc_ky); ?> - <?php echo e($hocKy->nam_hoc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['hoc_ky_id'];
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

                        <div class="row">
                            <!-- Nhóm lớp -->
                            <div class="col-md-3 mb-3">
                                <label for="nhom_lop" class="form-label">Nhóm lớp</label>
                                <input type="number" class="form-control <?php $__errorArgs = ['nhom_lop'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="nhom_lop" name="nhom_lop" value="<?php echo e(old('nhom_lop', $lopHocPhan->nhom_lop)); ?>"
                                    min="1">
                                <?php $__errorArgs = ['nhom_lop'];
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

                            <!-- Sức chứa -->
                            <div class="col-md-3 mb-3">
                                <label for="suc_chua" class="form-label">Sức chứa (SV tối đa) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control <?php $__errorArgs = ['suc_chua'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="suc_chua" name="suc_chua" value="<?php echo e(old('suc_chua', $lopHocPhan->suc_chua)); ?>"
                                    min="10" max="100">
                                <?php $__errorArgs = ['suc_chua'];
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

                            <!-- Số lượng tối thiểu -->
                            <div class="col-md-3 mb-3">
                                <label for="so_luong_toi_thieu" class="form-label">SV tối thiểu <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control <?php $__errorArgs = ['so_luong_toi_thieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="so_luong_toi_thieu" name="so_luong_toi_thieu"
                                    value="<?php echo e(old('so_luong_toi_thieu', $lopHocPhan->so_luong_toi_thieu)); ?>" min="5"
                                    max="30">
                                <?php $__errorArgs = ['so_luong_toi_thieu'];
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

                            <!-- Hình thức học -->
                            <div class="col-md-3 mb-3">
                                <label for="hinh_thuc" class="form-label">Hình thức học <span
                                        class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['hinh_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="hinh_thuc"
                                    name="hinh_thuc">
                                    <option value="offline"
                                        <?php echo e(old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'offline' ? 'selected' : ''); ?>>
                                        Offline</option>
                                    <option value="online"
                                        <?php echo e(old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'online' ? 'selected' : ''); ?>>Online
                                    </option>
                                    <option value="hybrid"
                                        <?php echo e(old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'hybrid' ? 'selected' : ''); ?>>Hybrid
                                    </option>
                                </select>
                                <?php $__errorArgs = ['hinh_thuc'];
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

                        <!-- Link online (hiện khi chọn online/hybrid) -->
                        <div class="row" id="link_online_group" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label for="link_online" class="form-label">Link học online</label>
                                <input type="url" class="form-control <?php $__errorArgs = ['link_online'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="link_online" name="link_online"
                                    value="<?php echo e(old('link_online', $lopHocPhan->link_online)); ?>"
                                    placeholder="https://meet.google.com/...">
                                <?php $__errorArgs = ['link_online'];
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

                        <div class="row">
                            <!-- Ngày bắt đầu -->
                            <div class="col-md-6 mb-3">
                                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php $__errorArgs = ['ngay_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ngay_bat_dau" name="ngay_bat_dau"
                                    value="<?php echo e(old('ngay_bat_dau', $lopHocPhan->ngay_bat_dau ? $lopHocPhan->ngay_bat_dau->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['ngay_bat_dau'];
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

                            <!-- Ngày kết thúc -->
                            <div class="col-md-6 mb-3">
                                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php $__errorArgs = ['ngay_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="ngay_ket_thuc" name="ngay_ket_thuc"
                                    value="<?php echo e(old('ngay_ket_thuc', $lopHocPhan->ngay_ket_thuc ? $lopHocPhan->ngay_ket_thuc->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['ngay_ket_thuc'];
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

                        <!-- Trạng thái -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="trang_thai_lop" class="form-label">Trạng thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['trang_thai_lop'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="trang_thai_lop" name="trang_thai_lop">
                                    <option value="mo_dang_ky"
                                        <?php echo e(old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'mo_dang_ky' ? 'selected' : ''); ?>>
                                        Mở đăng ký</option>
                                    <option value="dang_hoc"
                                        <?php echo e(old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'dang_hoc' ? 'selected' : ''); ?>>
                                        Đang học</option>
                                    <option value="ket_thuc"
                                        <?php echo e(old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'ket_thuc' ? 'selected' : ''); ?>>
                                        Kết thúc</option>
                                    <option value="huy"
                                        <?php echo e(old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'huy' ? 'selected' : ''); ?>>
                                        Hủy</option>
                                </select>
                                <?php $__errorArgs = ['trang_thai_lop'];
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

                        <!-- Ghi chú -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="ghi_chu" class="form-label">Ghi chú</label>
                                <textarea class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ghi_chu" name="ghi_chu" rows="3"><?php echo e(old('ghi_chu', $lopHocPhan->ghi_chu)); ?></textarea>
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

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Cập nhật
                                </button>
                                <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Hiện/ẩn link online
            document.getElementById('hinh_thuc').addEventListener('change', function() {
                const linkGroup = document.getElementById('link_online_group');
                if (this.value === 'online' || this.value === 'hybrid') {
                    linkGroup.style.display = 'block';
                } else {
                    linkGroup.style.display = 'none';
                }
            });

            // Trigger on page load
            if (document.getElementById('hinh_thuc').value === 'online' || document.getElementById('hinh_thuc').value ===
                'hybrid') {
                document.getElementById('link_online_group').style.display = 'block';
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lop-hoc-phan/edit.blade.php ENDPATH**/ ?>