<?php $__env->startSection('title', 'Thêm Ca học mới'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Ca học mới</h3>
                    <p class="text-subtitle text-muted">Thiết lập thời gian học cho ca mới</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.ca-hoc.index')); ?>">Ca học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra!</h5>
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle"></i> Thông tin Ca học
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.ca-hoc.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten_ca" class="form-label">
                                        Tên ca học <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['ten_ca'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ten_ca" 
                                           name="ten_ca" 
                                           value="<?php echo e(old('ten_ca')); ?>"
                                           placeholder="VD: Ca 1, Ca sáng..."
                                           required>
                                    <?php $__errorArgs = ['ten_ca'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Nhập tên gọi của ca học</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="thu_tu" class="form-label">
                                        Thứ tự <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['thu_tu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="thu_tu" 
                                           name="thu_tu" 
                                           value="<?php echo e(old('thu_tu')); ?>"
                                           min="1"
                                           max="20"
                                           placeholder="VD: 1, 2, 3..."
                                           required>
                                    <?php $__errorArgs = ['thu_tu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Thứ tự ca học trong ngày (từ 1-20)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gio_bat_dau" class="form-label">
                                        Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" 
                                           class="form-control <?php $__errorArgs = ['gio_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="gio_bat_dau" 
                                           name="gio_bat_dau" 
                                           value="<?php echo e(old('gio_bat_dau')); ?>"
                                           required>
                                    <?php $__errorArgs = ['gio_bat_dau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Thời gian bắt đầu ca học</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gio_ket_thuc" class="form-label">
                                        Giờ kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" 
                                           class="form-control <?php $__errorArgs = ['gio_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="gio_ket_thuc" 
                                           name="gio_ket_thuc" 
                                           value="<?php echo e(old('gio_ket_thuc')); ?>"
                                           required>
                                    <?php $__errorArgs = ['gio_ket_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Thời gian kết thúc ca học</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="trang_thai" 
                                               name="trang_thai"
                                               <?php echo e(old('trang_thai', true) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="trang_thai">
                                            <strong>Trạng thái hoạt động</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Bật để ca học có thể được sử dụng trong hệ thống
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                                    <textarea class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="ghi_chu" 
                                              name="ghi_chu" 
                                              rows="3"
                                              placeholder="Nhập ghi chú về ca học (không bắt buộc)"><?php echo e(old('ghi_chu')); ?></textarea>
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
                                    <small class="form-text text-muted">VD: Ca học buổi sáng - Tiết 1,2</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Lưu ý quan trọng:</strong> 
                            <p class="mb-2">Các ca học không được trùng khoảng thời gian với nhau. Ví dụ: nếu Ca 1 là 07:00-09:00, thì Ca 2 phải bắt đầu sau 09:00 (ví dụ: 09:05-11:05).</p>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Gợi ý thời gian:</strong> 
                            <ul class="mb-0 mt-2">
                                <li>Ca 1: 07:00 - 08:50 (Tiết 1,2) - Buổi sáng</li>
                                <li>Ca 2: 09:00 - 10:50 (Tiết 3,4) - Buổi sáng</li>
                                <li>Ca 3: 11:00 - 12:50 (Tiết 5,6) - Buổi sáng</li>
                                <li>Ca 4: 13:00 - 14:50 (Tiết 7,8) - Buổi chiều</li>
                                <li>Ca 5: 15:00 - 16:50 (Tiết 9,10) - Buổi chiều</li>
                                <li>Ca 6: 17:00 - 18:50 (Tiết 11,12) - Buổi chiều</li>
                            </ul>
                        </div>

                        <?php
                            $existingCaHoc = \App\Models\CaHoc::orderBy('gio_bat_dau')->get();
                        ?>
                        <?php if($existingCaHoc->count() > 0): ?>
                        <div class="alert alert-light border">
                            <strong><i class="bi bi-clock-history"></i> Các ca học hiện có:</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $existingCaHoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <strong><?php echo e($ca->ten_ca); ?></strong>: 
                                    <?php echo e(date('H:i', strtotime($ca->gio_bat_dau))); ?> - <?php echo e(date('H:i', strtotime($ca->gio_ket_thuc))); ?>

                                    <?php if(!$ca->trang_thai): ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('dao-tao.ca-hoc.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Lưu lại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Tính toán và hiển thị khoảng thời gian khi thay đổi giờ
    const gioBatDau = document.getElementById('gio_bat_dau');
    const gioKetThuc = document.getElementById('gio_ket_thuc');

    function calculateDuration() {
        if (gioBatDau.value && gioKetThuc.value) {
            const start = new Date('2000-01-01 ' + gioBatDau.value);
            const end = new Date('2000-01-01 ' + gioKetThuc.value);
            const diff = (end - start) / 60000; // minutes
            
            if (diff > 0) {
                console.log('Khoảng thời gian: ' + diff + ' phút');
            }
        }
    }

    gioBatDau.addEventListener('change', calculateDuration);
    gioKetThuc.addEventListener('change', calculateDuration);
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/ca-hoc/create.blade.php ENDPATH**/ ?>