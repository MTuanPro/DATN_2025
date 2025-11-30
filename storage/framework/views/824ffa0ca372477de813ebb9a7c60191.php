<?php $__env->startSection('title', 'Đăng ký môn học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Học kỳ:
                        <?php echo e($hocKy ? $hocKy->ten_hoc_ky . ' - ' . $hocKy->nam_hoc : 'N/A'); ?></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Đăng ký môn học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!$hocKy): ?>
            <div class="alert alert-warning">
                <h4 class="alert-heading">Thông báo</h4>
                <p><?php echo e($message ?? 'Hiện tại không có học kỳ nào mở đăng ký môn học.'); ?></p>
            </div>
        <?php else: ?>
            <!-- Thông tin đăng ký -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Thời gian đăng ký</h6>
                            <p class="mb-0">
                                <strong>Từ:</strong> <?php echo e($hocKy->ngay_bat_dau_dang_ky->format('d/m/Y')); ?><br>
                                <strong>Đến:</strong> <?php echo e($hocKy->ngay_ket_thuc_dang_ky->format('d/m/Y')); ?>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Số tín chỉ</h6>
                            <p class="mb-0">
                                <strong>Đã đăng ký:</strong> <?php echo e($tongTinChiDaDangKy); ?> TC<br>
                                <strong>Tối đa:</strong> <?php echo e($tinChiToiDa); ?> TC
</p>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar <?php echo e($tongTinChiDaDangKy >= $tinChiToiDa ? 'bg-danger' : 'bg-primary'); ?>"
                                    style="width: <?php echo e(($tongTinChiDaDangKy / $tinChiToiDa) * 100); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Trạng thái</h6>
                            <?php
                                // Kiểm tra xem học kỳ có được mở đăng ký không
                                $hocKyMoDangKy = \App\Models\HocKy::where('la_hoc_ky_hien_tai', true)
                                    ->where('dang_mo_dang_ky', true)
                                    ->first();
                            ?>
                            <?php if($hocKyMoDangKy && $hocKy->id == $hocKyMoDangKy->id): ?>
                                <?php if(now()->between($hocKy->ngay_bat_dau_dang_ky, $hocKy->ngay_ket_thuc_dang_ky)): ?>
                                    <span class="badge bg-success">Đang mở đăng ký</span>
                                <?php elseif(now() < $hocKy->ngay_bat_dau_dang_ky): ?>
                                    <span class="badge bg-warning">Chưa đến thời gian</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Đã hết thời gian</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-danger">Đã đóng</span>
                                <small class="d-block text-muted mt-1">Học kỳ chưa được mở đăng ký</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debug Info -->
            <?php if(isset($debugInfo) || config('app.debug')): ?>
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">🔍 Thông tin Debug</h6>
                </div>
                <div class="card-body">
                    <?php if(isset($debugInfo)): ?>
                        <?php if(isset($debugInfo['hoc_ky_hien_tai'])): ?>
                            <p class="mb-1"><strong>Học kỳ hiện tại:</strong> <?php echo e($debugInfo['hoc_ky_hien_tai']); ?></p>
                            <p class="mb-1"><strong>Học kỳ mở đăng ký:</strong> <?php echo e($debugInfo['hoc_ky_mo_dang_ky']); ?></p>
                        <?php else: ?>
                            <p class="mb-1"><strong>Học kỳ ID:</strong> <?php echo e($debugInfo['hoc_ky_id'] ?? 'N/A'); ?></p>
                            <p class="mb-1"><strong>Tổng lớp đang mở:</strong> <?php echo e($debugInfo['tong_lop_dang_mo'] ?? 0); ?> lớp</p>
                            <p class="mb-1"><strong>Tổng môn có lớp mở:</strong> <?php echo e($debugInfo['tong_mon_co_lop_mo'] ?? 0); ?> môn</p>
                            <p class="mb-1"><strong>Tổng CTK của chuyên ngành:</strong> <?php echo e($debugInfo['tong_chuong_trinh_khung'] ?? 0); ?> môn</p>
                            <p class="mb-1"><strong>CTK có lớp mở:</strong> <?php echo e($debugInfo['chuong_trinh_khung_co_lop_mo'] ?? 0); ?> môn</p>
                            <p class="mb-1"><strong>Chuyên ngành ID:</strong> <?php echo e($debugInfo['chuyen_nganh_id'] ?? 'N/A'); ?></p>
                            <p class="mb-1"><strong>Chuyên ngành:</strong> <?php echo e($debugInfo['chuyen_nganh'] ?? 'Chưa có'); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-1"><strong>Học kỳ ID:</strong> <?php echo e($hocKy->id ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Tổng lớp đang mở:</strong> <?php echo e($lopHocPhans->count()); ?> môn học</p>
                        <p class="mb-1"><strong>Môn trong CTK:</strong> <?php echo e($chuongTrinhKhung->count()); ?> môn</p>
                        <p class="mb-1"><strong>Chuyên ngành SV:</strong> <?php echo e($sinhVien->chuyenNganh->ten_chuyen_nganh ?? 'Chưa có'); ?></p>
                    <?php endif; ?>
                    <?php if($lopHocPhans->isNotEmpty()): ?>
                        <details class="mt-2">
                            <summary class="text-primary" style="cursor: pointer;">Xem danh sách lớp đang mở</summary>
                            <ul class="mt-2">
                                <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monId => $lops): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>Môn ID <?php echo e($monId); ?>: <?php echo e($lops->first()->monHoc->ten_mon ?? 'N/A'); ?> - <?php echo e($lops->count()); ?> lớp</li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </details>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Danh sách môn học -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách môn học có thể đăng ký</h5>
                </div>
                <div class="card-body">
                    <?php if($chuongTrinhKhung->isEmpty()): ?>
                        <div class="alert alert-info">
                            Chưa có chương trình khung cho chuyên ngành của bạn.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã MH</th>
                                        <th>Tên môn học</th>
                                        <th>Tín chỉ</th>
                                        <th>Học kỳ gợi ý</th>
                                        <th>Lớp học phần</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $chuongTrinhKhung; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $monHoc = $ct->monHoc;
                                            $daDangKy = in_array($monHoc->id, $monDaDangKy);
                                            $daHoc = in_array($monHoc->id, $monDaHoc);
                                            $daQua = in_array($monHoc->id, $monDaQua);
