<?php $__env->startSection('title', 'Sửa Lịch thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Sửa Lịch thi</h3>
                <p class="text-subtitle text-muted">Cập nhật thông tin lịch thi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lich-thi.index')); ?>">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if((isset($errors) && is_object($errors) && $errors->any()) || session('validation_errors') || session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> 
            Có lỗi xảy ra:
        </h4>
        <ul class="mb-0">
            <?php if(isset($errors) && is_object($errors)): ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if(session('validation_errors')): ?>
                <?php $__currentLoopData = session('validation_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <li><?php echo e(session('error')); ?></li>
            <?php endif; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Thành công:</h4>
        <p class="mb-0"><?php echo e(session('success')); ?></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('dao-tao.lich-thi.update', $lichThi)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <!-- Form tương tự create.blade.php nhưng có giá trị mặc định -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lop_hoc_phan_id">Lớp học phần <span class="text-danger">*</span></label>
                                <select name="lop_hoc_phan_id" id="lop_hoc_phan_id" class="form-select <?php $__errorArgs = ['lop_hoc_phan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Chọn lớp học phần --</option>
                                    <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lhp->id); ?>" <?php echo e((old('lop_hoc_phan_id') ?? $lichThi->lop_hoc_phan_id) == $lhp->id ? 'selected' : ''); ?>>
                                            <?php echo e($lhp->ma_lop_hp); ?> - <?php echo e($lhp->monHoc->ten_mon); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['lop_hoc_phan_id'];
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
                            <div class="form-group">
                                <label for="loai_thi">Loại thi <span class="text-danger">*</span></label>
                                <select name="loai_thi" id="loai_thi" class="form-select <?php $__errorArgs = ['loai_thi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="giua_ky" <?php echo e((old('loai_thi') ?? $lichThi->loai_thi) == 'giua_ky' ? 'selected' : ''); ?>>Giữa kỳ</option>
                                    <option value="cuoi_ky" <?php echo e((old('loai_thi') ?? $lichThi->loai_thi) == 'cuoi_ky' ? 'selected' : ''); ?>>Cuối kỳ</option>
                                    <option value="thi_lai" <?php echo e((old('loai_thi') ?? $lichThi->loai_thi) == 'thi_lai' ? 'selected' : ''); ?>>Thi lại</option>
                                </select>
                                <?php $__errorArgs = ['loai_thi'];
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ngay_thi">Ngày thi <span class="text-danger">*</span></label>
                                <input type="date" name="ngay_thi" id="ngay_thi" class="form-control <?php $__errorArgs = ['ngay_thi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('ngay_thi') ?? $lichThi->ngay_thi->format('Y-m-d')); ?>" required>
                                <?php $__errorArgs = ['ngay_thi'];
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
                            <div class="form-group">
                                <label for="ca_hoc_id">Ca thi <span class="text-danger">*</span></label>
                                <select name="ca_hoc_id" id="ca_hoc_id" class="form-select <?php $__errorArgs = ['ca_hoc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Chọn ca thi --</option>
                                    <?php $__currentLoopData = $caHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($caHoc->id); ?>" 
                                                <?php echo e((old('ca_hoc_id') ?? $lichThi->ca_hoc_id) == $caHoc->id ? 'selected' : ''); ?>

                                                data-gio-bat-dau="<?php echo e($caHoc->gio_bat_dau); ?>"
                                                data-gio-ket-thuc="<?php echo e($caHoc->gio_ket_thuc); ?>">
                                            <?php echo e($caHoc->ten_ca); ?> (<?php echo e($caHoc->getFormattedTimeRange()); ?>)
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
                                <small class="form-text text-muted" id="ca_hoc_preview"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="phong_thi_id">Phòng thi</label>
                                <select name="phong_thi_id" id="phong_thi_id" class="form-select <?php $__errorArgs = ['phong_thi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__currentLoopData = $phongHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($phong->id); ?>" <?php echo e((old('phong_thi_id') ?? $lichThi->phong_thi_id) == $phong->id ? 'selected' : ''); ?>>
                                            <?php echo e($phong->ten_phong); ?> (<?php echo e($phong->suc_chua); ?> chỗ)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['phong_thi_id'];
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
                            <div class="form-group">
                                <label for="so_sinh_vien_du_thi">SL dự thi</label>
                                <input type="number" name="so_sinh_vien_du_thi" id="so_sinh_vien_du_thi" 
                                       class="form-control <?php $__errorArgs = ['so_sinh_vien_du_thi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('so_sinh_vien_du_thi') ?? $lichThi->so_sinh_vien_du_thi); ?>" min="0">
                                <?php $__errorArgs = ['so_sinh_vien_du_thi'];
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
                            <div class="form-group">
                                <label for="giam_thi_1_id">Giám thị 1</label>
                                <select name="giam_thi_1_id" id="giam_thi_1_id" class="form-select <?php $__errorArgs = ['giam_thi_1_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Chọn giám thị 1 --</option>
                                    <?php $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gv->id); ?>" <?php echo e((old('giam_thi_1_id') ?? $lichThi->giam_thi_1_id) == $gv->id ? 'selected' : ''); ?>>
                                            <?php echo e($gv->ho_ten); ?> - <?php echo e($gv->ma_giang_vien); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['giam_thi_1_id'];
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
                                <label for="giam_thi_2_id">Giám thị 2</label>
                                <select name="giam_thi_2_id" id="giam_thi_2_id" class="form-select <?php $__errorArgs = ['giam_thi_2_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Chọn giám thị 2 --</option>
                                    <?php $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gv->id); ?>" <?php echo e((old('giam_thi_2_id') ?? $lichThi->giam_thi_2_id) == $gv->id ? 'selected' : ''); ?>>
                                            <?php echo e($gv->ho_ten); ?> - <?php echo e($gv->ma_giang_vien); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['giam_thi_2_id'];
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
                            <div class="form-group">
                                <label for="hinh_thuc">Hình thức thi <span class="text-danger">*</span></label>
                                <select name="hinh_thuc" id="hinh_thuc" class="form-select <?php $__errorArgs = ['hinh_thuc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="offline" <?php echo e((old('hinh_thuc') ?? $lichThi->hinh_thuc) == 'offline' ? 'selected' : ''); ?>>Thi tại trường</option>
                                    <option value="online" <?php echo e((old('hinh_thuc') ?? $lichThi->hinh_thuc) == 'online' ? 'selected' : ''); ?>>Thi trực tuyến</option>
                                    <option value="hybrid" <?php echo e((old('hinh_thuc') ?? $lichThi->hinh_thuc) == 'hybrid' ? 'selected' : ''); ?>>Kết hợp</option>
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
                                <label for="link_online">Link thi online</label>
                                <input type="url" name="link_online" id="link_online" 
                                       class="form-control <?php $__errorArgs = ['link_online'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('link_online') ?? $lichThi->link_online); ?>" placeholder="https://...">
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="de_thi_file">Đề thi (PDF, DOC, DOCX - Max 10MB)</label>
                                <?php if($lichThi->de_thi_file): ?>
                                    <p class="text-muted small">File hiện tại: <?php echo e(basename($lichThi->de_thi_file)); ?></p>
                                <?php endif; ?>
                                <input type="file" name="de_thi_file" id="de_thi_file" class="form-control <?php $__errorArgs = ['de_thi_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.doc,.docx">
                                <?php $__errorArgs = ['de_thi_file'];
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
                                <label for="dap_an_file">Đáp án (PDF, DOC, DOCX - Max 10MB)</label>
                                <?php if($lichThi->dap_an_file): ?>
                                    <p class="text-muted small">File hiện tại: <?php echo e(basename($lichThi->dap_an_file)); ?></p>
                                <?php endif; ?>
                                <input type="file" name="dap_an_file" id="dap_an_file" class="form-control <?php $__errorArgs = ['dap_an_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.doc,.docx">
                                <?php $__errorArgs = ['dap_an_file'];
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
                        <textarea name="ghi_chu" id="ghi_chu" rows="3" class="form-control <?php $__errorArgs = ['ghi_chu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  placeholder="Ghi chú về lịch thi..."><?php echo e(old('ghi_chu') ?? $lichThi->ghi_chu); ?></textarea>
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

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật
                        </button>
                        <a href="<?php echo e(route('dao-tao.lich-thi.index')); ?>" class="btn btn-secondary">
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
    // Hiển thị thông tin ca học khi chọn
    document.getElementById('ca_hoc_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const preview = document.getElementById('ca_hoc_preview');
        
        if (selectedOption.value) {
            const gioBatDau = selectedOption.getAttribute('data-gio-bat-dau');
            const gioKetThuc = selectedOption.getAttribute('data-gio-ket-thuc');
            preview.textContent = `Giờ thi: ${gioBatDau} - ${gioKetThuc}`;
            preview.style.color = '#28a745';
        } else {
            preview.textContent = '';
        }
    });
    
    // Ẩn/hiện link online dựa vào hình thức thi
    document.getElementById('hinh_thuc').addEventListener('change', function() {
        const linkOnlineDiv = document.getElementById('link_online').closest('.col-md-6');
        const linkOnlineInput = document.getElementById('link_online');
        
        if (this.value === 'offline') {
            linkOnlineDiv.style.display = 'none';
            linkOnlineInput.value = ''; // Xóa giá trị khi ẩn
            linkOnlineInput.removeAttribute('required');
        } else {
            linkOnlineDiv.style.display = 'block';
            if (this.value === 'online') {
                linkOnlineInput.setAttribute('required', 'required');
            } else {
                linkOnlineInput.removeAttribute('required');
            }
        }
    });
    
    // Trigger khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('ca_hoc_id').dispatchEvent(new Event('change'));
        document.getElementById('hinh_thuc').dispatchEvent(new Event('change'));
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-thi/edit.blade.php ENDPATH**/ ?>