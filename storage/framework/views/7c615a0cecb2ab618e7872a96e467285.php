<?php $__env->startSection('title', 'Quản lý Môn tiên quyết'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Môn tiên quyết</h3>
                    <p class="text-subtitle text-muted"><?php echo e($monHoc->ma_mon); ?> - <?php echo e($monHoc->ten_mon); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.mon-hoc.index')); ?>">Môn học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Môn tiên quyết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thêm môn tiên quyết -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Thêm Môn tiên quyết</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('dao-tao.mon-hoc.tien-quyet.store', $monHoc->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mon_tien_quyet_id" class="form-label">Môn tiên quyết <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['mon_tien_quyet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="mon_tien_quyet_id" name="mon_tien_quyet_id" required>
                                        <option value="">-- Chọn môn tiên quyết --</option>
                                        <?php $__currentLoopData = $danhSachMonHoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($mon->id); ?>"
                                                <?php echo e(old('mon_tien_quyet_id') == $mon->id ? 'selected' : ''); ?>>
                                                <?php echo e($mon->ma_mon); ?> - <?php echo e($mon->ten_mon); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['mon_tien_quyet_id'];
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

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="loai_tien_quyet" class="form-label">Loại tiên quyết <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['loai_tien_quyet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="loai_tien_quyet" name="loai_tien_quyet" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="bat_buoc"
                                            <?php echo e(old('loai_tien_quyet') == 'bat_buoc' ? 'selected' : ''); ?>>Bắt buộc</option>
                                        <option value="khuyen_nghi"
                                            <?php echo e(old('loai_tien_quyet') == 'khuyen_nghi' ? 'selected' : ''); ?>>Khuyến nghị
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['loai_tien_quyet'];
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

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="dieu_kien_qua_mon" class="form-label">Điều kiện qua môn <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['dieu_kien_qua_mon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="dieu_kien_qua_mon" name="dieu_kien_qua_mon" required>
                                        <option value="1" <?php echo e(old('dieu_kien_qua_mon') == '1' ? 'selected' : ''); ?>>Phải
                                            qua môn</option>
                                        <option value="0" <?php echo e(old('dieu_kien_qua_mon') == '0' ? 'selected' : ''); ?>>
                                            Không yêu cầu</option>
                                    </select>
                                    <?php $__errorArgs = ['dieu_kien_qua_mon'];
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

                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle"></i> Thêm
                                    </button>
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
unset($__errorArgs, $__bag); ?>" id="ghi_chu" name="ghi_chu" rows="2"
                                        placeholder="Ghi chú về môn tiên quyết..."><?php echo e(old('ghi_chu')); ?></textarea>
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
                    </form>
                </div>
            </div>

            <!-- Danh sách môn tiên quyết hiện tại -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Danh sách Môn tiên quyết của <?php echo e($monHoc->ma_mon); ?></h5>
                </div>
                <div class="card-body">
                    <?php if($monHoc->monTienQuyet->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn</th>
                                        <th>Tín chỉ</th>
                                        <th>Loại tiên quyết</th>
                                        <th>Điều kiện</th>
                                        <th>Ghi chú</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $monHoc->monTienQuyet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tienQuyet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><strong><?php echo e($tienQuyet->ma_mon); ?></strong></td>
                                            <td><?php echo e($tienQuyet->ten_mon); ?></td>
                                            <td><span class="badge bg-primary"><?php echo e($tienQuyet->so_tin_chi); ?> TC</span></td>
                                            <td>
                                                <form
                                                    action="<?php echo e(route('dao-tao.mon-hoc.tien-quyet.update', [$monHoc->id, $tienQuyet->pivot->id])); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <select name="loai_tien_quyet" class="form-select form-select-sm"
                                                        onchange="this.form.submit()">
                                                        <option value="bat_buoc"
                                                            <?php echo e($tienQuyet->pivot->loai_tien_quyet == 'bat_buoc' ? 'selected' : ''); ?>>
                                                            Bắt buộc
                                                        </option>
                                                        <option value="khuyen_nghi"
                                                            <?php echo e($tienQuyet->pivot->loai_tien_quyet == 'khuyen_nghi' ? 'selected' : ''); ?>>
                                                            Khuyến nghị
                                                        </option>
                                                    </select>
                                                    <input type="hidden" name="dieu_kien_qua_mon"
                                                        value="<?php echo e($tienQuyet->pivot->dieu_kien_qua_mon ? '1' : '0'); ?>">
                                                    <input type="hidden" name="ghi_chu"
                                                        value="<?php echo e($tienQuyet->pivot->ghi_chu); ?>">
                                                </form>
                                            </td>
                                            <td>
                                                <form
                                                    action="<?php echo e(route('dao-tao.mon-hoc.tien-quyet.update', [$monHoc->id, $tienQuyet->pivot->id])); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <select name="dieu_kien_qua_mon" class="form-select form-select-sm"
                                                        onchange="this.form.submit()">
                                                        <option value="1"
                                                            <?php echo e($tienQuyet->pivot->dieu_kien_qua_mon ? 'selected' : ''); ?>>
                                                            Phải qua môn
                                                        </option>
                                                        <option value="0"
                                                            <?php echo e(!$tienQuyet->pivot->dieu_kien_qua_mon ? 'selected' : ''); ?>>
                                                            Không yêu cầu
                                                        </option>
                                                    </select>
                                                    <input type="hidden" name="loai_tien_quyet"
                                                        value="<?php echo e($tienQuyet->pivot->loai_tien_quyet); ?>">
                                                    <input type="hidden" name="ghi_chu"
                                                        value="<?php echo e($tienQuyet->pivot->ghi_chu); ?>">
                                                </form>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo e($tienQuyet->pivot->ghi_chu ?? '-'); ?></small>
                                            </td>
                                            <td>
                                                <form
                                                    action="<?php echo e(route('dao-tao.mon-hoc.tien-quyet.destroy', [$monHoc->id, $tienQuyet->pivot->id])); ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa môn tiên quyết này?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có môn tiên quyết nào cho môn học này.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Danh sách các môn cần môn này làm tiên quyết -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">Các môn học cần <?php echo e($monHoc->ma_mon); ?> làm tiên quyết</h5>
                </div>
                <div class="card-body">
                    <?php if($monHoc->monCanMonNay->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn</th>
                                        <th>Tín chỉ</th>
                                        <th>Loại tiên quyết</th>
                                        <th>Điều kiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $monHoc->monCanMonNay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><strong><?php echo e($mon->ma_mon); ?></strong></td>
                                            <td><?php echo e($mon->ten_mon); ?></td>
                                            <td><span class="badge bg-primary"><?php echo e($mon->so_tin_chi); ?> TC</span></td>
                                            <td>
                                                <?php if($mon->pivot->loai_tien_quyet == 'bat_buoc'): ?>
                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Khuyến nghị</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($mon->pivot->dieu_kien_qua_mon): ?>
                                                    <span class="badge bg-success">Phải qua môn</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Không yêu cầu</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có môn học nào cần môn này làm tiên quyết.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nút quay lại -->
            <div class="mb-3">
                <a href="<?php echo e(route('dao-tao.mon-hoc.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/mon-hoc/tien-quyet.blade.php ENDPATH**/ ?>