$lopHPs = $lopHocPhans[$monHoc->id] ?? collect();
                                        ?>
                                        <tr>
                                            <td><code><?php echo e($monHoc->ma_mon); ?></code></td>
                                            <td>
                                                <strong><?php echo e($monHoc->ten_mon); ?></strong>
                                                <?php if($ct->bat_buoc): ?>
                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($monHoc->so_tin_chi); ?></td>
                                            <td>
                                                <?php
                                                    // Lấy học kỳ gợi ý từ CTK hoặc object
                                                    $hocKyGoiY = null;
                                                    if (is_object($ct)) {
                                                        // Nếu là model ChuongTrinhKhung
                                                        if (method_exists($ct, 'getAttribute')) {
                                                            $hocKyGoiY = $ct->hoc_ky_goi_y;
                                                        } else {
                                                            // Nếu là stdClass object
                                                            $hocKyGoiY = $ct->hoc_ky_goi_y ?? null;
                                                        }
                                                    } else {
                                                        $hocKyGoiY = $ct['hoc_ky_goi_y'] ?? null;
                                                    }
                                                ?>
                                                <?php if($hocKyGoiY && $hocKyGoiY > 0): ?>
                                                    <span class="badge bg-info text-white">Kỳ <?php echo e($hocKyGoiY); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa xác định</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($lopHPs->isEmpty()): ?>
                                                    <span class="text-muted">Chưa mở lớp</span>
                                                <?php else: ?>
                                                    <small class="text-primary"><?php echo e($lopHPs->count()); ?> lớp</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($daQua): ?>
                                                    <span class="badge bg-success">Đã qua môn</span>
                                                <?php elseif($daHoc): ?>
                                                    <span class="badge bg-warning">Đang học</span>
                                                <?php elseif($daDangKy): ?>
                                                    <span class="badge bg-info">Đã đăng ký</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa học</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!$daQua && !$daDangKy && !$lopHPs->isEmpty()): ?>
                                                    <button type="button" class="btn btn-sm btn-primary btn-dang-ky"
                                                        data-mon-hoc-id="<?php echo e($monHoc->id); ?>"
                                                        data-ten-mon="<?php echo e($monHoc->ten_mon); ?>"
                                                        data-tin-chi="<?php echo e($monHoc->so_tin_chi); ?>">
                                                        <i class="bi bi-plus-circle"></i> Đăng ký
                                                    </button>
                                                <?php elseif($daDangKy): ?>
                                                    <?php
