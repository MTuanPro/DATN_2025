<?php $__env->startSection('title', 'Yêu cầu điểm danh bù'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Yêu cầu điểm danh bù</h3>
                <p class="text-subtitle text-muted">Xem xét và duyệt yêu cầu điểm danh bù từ sinh viên</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Yêu cầu điểm danh bù</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        
        <div class="card mb-3">
            <div class="card-body py-3">
                <form method="GET" action="<?php echo e(route('giangvien.yeu-cau-diem-danh-bu.index')); ?>" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="trang_thai" class="form-label mb-1">Trạng thái</label>
                        <select name="trang_thai" id="trang_thai" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Tất cả --</option>
                            <option value="cho_duyet" <?php echo e(request('trang_thai') == 'cho_duyet' ? 'selected' : ''); ?>>Chờ duyệt</option>
                            <option value="da_duyet" <?php echo e(request('trang_thai') == 'da_duyet' ? 'selected' : ''); ?>>Đã duyệt</option>
                            <option value="tu_choi" <?php echo e(request('trang_thai') == 'tu_choi' ? 'selected' : ''); ?>>Từ chối</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if($yeuCaus->isEmpty()): ?>
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Không có yêu cầu điểm danh bù nào.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sinh viên</th>
                                    <th>Môn học</th>
                                    <th>Ngày học</th>
                                    <th>Lý do</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $yeuCaus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $yc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($yeuCaus->firstItem() + $index); ?></td>
                                        <td>
                                            <strong><?php echo e($yc->sinhVien->ho_ten ?? 'N/A'); ?></strong><br>
                                            <small class="text-muted"><?php echo e($yc->sinhVien->ma_sinh_vien ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($yc->lichHocChiTiet->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?><br>
                                            <small class="text-muted"><?php echo e($yc->lichHocChiTiet->lopHocPhan->ma_lop_hp ?? 'N/A'); ?></small>
                                        </td>
                                        <td><?php echo e(\Carbon\Carbon::parse($yc->lichHocChiTiet->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y')); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="xemLyDo('<?php echo e($yc->ly_do); ?>')">
                                                <i class="bi bi-eye"></i> Xem
                                            </button>
                                        </td>
                                        <td><?php echo e(\Carbon\Carbon::parse($yc->ngay_gui)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i')); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($yc->trang_thai_badge); ?>">
                                                <?php echo e($yc->trang_thai_text); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($yc->trang_thai == 'cho_duyet'): ?>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="duyetYeuCau(<?php echo e($yc->id); ?>)">
                                                    <i class="bi bi-check-circle"></i> Duyệt
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="tuChoiYeuCau(<?php echo e($yc->id); ?>)">
                                                    <i class="bi bi-x-circle"></i> Từ chối
                                                </button>
                                            <?php elseif($yc->trang_thai == 'tu_choi' && $yc->ly_do_tu_choi): ?>
                                                <small class="text-muted">Lý do: <?php echo e($yc->ly_do_tu_choi); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <?php echo e($yeuCaus->links()); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>


<div class="modal fade" id="modalLyDo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lý do xin điểm danh bù</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="lyDoContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalTuChoi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ chối yêu cầu điểm danh bù</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTuChoi">
                <input type="hidden" id="yeuCauIdTuChoi" name="yeu_cau_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ly_do_tu_choi" name="ly_do_tu_choi" rows="4" required 
                                  placeholder="Vui lòng nhập lý do từ chối (tối đa 500 ký tự)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function xemLyDo(lyDo) {
    document.getElementById('lyDoContent').textContent = lyDo;
    const modal = new bootstrap.Modal(document.getElementById('modalLyDo'));
    modal.show();
}

function duyetYeuCau(id) {
    Swal.fire({
        title: 'Xác nhận',
        text: 'Bạn có chắc muốn duyệt yêu cầu điểm danh bù này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Có, duyệt',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo e(url('giang-vien/yeu-cau-diem-danh-bu')); ?>/${id}/duyet`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Lỗi!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
            });
        }
    });
}

function tuChoiYeuCau(id) {
    document.getElementById('yeuCauIdTuChoi').value = id;
    document.getElementById('ly_do_tu_choi').value = '';
    const modal = new bootstrap.Modal(document.getElementById('modalTuChoi'));
    modal.show();
}

document.getElementById('formTuChoi').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('yeuCauIdTuChoi').value;
    const formData = {
        ly_do_tu_choi: document.getElementById('ly_do_tu_choi').value,
        _token: '<?php echo e(csrf_token()); ?>'
    };
    
    fetch(`<?php echo e(url('giang-vien/yeu-cau-diem-danh-bu')); ?>/${id}/tu-choi`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Thành công!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Lỗi!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/yeu-cau-diem-danh-bu/index.blade.php ENDPATH**/ ?>