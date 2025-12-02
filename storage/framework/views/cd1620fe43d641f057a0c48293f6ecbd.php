<?php $__env->startSection('title', 'Danh sách chờ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách chờ (Waiting List)</h3>
                    <p class="text-subtitle text-muted">Sinh viên không xếp được lớp</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.xep-lop.index')); ?>">Xếp lớp</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Danh sách chờ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('dao-tao.xep-lop.waiting-list')); ?>" class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <label class="form-label">&nbsp;</label>
                        <a href="<?php echo e(route('dao-tao.xep-lop.index')); ?>" class="btn btn-secondary d-block">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách chờ -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    Sinh viên không xếp được lớp (<?php echo e($waitingList->total()); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if($waitingList->isEmpty()): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tuyệt vời! Không có sinh viên nào trong danh sách chờ.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <strong>Lưu ý:</strong> Những sinh viên này cần được xếp lớp thủ công hoặc chờ có lớp mở thêm.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày ĐK</th>
                                    <th>Ưu tiên</th>
                                    <th>Lý do thất bại</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $waitingList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($waitingList->firstItem() + $index); ?></td>
                                        <td><code><?php echo e($dk->sinhVien->ma_sinh_vien); ?></code></td>
                                        <td><?php echo e($dk->sinhVien->ho_ten); ?></td>
                                        <td>
                                            <?php if($dk->sinhVien->lopHanhChinh): ?>
                                                <?php echo e($dk->sinhVien->lopHanhChinh->ma_lop); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($dk->monHoc->ten_mon); ?></strong>
                                            <br><small class="text-muted"><?php echo e($dk->monHoc->ma_mon); ?></small>
                                        </td>
                                        <td><?php echo e($dk->hocKy->ten_hoc_ky); ?></td>
                                        <td><?php echo e($dk->ngay_dang_ky->format('d/m/Y')); ?></td>
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
                                            <small class="text-danger">
                                                <?php echo e($dk->ly_do_that_bai ?? 'Không rõ'); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-xep-thu-cong"
                                                data-dang-ky-id="<?php echo e($dk->id); ?>"
                                                data-sinh-vien="<?php echo e($dk->sinhVien->ho_ten); ?>"
                                                data-mon-hoc-id="<?php echo e($dk->mon_hoc_id); ?>"
                                                data-hoc-ky-id="<?php echo e($dk->hoc_ky_id); ?>">
                                                <i class="bi bi-pencil"></i> Xếp lớp
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($waitingList->links()); ?>

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
                // Xếp lớp thủ công - Mở modal
                $('.btn-xep-thu-cong').click(function() {
                    currentDangKyId = $(this).data('dang-ky-id');
                    const tenSinhVien = $(this).data('sinh-vien');
                    const monHocId = $(this).data('mon-hoc-id');
                    const hocKyId = $(this).data('hoc-ky-id');

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

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/xep-lop/waiting-list.blade.php ENDPATH**/ ?>