$dangKyId = $dangKyCollection->firstWhere(
                                                            'mon_hoc_id',
                                                            $monHoc->id,
                                                        )?->id;
                                                    ?>
                                                    <button type="button" class="btn btn-sm btn-danger btn-huy-dang-ky"
                                                        data-dang-ky-id="<?php echo e($dangKyId); ?>">
                                                        <i class="bi bi-x-circle"></i> Hủy
                                                    </button>
                                                <?php elseif($daQua): ?>
                                                    <small class="text-muted">Đã qua môn</small>
                                                <?php elseif($lopHPs->isEmpty()): ?>
                                                    <small class="text-muted">Chưa mở lớp</small>
                                                <?php else: ?>
                                                    <?php if(config('app.debug')): ?>
                                                        <small class="text-warning" title="daQua:<?php echo e($daQua?'Y':'N'); ?> daDK:<?php echo e($daDangKy?'Y':'N'); ?> lops:<?php echo e($lopHPs->count()); ?>">
                                                            Debug
                                                        </small>
                                                    <?php endif; ?>
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
        <?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <!-- Modal for registration confirmation -->
        <div class="modal fade" id="dangKyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận đăng ký môn học</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="modal-mon-ten" class="fw-bold"></p>
                        <p> Tín chỉ: <span id="modal-mon-tc"></span> </p>
                        <div id="modal-error" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" id="confirm-dang-ky" class="btn btn-primary">Đăng ký</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function() {
                let selectedMonId = null;
                let selectedTinChi = 0;

                function showError(text) {
                    $('#modal-error').removeClass('d-none').text(text);
                }

                function clearError() {
                    $('#modal-error').addClass('d-none').text('');
                }

                // Open modal when clicking Đăng ký
                $('.btn-dang-ky').on('click', function() {
                    selectedMonId = $(this).data('mon-hoc-id');
                    const tenMon = $(this).data('ten-mon');
selectedTinChi = parseInt($(this).data('tin-chi')) || 0;
                    const tongTinChi = <?php echo e($tongTinChiDaDangKy); ?>;
                    const tinChiToiDa = <?php echo e($tinChiToiDa); ?>;

                    if ((tongTinChi + selectedTinChi) > tinChiToiDa) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Vượt quá số tín chỉ!',
                            text: `Bạn đã đăng ký ${tongTinChi} TC. Môn này có ${selectedTinChi} TC sẽ vượt quá giới hạn ${tinChiToiDa} TC.`
                        });
                        return;
                    }

                    clearError();
                    $('#modal-mon-ten').text(tenMon);
                    $('#modal-mon-tc').text(selectedTinChi + ' TC');
                    const modal = new bootstrap.Modal(document.getElementById('dangKyModal'));
                    modal.show();
                });

                // Confirm from modal
                $('#confirm-dang-ky').on('click', function() {
                    if (!selectedMonId) return;

                    const $btn = $(this);
                    $btn.prop('disabled', true).text('Đang xử lý...');

                    $.ajax({
                        url: '<?php echo e(route('sinh-vien.dang-ky-mon-hoc.store')); ?>',
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            mon_hoc_id: selectedMonId,
                            hoc_ky_id: <?php echo e($hocKy->id); ?>

                        }
                    }).done(function(response) {
                        // Kiểm tra có warnings không
                        if (response.warnings && response.warnings.length > 0) {
                            // Hiển thị cảnh báo trước
                            let warningMessages = response.warnings.map(w => {
                                const icon = w.type === 'cai_thien_diem' ? '💡' : 
                                           w.type === 'hoc_lai' ? '📚' : '⚠️';
                                return `${icon} ${w.message}`;
                            }).join('<br><br>');
                            
                            Swal.fire({
                                icon: response.warnings[0].severity === 'info' ? 'info' : 'warning',
                                title: 'Thông báo',
                                html: warningMessages + '<br><br>' + response.message,
                                width: '600px',
                                confirmButtonText: 'Đã hiểu'
                            }).then(() => location.reload());
                        } else {
                            // Không có warning - hiển thị thông báo thành công bình thường
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.message
                            }).then(() => location.reload());
                        }
                    }).fail(function(xhr) {
                        console.error('Lỗi đăng ký:', xhr);
                        let message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                        
                        // Hiển thị chi tiết lỗi nếu có
                        if (xhr.responseJSON?.errors && Array.isArray(xhr.responseJSON.errors)) {
                            message += '\n\n' + xhr.responseJSON.errors.join('\n');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Không thể đăng ký',
                            text: message,
                            html: message.replace(/\n/g, '<br>'),
                            width: '500px'
                        });
                        
                        // Đóng modal
                        bootstrap.Modal.getInstance(document.getElementById('dangKyModal'))?.hide();
                    }).always(function() {
                        $btn.prop('disabled', false).text('Đăng ký');
                    });
                });

                // Hủy đăng ký (keeps existing behaviour)
                $('.btn-huy-dang-ky').on('click', function() {
                    const dangKyId = $(this).data('dang-ky-id');

                    Swal.fire({
                        title: 'Xác nhận hủy',
                        text: 'Bạn có chắc muốn hủy đăng ký môn này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hủy đăng ký',
                        cancelButtonText: 'Không',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
url: `/sinh-vien/dang-ky-mon-hoc/${dangKyId}`,
                                method: 'DELETE',
                                data: { _token: '<?php echo e(csrf_token()); ?>' }
                            }).done(function(response) {
                                Swal.fire({ icon: 'success', title: 'Đã hủy!', text: response.message })
                                    .then(() => location.reload());
                            }).fail(function(xhr) {
                                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                                Swal.fire({ icon: 'error', title: 'Lỗi', text: message });
                            });
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/sinhvien/dang-ky-mon-hoc/index.blade.php ENDPATH**/ ?>