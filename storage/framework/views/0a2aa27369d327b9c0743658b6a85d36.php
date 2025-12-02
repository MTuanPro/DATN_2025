<?php $__env->startSection('title', 'Xếp lớp tự động'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Xếp lớp tự động</h3>
                    <p class="text-subtitle text-muted">Quản lý đăng ký môn học và xếp lớp tự động</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Xếp lớp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Chờ đóng học phí</h6>
                                <h3 class="mb-0 text-info"><?php echo e($thongKe['cho_dong_hoc_phi'] ?? 0); ?></h3>
                            </div>
                            <div class="avatar avatar-xl bg-info">
                                <i class="bi bi-cash-stack text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Chờ xếp lớp</h6>
                                <h3 class="mb-0 text-warning"><?php echo e($thongKe['cho_xep_lop']); ?></h3>
                            </div>
                            <div class="avatar avatar-xl bg-warning">
                                <i class="bi bi-hourglass-split text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Đã xếp lớp</h6>
                                <h3 class="mb-0 text-success"><?php echo e($thongKe['da_xep_lop']); ?></h3>
                            </div>
                            <div class="avatar avatar-xl bg-success">
                                <i class="bi bi-check-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Thất bại</h6>
                                <h3 class="mb-0 text-danger"><?php echo e($thongKe['that_bai']); ?></h3>
                            </div>
                            <div class="avatar avatar-xl bg-danger">
                                <i class="bi bi-x-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc và thao tác -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('dao-tao.xep-lop.index')); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Học kỳ</label>
                        <select name="hoc_ky_id" class="form-select">
                            <option value="">Tất cả</option>
                            <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hk->id); ?>" <?php echo e(request('hoc_ky_id') == $hk->id ? 'selected' : ''); ?>>
                                    <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="cho_dong_hoc_phi" <?php echo e(request('trang_thai') == 'cho_dong_hoc_phi' ? 'selected' : ''); ?>>Chờ đóng học phí</option>
                            <option value="cho_xep_lop" <?php echo e(request('trang_thai') == 'cho_xep_lop' ? 'selected' : ''); ?>>Chờ xếp lớp</option>
                            <option value="da_xep_lop" <?php echo e(request('trang_thai') == 'da_xep_lop' ? 'selected' : ''); ?>>Đã xếp lớp</option>
                            <option value="that_bai" <?php echo e(request('trang_thai') == 'that_bai' ? 'selected' : ''); ?>>Thất bại</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success d-block" id="btnXepLopTuDong">
                            <i class="bi bi-magic"></i> Xếp lớp tự động
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách đăng ký -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Danh sách đăng ký môn học</h5>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Lưu ý:</strong> 
                    • Sinh viên có trạng thái <span class="badge bg-info">Chờ đóng học phí</span> cần đóng tiền trước khi xếp lớp.
                    <br>
                    • Chỉ sinh viên có trạng thái <span class="badge bg-warning">Chờ xếp lớp</span> (đã đóng học phí) mới có thể xếp vào lớp.
                </p>
            </div>
            <div class="card-body">
                <?php if($dangKys->isEmpty()): ?>
                    <div class="alert alert-info mb-0">
                        Không có đăng ký nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày ĐK</th>
                                    <th>Ưu tiên</th>
                                    <th>Trạng thái</th>
                                    <th>Học phí</th>
                                    <th>Lý do</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $dangKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($dangKys->firstItem() + $index); ?></td>
                                        <td><code><?php echo e($dk->sinhVien->ma_sinh_vien); ?></code></td>
                                        <td><?php echo e($dk->sinhVien->ho_ten); ?></td>
                                        <td>
                                            <strong><?php echo e($dk->monHoc->ten_mon); ?></strong>
                                            <br><small class="text-muted"><?php echo e($dk->monHoc->ma_mon); ?></small>
                                        </td>
                                        <td><?php echo e($dk->hocKy->ten_hoc_ky); ?></td>
                                        <td><?php echo e($dk->ngay_dang_ky->format('d/m/Y H:i')); ?></td>
                                        <td>
                                            <?php if($dk->uu_tien >= 100): ?>
                                                <span class="badge bg-danger"><?php echo e($dk->uu_tien); ?></span>
                                            <?php elseif($dk->uu_tien >= 50): ?>
                                                <span class="badge bg-warning"><?php echo e($dk->uu_tien); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e($dk->uu_tien); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($dk->trang_thai_badge); ?>">
                                                <?php echo e($dk->trang_thai_label); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                // Lấy thông tin học phí
                                                $hocPhi = \App\Models\HocPhiHocKy::where('sinh_vien_id', $dk->sinh_vien_id)
                                                    ->where('hoc_ky_id', $dk->hoc_ky_id)
                                                    ->first();
                                                
                                                $chiTietHocPhi = null;
                                                if ($hocPhi) {
                                                    $chiTietHocPhi = \App\Models\ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                                                        ->where('mon_hoc_id', $dk->mon_hoc_id)
                                                        ->first();
                                                }
                                            ?>
                                            
                                            <?php if($chiTietHocPhi): ?>
                                                <?php if($chiTietHocPhi->trang_thai == 'da_dong'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Đã đóng
                                                    </span>
                                                <?php elseif($chiTietHocPhi->trang_thai == 'chua_dong'): ?>
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-circle"></i> Chưa đóng
                                                    </span>
                                                <?php elseif($chiTietHocPhi->trang_thai == 'huy'): ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-x-circle"></i> Đã hủy
                                                    </span>
                                                <?php endif; ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo e(number_format($chiTietHocPhi->thanh_tien, 0, ',', '.')); ?> đ
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($dk->ly_do_that_bai): ?>
                                                <?php if($dk->trang_thai == 'that_bai'): ?>
                                                    <small class="text-danger"><?php echo e($dk->ly_do_that_bai); ?></small>
                                                <?php elseif($dk->trang_thai == 'cho_xep_lop'): ?>
                                                    <small class="text-warning"><?php echo e($dk->ly_do_that_bai); ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted"><?php echo e($dk->ly_do_that_bai); ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($dk->trang_thai == 'cho_xep_lop'): ?>
                                                <button type="button" class="btn btn-sm btn-primary btn-xep-thu-cong"
                                                    data-dang-ky-id="<?php echo e($dk->id); ?>"
                                                    data-sinh-vien="<?php echo e($dk->sinhVien->ho_ten); ?>"
                                                    data-mon-hoc-id="<?php echo e($dk->mon_hoc_id); ?>">
                                                    <i class="bi bi-pencil"></i> Xếp
                                                </button>
                                            <?php elseif($dk->trang_thai == 'cho_dong_hoc_phi'): ?>
                                                <span class="text-muted small">Chờ đóng học phí</span>
                                            <?php elseif($dk->lopHocPhanSinhVien): ?>
                                                <a href="<?php echo e(route('dao-tao.xep-lop.danh-sach-lop', $dk->lopHocPhanSinhVien->lop_hoc_phan_id)); ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Xem lớp
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($dangKys->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal xếp lớp thủ công -->
    <div class="modal fade" id="modalXepLop" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xếp lớp thủ công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Sinh viên:</strong> <span id="tenSinhVien"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Chọn lớp học phần</label>
                        <select id="selectLopHocPhan" class="form-select">
                            <option value="">-- Chọn lớp --</option>
                        </select>
                        <small class="text-muted">Chỉ hiển thị các lớp còn chỗ trống</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="btnXacNhanXepLop">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            let currentDangKyId = null;

            $(document).ready(function() {
                // Xếp lớp tự động
                $('#btnXepLopTuDong').click(function() {
                    const hocKyId = $('select[name="hoc_ky_id"]').val();

                    if (!hocKyId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa chọn học kỳ',
                            text: 'Vui lòng chọn học kỳ trước khi xếp lớp tự động.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Xác nhận xếp lớp tự động',
                        text: 'Hệ thống sẽ tự động xếp lớp cho tất cả sinh viên đăng ký trong học kỳ này.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xếp lớp',
                        cancelButtonText: 'Hủy',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: '<?php echo e(route('dao-tao.xep-lop.auto-assign')); ?>',
                                method: 'POST',
                                data: {
                                    _token: '<?php echo e(csrf_token()); ?>',
                                    hoc_ky_id: hocKyId
                                }
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Hoàn tất!',
                                html: result.value.message
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                });

                // Xếp lớp thủ công - Mở modal
                $('.btn-xep-thu-cong').click(function() {
                    currentDangKyId = $(this).data('dang-ky-id');
                    const tenSinhVien = $(this).data('sinh-vien');
                    const monHocId = $(this).data('mon-hoc-id');
                    const hocKyId = $('select[name="hoc_ky_id"]').val();

                    $('#tenSinhVien').text(tenSinhVien);

                    // Load danh sách lớp học phần
                    $.ajax({
                        url: `/dao-tao/xep-lop/lop-hoc-phan-by-mon/${monHocId}`,
                        method: 'GET',
                        data: {
                            hoc_ky_id: hocKyId
                        },
                        success: function(response) {
                            let options = '<option value="">-- Chọn lớp --</option>';
                            response.data.forEach(lop => {
                                // Hiển thị: Mã lớp (Số hiện tại/Sức chứa - Còn X chỗ)
                                options +=
                                    `<option value="${lop.id}">${lop.ma_lop_hoc_phan} (${lop.so_luong_hien_tai}/${lop.so_luong_toi_da} - Còn ${lop.con_trong} chỗ)</option>`;
                            });
                            $('#selectLopHocPhan').html(options);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không thể tải danh sách lớp học phần!'
                            });
                        }
                    });

                    $('#modalXepLop').modal('show');
                });

                // Xác nhận xếp lớp thủ công
                $('#btnXacNhanXepLop').click(function() {
                    const lopHocPhanId = $('#selectLopHocPhan').val();

                    if (!lopHocPhanId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa chọn lớp',
                            text: 'Vui lòng chọn lớp học phần.'
                        });
                        return;
                    }

                    $.ajax({
                        url: '<?php echo e(route('dao-tao.xep-lop.manual-assign')); ?>',
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            dang_ky_tam_id: currentDangKyId,
                            lop_hoc_phan_id: lopHocPhanId
                        },
                        success: function(response) {
                            $('#modalXepLop').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: xhr.responseJSON?.message || 'Có lỗi xảy ra!'
                            });
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/xep-lop/index.blade.php ENDPATH**/ ?>