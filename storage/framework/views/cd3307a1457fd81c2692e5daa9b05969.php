<?php $__env->startSection('title', 'Thông báo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thông báo</h3>
                    <p class="text-subtitle text-muted">Quản lý thông báo của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Loại thông báo</label>
                            <select name="loai_thong_bao" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="tin_tuc" <?php echo e(request('loai_thong_bao') == 'tin_tuc' ? 'selected' : ''); ?>>Tin
                                    tức</option>
                                <option value="thong_bao_chung"
                                    <?php echo e(request('loai_thong_bao') == 'thong_bao_chung' ? 'selected' : ''); ?>>Thông báo chung
                                </option>
                                <option value="tin_gap" <?php echo e(request('loai_thong_bao') == 'tin_gap' ? 'selected' : ''); ?>>Tin
                                    gấp</option>
                                <option value="lich_hoc" <?php echo e(request('loai_thong_bao') == 'lich_hoc' ? 'selected' : ''); ?>>
                                    Lịch học</option>
                                <option value="lich_thi" <?php echo e(request('loai_thong_bao') == 'lich_thi' ? 'selected' : ''); ?>>
                                    Lịch thi</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="trang_thai_doc" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="chua_doc" <?php echo e(request('trang_thai_doc') == 'chua_doc' ? 'selected' : ''); ?>>
                                    Chưa đọc</option>
                                <option value="da_doc" <?php echo e(request('trang_thai_doc') == 'da_doc' ? 'selected' : ''); ?>>Đã đọc
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề..."
                                value="<?php echo e(request('search')); ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách thông báo -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách thông báo</h4>
                    <div>
                        <span class="badge bg-primary" id="unread-count"><?php echo e($chuaDocCount); ?> chưa đọc</span>
                        <button type="button" class="btn btn-sm btn-success ms-2" onclick="markAllAsRead()">
                            <i class="bi bi-check-all"></i> Đánh dấu tất cả đã đọc
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($thongBaos->count() > 0): ?>
                        <div class="list-group">
                            <?php $__currentLoopData = $thongBaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('giangvien.thong-bao.show', $tb->thongBao->id)); ?>"
                                    class="list-group-item list-group-item-action <?php echo e(!$tb->da_doc ? 'bg-light' : ''); ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <?php if(!$tb->da_doc): ?>
                                                <span class="badge bg-primary me-2">Mới</span>
                                            <?php endif; ?>
                                            <?php if($tb->thongBao->ghim_dau_trang): ?>
                                                <i class="bi bi-pin-angle-fill text-warning"></i>
                                            <?php endif; ?>
                                            <h5 class="mb-1 <?php echo e(!$tb->da_doc ? 'fw-bold' : ''); ?>">
                                                <?php echo e($tb->thongBao->tieu_de); ?>

                                            </h5>
                                            <p class="mb-1 text-muted small">
                                                <?php echo e(Str::limit($tb->thongBao->noi_dung, 150)); ?>

                                            </p>
                                            <div class="mt-2">
                                                <span class="badge bg-<?php echo e($tb->thongBao->loai_badge); ?> me-1">
                                                    <?php echo e($tb->thongBao->loai_thong_bao); ?>

                                                </span>
                                                <span class="badge bg-<?php echo e($tb->thongBao->muc_do_badge); ?>">
                                                    <?php echo e($tb->thongBao->muc_do_quan_trong); ?>

                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end ms-3">
                                            <small class="text-muted">
                                                <?php echo e($tb->thongBao->ngay_gui ? $tb->thongBao->ngay_gui->format('d/m/Y H:i') : ''); ?>

                                            </small>
                                            <?php if($tb->da_doc): ?>
                                                <br><small class="text-success">
                                                    <i class="bi bi-check-circle"></i> Đã đọc
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="mt-3">
                            <?php echo e($thongBaos->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
                            <p class="text-muted mt-3">Không có thông báo nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function markAllAsRead() {
            if (!confirm('Đánh dấu tất cả thông báo là đã đọc?')) return;

            fetch('<?php echo e(route('giangvien.thong-bao.mark-all-read')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra!');
                });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/thong-bao/index.blade.php ENDPATH**/ ?>