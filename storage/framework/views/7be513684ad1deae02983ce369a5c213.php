<?php $__env->startSection('title', 'Điểm danh sinh viên'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Điểm danh sinh viên</h3>
                <p class="text-subtitle text-muted">
                    <?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?> - <?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?>

                </p>
            </div>
            <a href="<?php echo e(route('giangvien.diem-danh.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin buổi học -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 35%;">Ngày học:</th>
                                    <td><strong class="text-primary"><?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?> (<?php echo e($buoiHoc->ngay_hoc->dayName); ?>)</strong></td>
                                </tr>
                                <tr>
                                    <th>Tiết:</th>
                                    <td>Tiết <?php echo e($buoiHoc->tiet_bat_dau); ?> - <?php echo e($buoiHoc->tiet_ket_thuc); ?></td>
                                </tr>
                                <tr>
                                    <th>Giờ:</th>
                                    <td><?php echo e($buoiHoc->gio_bat_dau); ?> - <?php echo e($buoiHoc->gio_ket_thuc); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 35%;">Phòng học:</th>
                                    <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><strong><?php echo e($sinhViens->count()); ?></strong> sinh viên</td>
                                </tr>
                                <tr>
                                    <th>Thời gian sửa:</th>
                                    <td>
                                        <?php if($coTheSua): ?>
                                            <span class="badge bg-success">Có thể sửa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Đã hết hạn sửa (24h)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form điểm danh -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách sinh viên</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('giangvien.diem-danh.store', $buoiHoc->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Toolbar điểm danh nhanh -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><i class="bi bi-lightbulb"></i> Điểm danh nhanh:</strong>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-success" onclick="checkAll('co_mat')">
                                        <i class="bi bi-check-all"></i> Tất cả có mặt
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="checkAll('vang')">
                                        <i class="bi bi-x-circle"></i> Tất cả vắng
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th style="width: 120px;">Mã SV</th>
                                        <th>Họ và tên</th>
                                        <th style="width: 200px;">Lớp hành chính</th>
                                        <th style="width: 350px;">Trạng thái</th>
                                        <th style="width: 250px;">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $currentStatus = $diemDanhData[$sv->id] ?? 'co_mat';
                                            $currentGhiChu = $diemDanhGhiChu[$sv->id] ?? '';
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($index + 1); ?></td>
                                            <td><strong><?php echo e($sv->sinhVien->ma_sinh_vien); ?></strong></td>
                                            <td><?php echo e($sv->sinhVien->ho_ten); ?></td>
                                            <td><?php echo e($sv->sinhVien->lopHanhChinh->ten_lop ?? 'N/A'); ?></td>
                                            <td>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="diem_danh[<?php echo e($sv->id); ?>]" 
                                                           id="co_mat_<?php echo e($sv->id); ?>" value="co_mat" 
                                                           <?php echo e($currentStatus == 'co_mat' ? 'checked' : ''); ?>>
                                                    <label class="btn btn-outline-success btn-sm" for="co_mat_<?php echo e($sv->id); ?>">
                                                        <i class="bi bi-check-circle"></i> Có mặt
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[<?php echo e($sv->id); ?>]" 
                                                           id="vang_<?php echo e($sv->id); ?>" value="vang"
                                                           <?php echo e($currentStatus == 'vang' ? 'checked' : ''); ?>>
                                                    <label class="btn btn-outline-danger btn-sm" for="vang_<?php echo e($sv->id); ?>">
                                                        <i class="bi bi-x-circle"></i> Vắng
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[<?php echo e($sv->id); ?>]" 
                                                           id="di_tre_<?php echo e($sv->id); ?>" value="di_tre"
                                                           <?php echo e($currentStatus == 'di_tre' ? 'checked' : ''); ?>>
                                                    <label class="btn btn-outline-warning btn-sm" for="di_tre_<?php echo e($sv->id); ?>">
                                                        <i class="bi bi-clock"></i> Đi trễ
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[<?php echo e($sv->id); ?>]" 
                                                           id="nghi_phep_<?php echo e($sv->id); ?>" value="nghi_phep"
                                                           <?php echo e($currentStatus == 'nghi_phep' ? 'checked' : ''); ?>>
                                                    <label class="btn btn-outline-info btn-sm" for="nghi_phep_<?php echo e($sv->id); ?>">
                                                        <i class="bi bi-envelope"></i> Nghỉ phép
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="ghi_chu[<?php echo e($sv->id); ?>]" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Ghi chú..."
                                                       value="<?php echo e($currentGhiChu); ?>"
                                                       maxlength="500">
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Tổng số: <strong><?php echo e($sinhViens->count()); ?></strong> sinh viên
                            </div>
                            <div>
                                <a href="<?php echo e(route('giangvien.diem-danh.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                                <?php if($coTheSua): ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Lưu điểm danh
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function checkAll(status) {
            const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/diem-danh/show.blade.php ENDPATH**/ ?>