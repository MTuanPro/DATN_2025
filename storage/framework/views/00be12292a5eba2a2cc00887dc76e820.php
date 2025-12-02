<?php $__env->startSection('title', 'Chi tiết bảng điểm'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết bảng điểm</h3>
                <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.duyet-diem.index')); ?>">Duyệt điểm</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Thông tin lớp -->
    <section class="section">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin lớp học phần</h5>
                <div>
                    <a href="<?php echo e(route('dao-tao.duyet-diem.index')); ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mã lớp:</th>
                                <td><strong><?php echo e($lopHocPhan->ma_lop_hp); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td><?php echo e($lopHocPhan->monHoc->ma_mon); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></td>
                            </tr>
                            <tr>
                                <th>Số tín chỉ:</th>
                                <td><?php echo e($lopHocPhan->monHoc->so_tin_chi); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Học kỳ:</th>
                                <td><?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> - <?php echo e($lopHocPhan->hocKy->nam_hoc); ?></td>
                            </tr>
                            <tr>
                                <th>Số sinh viên:</th>
                                <td><span class="badge bg-info"><?php echo e($thongKe['tong_sv']); ?> SV</span></td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    <?php if($lopHocPhan->trang_thai_lop === 'da_khoa_diem'): ?>
                                        <span class="badge bg-warning"><i class="bi bi-clock"></i> Chờ duyệt</span>
                                    <?php elseif($lopHocPhan->trang_thai_lop === 'da_duyet_diem'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-info"><?php echo e($lopHocPhan->ten_trang_thai); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Thống kê -->
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Tổng SV</h6>
                                <h3 class="mb-0"><?php echo e($thongKe['tong_sv']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV có điểm</h6>
                                <h3 class="mb-0"><?php echo e($thongKe['sv_co_diem']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV qua môn</h6>
                                <h3 class="mb-0"><?php echo e($thongKe['sv_qua_mon']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV không qua</h6>
                                <h3 class="mb-0"><?php echo e($thongKe['sv_khong_qua_mon']); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($thongKe['diem_tb']): ?>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info text-center mb-0">
                            <strong>Điểm trung bình lớp: <?php echo e(number_format($thongKe['diem_tb'], 2)); ?></strong>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cấu hình đầu điểm -->
    <?php if(!$cauHinhs->isEmpty()): ?>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Cấu hình đầu điểm</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cauHinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-2">
                            <div class="alert alert-info mb-0">
                                <strong><?php echo e($cauHinh->ten_dau_diem); ?>:</strong> <?php echo e($cauHinh->ty_le); ?>%
                                <?php if($cauHinh->so_cot > 1): ?>
                                    <small>(<?php echo e($cauHinh->so_cot); ?> cột)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Bảng điểm -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-table"></i> Bảng điểm sinh viên</h5>
                <?php if($lopHocPhan->trang_thai_lop === 'da_khoa_diem'): ?>
                <div>
                    <button type="button" class="btn btn-sm btn-success" onclick="duyetDiem('phe_duyet')">
                        <i class="bi bi-check-circle"></i> Phê duyệt
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="duyetDiem('tra_ve')">
                        <i class="bi bi-arrow-counterclockwise"></i> Trả về
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($cauHinhs->isEmpty()): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có cấu hình đầu điểm cho lớp này.
                    </div>
                <?php elseif($sinhViens->isEmpty()): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-inbox"></i> Chưa có sinh viên nào trong lớp học phần.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" rowspan="2">STT</th>
                                    <th width="100" rowspan="2">MSSV</th>
                                    <th width="200" rowspan="2">Họ tên</th>
                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cauHinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="text-center" colspan="<?php echo e($cauHinh->so_cot); ?>">
                                            <?php echo e($cauHinh->ten_dau_diem); ?><br>
                                            <small class="text-muted">(<?php echo e($cauHinh->ty_le); ?>%)</small>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th width="80" class="text-center" rowspan="2">Điểm TK</th>
                                    <th width="80" class="text-center" rowspan="2">Kết quả</th>
                                </tr>
                                <tr>
                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cauHinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php for($cot = 1; $cot <= $cauHinh->so_cot; $cot++): ?>
                                            <th width="70" class="text-center">
                                                <?php if($cauHinh->so_cot > 1): ?>
                                                    Cột <?php echo e($cot); ?>

                                                <?php else: ?>
                                                    Điểm
                                                <?php endif; ?>
                                            </th>
                                        <?php endfor; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $sinhViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhpsv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $nhapDiemSV = $nhapDiems->get($lhpsv->id) ?? collect();
                                    $ketQua = $lhpsv->ketQuaHocTap;
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></td>
                                    <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cauHinh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php for($cot = 1; $cot <= $cauHinh->so_cot; $cot++): ?>
                                            <?php
                                                $diem = $nhapDiemSV->where('cau_hinh_id', $cauHinh->id)
                                                    ->where('cot_diem', $cot)
                                                    ->first();
                                            ?>
                                            <td class="text-center">
                                                <?php if($diem): ?>
                                                    <strong><?php echo e(number_format($diem->diem_so, 1)); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td class="text-center">
                                        <?php if($ketQua && $ketQua->diem_he_10 !== null): ?>
                                            <strong class="text-primary"><?php echo e(number_format($ketQua->diem_he_10, 2)); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($ketQua): ?>
                                            <?php if($ketQua->qua_mon): ?>
                                                <span class="badge bg-success">Qua môn</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Không qua</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function duyetDiem(hanhDong) {
        if (hanhDong === 'phe_duyet') {
            Swal.fire({
                title: 'Xác nhận phê duyệt',
                text: 'Bạn có chắc muốn phê duyệt và công bố điểm cho sinh viên?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, phê duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    guiDuyetDiem(hanhDong);
                }
            });
        } else {
            Swal.fire({
                title: 'Trả về điểm',
                html: '<input id="lyDoTraVe" class="swal2-input" placeholder="Nhập lý do trả về">',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Trả về',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const lyDo = document.getElementById('lyDoTraVe').value;
                    if (!lyDo) {
                        Swal.showValidationMessage('Vui lòng nhập lý do trả về');
                        return false;
                    }
                    return { ly_do_tra_ve: lyDo };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    guiDuyetDiem(hanhDong, result.value.ly_do_tra_ve);
                }
            });
        }
    }

    function guiDuyetDiem(hanhDong, lyDoTraVe = '') {
        const data = {
            hanh_dong: hanhDong,
            _token: '<?php echo e(csrf_token()); ?>'
        };

        if (hanhDong === 'tra_ve' && lyDoTraVe) {
            data.ly_do_tra_ve = lyDoTraVe;
        }

        fetch('<?php echo e(route("dao-tao.duyet-diem.duyet", $lopHocPhan->id)); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Thành công!', data.message, 'success')
                    .then(() => {
                        window.location.href = '<?php echo e(route("dao-tao.duyet-diem.index")); ?>';
                    });
            } else {
                Swal.fire('Lỗi!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi duyệt điểm', 'error');
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/duyet-diem/show.blade.php ENDPATH**/ ?>