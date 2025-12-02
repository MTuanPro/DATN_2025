<?php $__env->startSection('title', 'Chỉnh sửa thông báo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa thông báo</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.thong-bao.index')); ?>">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('admin.thong-bao.update', $thongBao)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-8">
                                
                                <div class="mb-3">
                                    <label for="tieu_de" class="form-label">Tiêu đề <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['tieu_de'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="tieu_de" name="tieu_de" value="<?php echo e(old('tieu_de', $thongBao->tieu_de)); ?>"
                                        required>
                                    <?php $__errorArgs = ['tieu_de'];
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

                                
                                <div class="mb-3">
                                    <label for="noi_dung" class="form-label">Nội dung <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control <?php $__errorArgs = ['noi_dung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="noi_dung" name="noi_dung" rows="10"
                                        required><?php echo e(old('noi_dung', $thongBao->noi_dung)); ?></textarea>
                                    <?php $__errorArgs = ['noi_dung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Hỗ trợ HTML cơ bản</small>
                                </div>

                                
                                <div class="mb-3">
                                    <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>

                                    <?php if($thongBao->anh_dai_dien): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo e(asset('storage/' . $thongBao->anh_dai_dien)); ?>" alt="Ảnh hiện tại"
                                                class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                            <div class="mt-1">
                                                <small class="text-muted">Ảnh hiện tại</small>
                                                <label class="ms-2">
                                                    <input type="checkbox" name="xoa_anh" value="1"> Xóa ảnh này
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <input type="file" class="form-control <?php $__errorArgs = ['anh_dai_dien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="anh_dai_dien" name="anh_dai_dien" accept="image/*">
                                    <?php $__errorArgs = ['anh_dai_dien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB. Chọn ảnh mới để thay
                                        thế.</small>
                                </div>

                                
                                <div class="mb-3">
                                    <label for="file_dinh_kem" class="form-label">File đính kèm</label>

                                    <?php if($thongBao->file_dinh_kem): ?>
                                        <div class="mb-2 p-2 bg-light rounded">
                                            <i class="bi bi-file-earmark-text text-primary"></i>
                                            <a href="<?php echo e(asset('storage/' . $thongBao->file_dinh_kem)); ?>" target="_blank"
                                                class="text-decoration-none">
                                                <?php echo e(basename($thongBao->file_dinh_kem)); ?>

                                            </a>
                                            <label class="ms-2">
                                                <input type="checkbox" name="xoa_file" value="1"> Xóa file này
                                            </label>
                                        </div>
                                    <?php endif; ?>

                                    <input type="file" class="form-control <?php $__errorArgs = ['file_dinh_kem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="file_dinh_kem" name="file_dinh_kem" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <?php $__errorArgs = ['file_dinh_kem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Định dạng: PDF, DOC, DOCX, XLS, XLSX. Tối đa 10MB. Chọn file
                                        mới để thay thế.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                
                                <div class="mb-3">
                                    <label for="loai_thong_bao" class="form-label">Loại thông báo <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['loai_thong_bao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="loai_thong_bao" name="loai_thong_bao" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="tin_tuc"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'tin_tuc' ? 'selected' : ''); ?>>
                                            Tin tức</option>
                                        <option value="thong_bao_chung"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'thong_bao_chung' ? 'selected' : ''); ?>>
                                            Thông báo
                                            chung</option>
                                        <option value="tin_gap"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'tin_gap' ? 'selected' : ''); ?>>
                                            Tin gấp</option>
                                        <option value="lich_hoc"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'lich_hoc' ? 'selected' : ''); ?>>
                                            Lịch học</option>
                                        <option value="lich_thi"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'lich_thi' ? 'selected' : ''); ?>>
                                            Lịch thi</option>
                                        <option value="hoc_phi"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'hoc_phi' ? 'selected' : ''); ?>>
                                            Học phí</option>
                                        <option value="diem"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'diem' ? 'selected' : ''); ?>>
                                            Điểm
                                        </option>
                                        <option value="dang_ky_mon"
                                            <?php echo e(old('loai_thong_bao', $thongBao->loai_thong_bao) == 'dang_ky_mon' ? 'selected' : ''); ?>>
                                            Đăng ký môn
                                        </option>
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

                                
                                <div class="mb-3">
                                    <label for="muc_do_quan_trong" class="form-label">Mức độ quan trọng <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['muc_do_quan_trong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="muc_do_quan_trong" name="muc_do_quan_trong" required>
                                        <option value="binh_thuong"
                                            <?php echo e(old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'binh_thuong' ? 'selected' : ''); ?>>
                                            Bình thường
                                        </option>
                                        <option value="quan_trong"
                                            <?php echo e(old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'quan_trong' ? 'selected' : ''); ?>>
                                            Quan trọng
                                        </option>
                                        <option value="rat_quan_trong"
                                            <?php echo e(old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'rat_quan_trong' ? 'selected' : ''); ?>>
                                            Rất quan
                                            trọng</option>
                                    </select>
                                    <?php $__errorArgs = ['muc_do_quan_trong'];
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

                                
                                <div class="mb-3">
                                    <label for="doi_tuong" class="form-label">Đối tượng nhận <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['doi_tuong'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="doi_tuong"
                                        name="doi_tuong" required>
                                        <option value="">-- Chọn đối tượng --</option>
                                        <option value="all"
                                            <?php echo e(old('doi_tuong', $thongBao->doi_tuong) == 'all' ? 'selected' : ''); ?>>Tất cả
                                        </option>
                                        <option value="sinh_vien"
                                            <?php echo e(old('doi_tuong', $thongBao->doi_tuong) == 'sinh_vien' ? 'selected' : ''); ?>>
                                            Sinh viên</option>
                                        <option value="giang_vien"
                                            <?php echo e(old('doi_tuong', $thongBao->doi_tuong) == 'giang_vien' ? 'selected' : ''); ?>>
                                            Giảng viên</option>
                                        <option value="dao_tao"
                                            <?php echo e(old('doi_tuong', $thongBao->doi_tuong) == 'dao_tao' ? 'selected' : ''); ?>>Đào
                                            tạo</option>
                                        <option value="admin"
                                            <?php echo e(old('doi_tuong', $thongBao->doi_tuong) == 'admin' ? 'selected' : ''); ?>>Admin
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['doi_tuong'];
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

                                
                                <div class="mb-3">
                                    <label for="trang_thai" class="form-label">Trạng thái <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['trang_thai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="trang_thai"
                                        name="trang_thai" required>
                                        <option value="cong_khai"
                                            <?php echo e(old('trang_thai', $thongBao->trang_thai) == 'cong_khai' ? 'selected' : ''); ?>>
                                            Công khai</option>
                                        <option value="nhap"
                                            <?php echo e(old('trang_thai', $thongBao->trang_thai) == 'nhap' ? 'selected' : ''); ?>>
                                            Nháp</option>
                                        <option value="da_xoa"
                                            <?php echo e(old('trang_thai', $thongBao->trang_thai) == 'da_xoa' ? 'selected' : ''); ?>>
                                            Đã xóa</option>
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

                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ghim_dau_trang"
                                            name="ghim_dau_trang" value="1"
                                            <?php echo e(old('ghim_dau_trang', $thongBao->ghim_dau_trang) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="ghim_dau_trang">
                                            Ghim đầu trang
                                        </label>
                                    </div>
                                </div>

                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_email" name="gui_email"
                                            value="1" <?php echo e(old('gui_email', $thongBao->gui_email) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="gui_email">
                                            Gửi email thông báo
                                        </label>
                                    </div>
                                </div>

                                
                                <div class="mb-3">
                                    <label for="hien_thi_tu_ngay" class="form-label">Hiển thị từ ngày</label>
                                    <input type="datetime-local"
                                        class="form-control <?php $__errorArgs = ['hien_thi_tu_ngay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="hien_thi_tu_ngay" name="hien_thi_tu_ngay"
                                        value="<?php echo e(old('hien_thi_tu_ngay', $thongBao->hien_thi_tu_ngay)); ?>">
                                    <?php $__errorArgs = ['hien_thi_tu_ngay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Để trống để hiển thị ngay</small>
                                </div>

                                
                                <div class="mb-3">
                                    <label for="ngay_het_han" class="form-label">Ngày hết hạn</label>
                                    <input type="datetime-local"
                                        class="form-control <?php $__errorArgs = ['ngay_het_han'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ngay_het_han"
                                        name="ngay_het_han" value="<?php echo e(old('ngay_het_han', $thongBao->ngay_het_han)); ?>">
                                    <?php $__errorArgs = ['ngay_het_han'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Để trống nếu không có hạn</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Cập nhật thông báo
                                </button>
                                <a href="<?php echo e(route('admin.thong-bao.index')); ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/thong-bao/edit.blade.php ENDPATH**/ ?>