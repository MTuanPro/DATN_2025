<?php $__env->startSection('title', 'Cập nhật buổi học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Cập nhật buổi học</h3>
                <p class="text-subtitle text-muted">
                    <?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?> - <?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?>

                </p>
            </div>
            <a href="<?php echo e(route('giangvien.buoi-hoc.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin buổi học -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Ngày học:</th>
                                    <td><strong><?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?> (<?php echo e($buoiHoc->ngay_hoc->dayName); ?>)</strong></td>
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
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Lớp học phần:</th>
                                    <td><?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?></td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td><?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Phòng học:</th>
                                    <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form cập nhật -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cập nhật thông tin</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

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

                    <form action="<?php echo e(route('giangvien.buoi-hoc.update', $buoiHoc->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="trang_thai" id="trang_thai" class="form-select <?php $__errorArgs = ['trang_thai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="chua_day" <?php echo e(old('trang_thai', $buoiHoc->trang_thai) == 'chua_day' ? 'selected' : ''); ?>>
                                        Chưa dạy
                                    </option>
                                    <option value="dang_day" <?php echo e(old('trang_thai', $buoiHoc->trang_thai) == 'dang_day' ? 'selected' : ''); ?>>
                                        Đang dạy
                                    </option>
                                    <option value="da_day" <?php echo e(old('trang_thai', $buoiHoc->trang_thai) == 'da_day' ? 'selected' : ''); ?>>
                                        Đã dạy
                                    </option>
                                    <option value="huy" <?php echo e(old('trang_thai', $buoiHoc->trang_thai) == 'huy' ? 'selected' : ''); ?>>
                                        Hủy
                                    </option>
                                </select>
                                <?php $__errorArgs = ['trang_thai'];
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

                            <div class="col-md-12 mb-3">
                                <label for="noi_dung_giang_day" class="form-label">Nội dung giảng dạy</label>
                                <textarea name="noi_dung_giang_day" id="noi_dung_giang_day" rows="5" 
                                          class="form-control <?php $__errorArgs = ['noi_dung_giang_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                          placeholder="Nhập nội dung giảng dạy của buổi học..."><?php echo e(old('noi_dung_giang_day', $buoiHoc->noi_dung_giang_day)); ?></textarea>
                                <?php $__errorArgs = ['noi_dung_giang_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">Tối đa 1000 ký tự</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="tai_lieu" class="form-label">Tài liệu đính kèm</label>
                                
                                <?php if($buoiHoc->tai_lieu_dinh_kem): ?>
                                    <div class="alert alert-info mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-earmark-text"></i>
                                                <strong>Tài liệu hiện tại:</strong>
                                                <a href="<?php echo e(route('giangvien.buoi-hoc.download-tai-lieu', $buoiHoc->id)); ?>" 
                                                   class="text-decoration-none" target="_blank">
                                                    <?php echo e(basename($buoiHoc->tai_lieu_dinh_kem)); ?>

                                                </a>
                                            </div>
                                            <form action="<?php echo e(route('giangvien.buoi-hoc.delete-tai-lieu', $buoiHoc->id)); ?>" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="tai_lieu" id="tai_lieu" 
                                       class="form-control <?php $__errorArgs = ['tai_lieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                                <?php $__errorArgs = ['tai_lieu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">
                                    Chấp nhận: PDF, Word, PowerPoint, Excel, ZIP. Tối đa 10MB.
                                </small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="ghi_chu" class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" id="ghi_chu" rows="3" 
                                          class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                          placeholder="Ghi chú thêm về buổi học (nếu có)..."><?php echo e(old('ghi_chu', $buoiHoc->ghi_chu)); ?></textarea>
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
                                <small class="text-muted">Tối đa 500 ký tự</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('giangvien.buoi-hoc.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/buoi-hoc/edit.blade.php ENDPATH**/ ?>