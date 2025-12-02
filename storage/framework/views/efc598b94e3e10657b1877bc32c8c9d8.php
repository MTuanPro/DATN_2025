<?php $__env->startSection('title', 'Chỉnh sửa lịch học cố định'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa lịch học cố định</h3>
                    <p class="text-subtitle text-muted"><?php echo e($lichCoDinh->lopHocPhan->ma_lop_hp); ?> -
                        <?php echo e($lichCoDinh->lopHocPhan->monHoc->ten_mon); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id)); ?>">Lịch
                                    cố định</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin lịch học</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.lich-co-dinh.update', $lichCoDinh)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="thu_trong_tuan">Thứ <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['thu_trong_tuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="thu_trong_tuan" name="thu_trong_tuan" required>
                                        <option value="">-- Chọn thứ --</option>
                                        <option value="2"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 2 ? 'selected' : ''); ?>>
                                            Thứ 2</option>
                                        <option value="3"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 3 ? 'selected' : ''); ?>>
                                            Thứ 3</option>
                                        <option value="4"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 4 ? 'selected' : ''); ?>>
                                            Thứ 4</option>
                                        <option value="5"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 5 ? 'selected' : ''); ?>>
                                            Thứ 5</option>
                                        <option value="6"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 6 ? 'selected' : ''); ?>>
                                            Thứ 6</option>
                                        <option value="7"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 7 ? 'selected' : ''); ?>>
                                            Thứ 7</option>
                                        <option value="8"
                                            <?php echo e(old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 8 ? 'selected' : ''); ?>>
                                            Chủ nhật</option>
                                    </select>
                                    <?php $__errorArgs = ['thu_trong_tuan'];
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
                                <div class="form-group">
                                    <label for="ca_hoc_id">Ca học <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['ca_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ca_hoc_id"
                                        name="ca_hoc_id" required>
                                        <option value="">-- Chọn ca học --</option>
                                        <?php $__currentLoopData = $caHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($caHoc->id); ?>"
                                                <?php echo e(old('ca_hoc_id', $lichCoDinh->ca_hoc_id) == $caHoc->id ? 'selected' : ''); ?>

                                                data-tiet-bat-dau="<?php echo e($caHoc->tiet_bat_dau); ?>"
                                                data-tiet-ket-thuc="<?php echo e($caHoc->tiet_ket_thuc); ?>"
                                                data-gio-bat-dau="<?php echo e(\Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i')); ?>"
                                                data-gio-ket-thuc="<?php echo e(\Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i')); ?>">
                                                <?php echo e($caHoc->ten_ca); ?> (<?php echo e(\Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i')); ?> -
                                                <?php echo e(\Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i')); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['ca_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle"></i> Thông tin tiết và giờ sẽ được tự động điền từ ca học
                                    </small>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mb-3" id="caHocInfo" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                        <div class="row">
                                        <div class="col-md-3">
                                            <strong>Tiết:</strong> <span id="displayTiet">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Giờ:</strong> <span id="displayGio">-</span>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phong_hoc_id">Phòng học <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['phong_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="phong_hoc_id" name="phong_hoc_id" required>
                                        <option value="">-- Chọn phòng học --</option>
                                        <?php $__currentLoopData = $phongHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phongHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($phongHoc->id); ?>"
                                                <?php echo e(old('phong_hoc_id', $lichCoDinh->phong_hoc_id) == $phongHoc->id ? 'selected' : ''); ?>>
                                                <?php echo e($phongHoc->ten_phong); ?> (<?php echo e($phongHoc->suc_chua); ?> chỗ)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['phong_hoc_id'];
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
                                <div class="form-group">
                                    <label for="giang_vien_id">Giảng viên <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['giang_vien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="giang_vien_id" name="giang_vien_id" required>
                                        <option value="">-- Chọn giảng viên --</option>
                                        <?php $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $giangVien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($giangVien->id); ?>"
                                                <?php echo e(old('giang_vien_id', $lichCoDinh->giang_vien_id) == $giangVien->id ? 'selected' : ''); ?>>
                                                <?php echo e($giangVien->ho_ten); ?>

                                                <?php if(isset($giangVienChinhId) && $giangVien->id == $giangVienChinhId): ?>
                                                    (Giảng viên chính)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['giang_vien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php if(isset($giangVienChinhId) && $lichCoDinh->giang_vien_id != $giangVienChinhId): ?>
                                        <?php
                                            $giangVienChinh = $lichCoDinh->lopHocPhan->giangVienChinh;
                                        ?>
                                        <small class="form-text text-info">
                                            <i class="bi bi-info-circle"></i> Giảng viên chính của lớp: 
                                            <?php if($giangVienChinh): ?>
                                                <strong><?php echo e($giangVienChinh->giangVien->ho_ten); ?></strong>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hinh_thuc">Hình thức <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['hinh_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="hinh_thuc"
                                        name="hinh_thuc" required>
                                        <option value="offline"
                                            <?php echo e(old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'offline' ? 'selected' : ''); ?>>
                                            Offline</option>
                                        <option value="online"
                                            <?php echo e(old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'online' ? 'selected' : ''); ?>>
                                            Online</option>
                                        <option value="hybrid"
                                            <?php echo e(old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'hybrid' ? 'selected' : ''); ?>>
                                            Hybrid</option>
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_online">Link Online</label>
                                    <input type="url" class="form-control <?php $__errorArgs = ['link_online'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="link_online" name="link_online"
                                        value="<?php echo e(old('link_online', $lichCoDinh->link_online)); ?>"
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
                        </div>

                        <div class="form-group">
                            <label for="ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"><?php echo e(old('ghi_chu', $lichCoDinh->ghi_chu)); ?></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id)); ?>"
                                class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const caHocSelect = document.getElementById('ca_hoc_id');
                const caHocInfo = document.getElementById('caHocInfo');
                const displayTiet = document.getElementById('displayTiet');
                const displayGio = document.getElementById('displayGio');

                function updateCaHocInfo() {
                    const selectedOption = caHocSelect.options[caHocSelect.selectedIndex];
                    if (selectedOption.value) {
                        const tietBatDau = selectedOption.getAttribute('data-tiet-bat-dau');
                        const tietKetThuc = selectedOption.getAttribute('data-tiet-ket-thuc');
                        const gioBatDau = selectedOption.getAttribute('data-gio-bat-dau');
                        const gioKetThuc = selectedOption.getAttribute('data-gio-ket-thuc');

                        displayTiet.textContent = `${tietBatDau} - ${tietKetThuc}`;
                        displayGio.textContent = `${gioBatDau} - ${gioKetThuc}`;
                        caHocInfo.style.display = 'block';
                    } else {
                        caHocInfo.style.display = 'none';
                    }
                }

                // Cập nhật khi chọn ca học
                caHocSelect.addEventListener('change', updateCaHocInfo);

                // Cập nhật khi trang load (nếu đã có ca học được chọn)
                updateCaHocInfo();
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-hoc-co-dinh/edit.blade.php ENDPATH**/ ?>