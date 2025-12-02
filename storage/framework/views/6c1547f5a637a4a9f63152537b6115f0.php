<?php $__env->startSection('title', 'Thêm lịch học cố định'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm lịch học cố định</h3>
                    <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhan)); ?>">Lịch cố định</a>
                            </li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin môn học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin môn học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Mã môn:</strong> <?php echo e($lopHocPhan->monHoc->ma_mon); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Số tín chỉ:</strong> <?php echo e($lopHocPhan->monHoc->so_tin_chi); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Số buổi học:</strong> 
                            <span class="badge bg-info"><?php echo e($lopHocPhan->monHoc->so_buoi_hoc ?? 15); ?> buổi</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Thời gian:</strong> 
                            <?php echo e(\Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau)->format('d/m/Y')); ?> - 
                            <?php echo e(\Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y')); ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Tạo lịch học tự động</h5>
                    <p class="text-muted small mb-0">Hệ thống sẽ tự động tạo tất cả các buổi học theo pattern bạn chọn</p>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra:</h6>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh.store', $lopHocPhan)); ?>" method="POST" id="scheduleForm">
                        <?php echo csrf_field(); ?>

                        <!-- Phần 1: Chọn Ca học và Thời gian -->
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i> <strong>Bước 1:</strong> Chọn ca học và pattern lặp lại
                        </div>

                        <div class="row">
                            <!-- Ca học -->
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
unset($__errorArgs, $__bag); ?>"
                                        id="ca_hoc_id" name="ca_hoc_id" required>
                                        <option value="">-- Chọn ca học --</option>
                                        <?php $__currentLoopData = $caHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($caHoc->id); ?>" 
                                                data-gio-bat-dau="<?php echo e($caHoc->gio_bat_dau); ?>"
                                                data-gio-ket-thuc="<?php echo e($caHoc->gio_ket_thuc); ?>"
                                                <?php echo e((int)old('ca_hoc_id', 0) === (int)$caHoc->id ? 'selected' : ''); ?>>
                                                <?php echo e($caHoc->ten_ca); ?> (<?php echo e(date('H:i', strtotime($caHoc->gio_bat_dau))); ?> - <?php echo e(date('H:i', strtotime($caHoc->gio_ket_thuc))); ?>)
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
                                </div>
                            </div>

                            <!-- Số buổi học -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="so_buoi_hoc">Số buổi học <span class="text-danger">*</span></label>
                                    <input type="number" 
                                        class="form-control <?php $__errorArgs = ['so_buoi_hoc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="so_buoi_hoc" 
                                        name="so_buoi_hoc" 
                                        value="<?php echo e(old('so_buoi_hoc', $lopHocPhan->monHoc->so_buoi_hoc ?? 15)); ?>"
                                        min="1" 
                                        max="50" 
                                        required>
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
                                    <small class="text-muted">
                                        Mặc định: <?php echo e($lopHocPhan->monHoc->so_buoi_hoc ?? 15); ?> buổi
                                        <span id="max-sessions-hint" class="text-info d-none">
                                            | <i class="bi bi-info-circle"></i> Tối đa có thể tạo: <strong id="max-sessions-count">0</strong> buổi
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pattern lặp lại -->
                        <div class="row">
                            <!-- Ngày bắt đầu -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ngay_bat_dau_lich">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" 
                                        class="form-control <?php $__errorArgs = ['ngay_bat_dau_lich'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="ngay_bat_dau_lich" 
                                        name="ngay_bat_dau_lich" 
                                        value="<?php echo e(old('ngay_bat_dau_lich', $lopHocPhan->ngay_bat_dau)); ?>"
                                        min="<?php echo e($lopHocPhan->ngay_bat_dau); ?>"
                                        max="<?php echo e($lopHocPhan->ngay_ket_thuc); ?>"
                                        required>
                                    <?php $__errorArgs = ['ngay_bat_dau_lich'];
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

                            <!-- Các thứ trong tuần -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Các thứ học trong tuần <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="2" id="thu2">
                                            <label class="form-check-label" for="thu2">Thứ 2</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="3" id="thu3">
                                            <label class="form-check-label" for="thu3">Thứ 3</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="4" id="thu4">
                                            <label class="form-check-label" for="thu4">Thứ 4</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="5" id="thu5">
                                            <label class="form-check-label" for="thu5">Thứ 5</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="6" id="thu6">
                                            <label class="form-check-label" for="thu6">Thứ 6</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="7" id="thu7">
                                            <label class="form-check-label" for="thu7">Thứ 7</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="8" id="thu8">
                                            <label class="form-check-label" for="thu8">CN</label>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['thu_trong_tuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle"></i> 
                                        Chọn các thứ bạn muốn xếp lịch. Ví dụ: chọn Thứ 2 và Thứ 4 → lịch sẽ lặp theo pattern T2-T4-T2-T4...
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Phần 2: Thông tin cố định -->
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i> <strong>Bước 2:</strong> Chọn phòng học, giảng viên và hình thức (áp dụng cho tất cả buổi)
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
                                                <?php echo e((int)old('phong_hoc_id', 0) === (int)$phongHoc->id ? 'selected' : ''); ?>>
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
                                                <?php echo e((int)old('giang_vien_id', $giangVienChinhId ?? 0) === (int)$giangVien->id ? 'selected' : ''); ?>>
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
                                    <?php if(isset($giangVienChinhId)): ?>
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle"></i> Đã tự động chọn giảng viên chính từ phân công. Bạn có thể thay đổi nếu cần.
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
                                        <option value="offline" <?php echo e(old('hinh_thuc', 'offline') == 'offline' ? 'selected' : ''); ?>>
                                            Offline</option>
                                        <option value="online" <?php echo e(old('hinh_thuc') == 'online' ? 'selected' : ''); ?>>Online
                                        </option>
                                        <option value="hybrid" <?php echo e(old('hinh_thuc') == 'hybrid' ? 'selected' : ''); ?>>Hybrid
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
                                        id="link_online" name="link_online" value="<?php echo e(old('link_online')); ?>"
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
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="2"><?php echo e(old('ghi_chu')); ?></textarea>
                        </div>

                        <hr class="my-4">

                        <!-- Preview lịch -->
                        <div id="preview-section" class="d-none">
                            <div class="alert alert-success">
                                <i class="bi bi-calendar-check"></i> <strong>Preview:</strong> 
                                Hệ thống sẽ tạo <span id="preview-count" class="badge bg-success">0</span> buổi học
                            </div>
                            <div id="preview-list" class="small text-muted"></div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-plus"></i> Tạo lịch học tự động
                            </button>
                            <button type="button" id="btn-preview" class="btn btn-info btn-lg">
                                <i class="bi bi-eye"></i> Xem trước
                            </button>
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhan)); ?>"
                                class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const btnPreview = document.getElementById('btn-preview');
    const previewSection = document.getElementById('preview-section');
    const previewCount = document.getElementById('preview-count');
    const previewList = document.getElementById('preview-list');
    const maxSessionsHint = document.getElementById('max-sessions-hint');
    const maxSessionsCount = document.getElementById('max-sessions-count');
    
    // Lấy ngày kết thúc của lớp học phần từ server
    const ngayKetThucLop = '<?php echo e($lopHocPhan->ngay_ket_thuc); ?>';
    
    // Hàm tính số buổi tối đa có thể tạo
    function calculateMaxSessions() {
        const ngayBatDau = document.getElementById('ngay_bat_dau_lich').value;
        const thuCheckboxes = document.querySelectorAll('.thu-checkbox:checked');
        const thuList = Array.from(thuCheckboxes).map(cb => parseInt(cb.value));
        
        if (!ngayBatDau || thuList.length === 0 || !ngayKetThucLop) {
            maxSessionsHint.classList.add('d-none');
            return;
        }
        
        // Tính toán số buổi tối đa
        const startDate = new Date(ngayBatDau);
        const endDate = new Date(ngayKetThucLop);
        let count = 0;
        let currentDate = new Date(startDate);
        
        while (currentDate <= endDate && count < 100) { // Giới hạn 100 để tránh vòng lặp vô hạn
            const dayOfWeek = currentDate.getDay();
            const thuTrongTuan = dayOfWeek === 0 ? 8 : dayOfWeek + 1;
            
            if (thuList.includes(thuTrongTuan)) {
                count++;
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        // Hiển thị kết quả
        if (count > 0) {
            maxSessionsCount.textContent = count;
            maxSessionsHint.classList.remove('d-none');
            
            // Cảnh báo nếu số buổi nhập vào lớn hơn số buổi tối đa
            const soBuoiHoc = parseInt(document.getElementById('so_buoi_hoc').value);
            if (soBuoiHoc > count) {
                maxSessionsHint.classList.remove('text-info');
                maxSessionsHint.classList.add('text-warning');
            } else {
                maxSessionsHint.classList.remove('text-warning');
                maxSessionsHint.classList.add('text-info');
            }
        } else {
            maxSessionsHint.classList.add('d-none');
        }
    }
    
    // Lắng nghe sự kiện thay đổi
    document.getElementById('ngay_bat_dau_lich').addEventListener('change', calculateMaxSessions);
    document.querySelectorAll('.thu-checkbox').forEach(cb => {
        cb.addEventListener('change', calculateMaxSessions);
    });
    
    // Tính toán ban đầu nếu đã có giá trị
    if (document.getElementById('ngay_bat_dau_lich').value) {
        calculateMaxSessions();
    }
    
    // Hiển thị link online khi chọn online/hybrid
    document.getElementById('hinh_thuc').addEventListener('change', function() {
        const linkOnlineGroup = document.getElementById('link_online').closest('.form-group');
        if (this.value === 'online' || this.value === 'hybrid') {
            linkOnlineGroup.style.display = 'block';
        } else {
            linkOnlineGroup.style.display = 'block'; // Vẫn hiển thị nhưng không bắt buộc
        }
    });

    // Preview lịch học
    btnPreview.addEventListener('click', function() {
        // Lấy dữ liệu từ form
        const ngayBatDau = document.getElementById('ngay_bat_dau_lich').value;
        const soBuoiHoc = parseInt(document.getElementById('so_buoi_hoc').value);
        const caHocSelect = document.getElementById('ca_hoc_id');
        const caHocText = caHocSelect.options[caHocSelect.selectedIndex]?.text || '';
        
        // Lấy các thứ được chọn
        const thuCheckboxes = document.querySelectorAll('.thu-checkbox:checked');
        const thuList = Array.from(thuCheckboxes).map(cb => parseInt(cb.value)).sort();
        
        if (!ngayBatDau || !soBuoiHoc || thuList.length === 0 || !caHocText) {
            alert('Vui lòng điền đầy đủ thông tin: Ngày bắt đầu, Số buổi học, Ca học và Các thứ trong tuần');
            return;
        }

        // Tính toán các ngày học
        const ngayHocList = [];
        let currentDate = new Date(ngayBatDau);
        const endDate = new Date(ngayKetThucLop);
        let iterations = 0;
        const maxIterations = 365;
        
        while (ngayHocList.length < soBuoiHoc && iterations < maxIterations) {
            // Kiểm tra nếu vượt quá ngày kết thúc
            if (currentDate > endDate) {
                break;
            }
            
            // Lấy thứ hiện tại (JavaScript: 0=CN, 1=T2, ..., 6=T7)
            const dayOfWeek = currentDate.getDay();
            // Chuyển đổi: JavaScript -> Hệ thống của ta (2=T2, 3=T3, ..., 7=T7, 8=CN)
            const thuTrongTuan = dayOfWeek === 0 ? 8 : dayOfWeek + 1;
            
            // Kiểm tra nếu thứ hiện tại nằm trong danh sách được chọn
            if (thuList.includes(thuTrongTuan)) {
                ngayHocList.push({
                    ngay: new Date(currentDate),
                    thu: thuTrongTuan
                });
            }
            
            // Chuyển sang ngày tiếp theo
            currentDate.setDate(currentDate.getDate() + 1);
            iterations++;
        }
        
        // Hiển thị preview với cảnh báo nếu không đủ
        previewCount.textContent = ngayHocList.length;
        
        const thuNames = {2: 'T2', 3: 'T3', 4: 'T4', 5: 'T5', 6: 'T6', 7: 'T7', 8: 'CN'};
        let previewHTML = '';
        
        if (ngayHocList.length < soBuoiHoc) {
            previewHTML = `<div class="alert alert-warning mb-2">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Cảnh báo:</strong> Chỉ có thể tạo được ${ngayHocList.length} buổi trong khoảng thời gian của lớp học phần (yêu cầu: ${soBuoiHoc} buổi).
            </div>`;
        }
        
        previewHTML += ngayHocList.map((item, index) => {
            const dateStr = item.ngay.toLocaleDateString('vi-VN');
            return `<span class="badge bg-light text-dark me-2 mb-2">Buổi ${index + 1}: ${thuNames[item.thu]} ${dateStr} - ${caHocText}</span>`;
        }).join('');
        
        previewList.innerHTML = previewHTML;
        previewSection.classList.remove('d-none');
        
        // Scroll to preview
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-hoc-co-dinh/create.blade.php ENDPATH**/ ?>