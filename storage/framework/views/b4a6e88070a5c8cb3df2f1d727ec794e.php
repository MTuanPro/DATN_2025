<?php $__env->startSection('title', 'Phân Phòng Thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Phân Phòng Thi</h4>
            <p class="text-muted mb-0">
                <?php echo e($lichThi->lopHocPhan->monHoc->ten_mon_hoc); ?> - 
                <?php echo e($lichThi->lopHocPhan->ma_lop); ?>

            </p>
        </div>
        <a href="<?php echo e(route('dao-tao.lich-thi.show', $lichThi)); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông tin lịch thi -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Thời gian:</strong> <?php echo e(\Carbon\Carbon::parse($lichThi->ngay_thi)->format('d/m/Y')); ?> 
                        - <?php echo e(\Carbon\Carbon::parse($lichThi->gio_bat_dau)->format('H:i')); ?> đến 
                        <?php echo e(\Carbon\Carbon::parse($lichThi->gio_ket_thuc)->format('H:i')); ?></p>
                    <p><strong>Phòng mặc định:</strong> <?php echo e($lichThi->phongThi->ten_phong ?? 'Chưa chọn'); ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Hình thức:</strong> 
                        <span class="badge <?php echo e($lichThi->hinh_thuc === 'offline' ? 'bg-primary' : 'bg-success'); ?>">
                            <?php echo e($lichThi->hinh_thuc === 'offline' ? 'Thi tại trường' : 'Thi trực tuyến'); ?>

                        </span>
                    </p>
                    <p><strong>Tổng số sinh viên:</strong> <?php echo e($lichThi->lichThiSinhViens->count()); ?> sinh viên</p>
                </div>
                <div class="col-md-4">
                    <?php
                        $phongDangDung = $lichThi->lichThiSinhViens->groupBy('phong_thi_id')->filter(fn($items, $key) => $key !== null);
                    ?>
                    <p><strong>Số phòng đang dùng:</strong> 
                        <span class="badge bg-info"><?php echo e($phongDangDung->count()); ?> phòng</span>
                    </p>
                    <?php if($phongDangDung->count() > 0): ?>
                        <small class="text-muted">
                            <?php $__currentLoopData = $phongDangDung; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phongId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $phong = $items->first()->phongThi; ?>
                                <div>• <?php echo e($phong->ten_phong ?? 'N/A'); ?>: <?php echo e($items->count()); ?> SV</div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Form chọn phòng -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Chuyển sinh viên sang phòng khác</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('dao-tao.lich-thi.cap-nhat-phong', $lichThi)); ?>" method="POST" id="formPhanPhong">
                <?php echo csrf_field(); ?>
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Chọn phòng thi đích:</label>
                        <select name="phong_thi_id" class="form-select" required id="selectPhong">
                            <option value="">-- Chọn phòng --</option>
                            
                            <?php
                                $phongDangDung = $lichThi->lichThiSinhViens->pluck('phong_thi_id')->unique()->filter();
                            ?>
                            
                            <?php if($phongHocs->isNotEmpty()): ?>
                                <optgroup label="📍 Phòng đang sử dụng cho lịch thi này">
                                    <?php $__currentLoopData = $phongHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $isDangDung = $phongDangDung->contains($phong->id);
                                            $isPhongMacDinh = $phong->id == $lichThi->phong_thi_id;
                                            $soSinhVien = $lichThi->lichThiSinhViens->where('phong_thi_id', $phong->id)->count();
                                            $conTrong = $phong->suc_chua - $soSinhVien;
                                        ?>
                                        <option value="<?php echo e($phong->id); ?>">
                                            <?php echo e($phong->ten_phong); ?>

                                            <?php if($isPhongMacDinh && $isDangDung): ?>
                                                - Mặc định (<?php echo e($soSinhVien); ?>/<?php echo e($phong->suc_chua); ?>, còn <?php echo e($conTrong); ?>)
                                            <?php elseif($isPhongMacDinh): ?>
                                                - Mặc định (Trống <?php echo e($phong->suc_chua); ?>)
                                            <?php elseif($isDangDung): ?>
                                                - Đang có <?php echo e($soSinhVien); ?>/<?php echo e($phong->suc_chua); ?> (còn <?php echo e($conTrong); ?>)
                                            <?php else: ?>
                                                - Trống (<?php echo e($phong->suc_chua); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            <?php endif; ?>
                            
                            <?php if(isset($phongTrong) && $phongTrong->isNotEmpty()): ?>
                                <optgroup label="➕ Phòng trống khác (<?php echo e($phongTrong->count()); ?>)">
                                    <?php $__currentLoopData = $phongTrong->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phong): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($phong->id); ?>">
                                            <?php echo e($phong->ten_phong); ?> - Trống (<?php echo e($phong->suc_chua); ?> chỗ)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($phongTrong->count() > 10): ?>
                                        <option disabled>... và <?php echo e($phongTrong->count() - 10); ?> phòng khác</option>
                                    <?php endif; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Nhóm 1: Phòng đang dùng cho lịch thi này | 
                            Nhóm 2: Phòng trống (không trùng giờ)
                        </small>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100" id="btnChuyenPhong" disabled>
                            <i class="bi bi-arrow-right-circle"></i> Chuyển sinh viên đã chọn
                        </button>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    <strong>Hướng dẫn:</strong> Chọn sinh viên ở bảng dưới, sau đó chọn phòng đích và nhấn "Chuyển sinh viên"
                </small>
            </form>
        </div>
    </div>

    <!-- Danh sách sinh viên theo phòng -->
    <?php if($sinhVienTheoPhong->isEmpty()): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Chưa có sinh viên nào được phân công cho lịch thi này.
        </div>
    <?php else: ?>
        <?php $__currentLoopData = $sinhVienTheoPhong; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phongId => $sinhViens): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-door-open"></i> 
                        <?php if($phongId): ?>
                            <?php echo e($sinhViens->first()->phongThi->ten_phong); ?>

                            <span class="badge bg-secondary"><?php echo e($sinhViens->count()); ?> sinh viên</span>
                        <?php else: ?>
                            Chưa phân phòng
                            <span class="badge bg-warning text-dark"><?php echo e($sinhViens->count()); ?> sinh viên</span>
                        <?php endif; ?>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="chonTatCaPhong(<?php echo e($phongId ?? 'null'); ?>)">
                        <i class="bi bi-check-all"></i> Chọn tất cả
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input checkbox-all" 
                                               data-phong="<?php echo e($phongId ?? 'null'); ?>">
                                    </th>
                                    <th>STT</th>
                                    <th>Số báo danh</th>
                                    <th>Mã sinh viên</th>
                                    <th>Họ tên</th>
                                    <th>Lớp hành chính</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input checkbox-sv" 
                                                   name="sinh_vien_ids[]" 
                                                   value="<?php echo e($item->sinh_vien_id); ?>"
                                                   data-phong="<?php echo e($phongId ?? 'null'); ?>"
                                                   form="formPhanPhong">
                                        </td>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($item->so_bao_danh); ?></strong></td>
                                        <td><?php echo e($item->sinhVien->ma_sinh_vien); ?></td>
                                        <td><?php echo e($item->sinhVien->ho_ten); ?></td>
                                        <td><?php echo e($item->sinhVien->lopHanhChinh->ten_lop ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($item->trang_thai === 'du_thi'): ?>
                                                <span class="badge bg-success">Dự thi</span>
                                            <?php elseif($item->trang_thai === 'vang_co_phep'): ?>
                                                <span class="badge bg-warning text-dark">Vắng có phép</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Vắng không phép</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Link xem danh sách chi tiết -->
    <div class="text-center mt-4">
        <a href="<?php echo e(route('dao-tao.lich-thi.danh-sach-sinh-vien', $lichThi)); ?>" 
           class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-file-earmark-text"></i> Xem danh sách chi tiết (In/Xuất)
        </a>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxAll = document.querySelectorAll('.checkbox-all');
    const checkboxSinhVien = document.querySelectorAll('.checkbox-sv');
    const btnChuyenPhong = document.getElementById('btnChuyenPhong');
    const form = document.getElementById('formPhanPhong');

    // Xử lý chọn tất cả theo phòng
    checkboxAll.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const phongId = this.dataset.phong;
            const checkboxes = document.querySelectorAll(`.checkbox-sv[data-phong="${phongId}"]`);
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateButtonState();
        });
    });

    // Xử lý khi chọn từng sinh viên
    checkboxSinhVien.forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonState);
    });

    // Cập nhật trạng thái nút chuyển phòng
    function updateButtonState() {
        const anyChecked = Array.from(checkboxSinhVien).some(cb => cb.checked);
        btnChuyenPhong.disabled = !anyChecked;
    }

    // Validate trước khi submit
    form.addEventListener('submit', function(e) {
        const checkedCount = Array.from(checkboxSinhVien).filter(cb => cb.checked).length;
        const phongSelect = this.querySelector('[name="phong_thi_id"]');
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một sinh viên!');
            return;
        }

        if (!phongSelect.value) {
            e.preventDefault();
            alert('Vui lòng chọn phòng thi đích!');
            return;
        }

        if (!confirm(`Bạn có chắc chắn muốn chuyển ${checkedCount} sinh viên sang phòng đã chọn?`)) {
            e.preventDefault();
        }
    });
});

// Hàm chọn tất cả theo phòng (gọi từ button)
function chonTatCaPhong(phongId) {
    const checkbox = document.querySelector(`.checkbox-all[data-phong="${phongId}"]`);
    if (checkbox) {
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/lich-thi/phan-phong.blade.php ENDPATH**/ ?>