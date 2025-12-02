<?php $__env->startSection('title', 'Danh sách Feedback - AI Chatbot'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Danh sách Feedback</h3>
                    <p class="text-subtitle text-muted">Đánh giá từ sinh viên về chatbot</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Feedback</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Tổng feedback</h6>
                        <h3><?php echo e($stats['total']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Hữu ích</h6>
                        <h3><?php echo e($stats['huu_ich']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Không hữu ích</h6>
                        <h3><?php echo e($stats['khong_huu_ich']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Tỷ lệ hài lòng</h6>
                        <h3><?php echo e($stats['ty_le_huu_ich']); ?>%</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-0">Danh sách Feedback</h5>
                        <a href="<?php echo e(route('admin.ai-chatbot.feedback.analytics')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-graph-up"></i> Phân tích
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Tìm kiếm..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="danh_gia" class="form-select form-select-sm">
                                    <option value="">-- Đánh giá --</option>
                                    <option value="huu_ich" <?php echo e(request('danh_gia') == 'huu_ich' ? 'selected' : ''); ?>>Hữu ích</option>
                                    <option value="khong_huu_ich" <?php echo e(request('danh_gia') == 'khong_huu_ich' ? 'selected' : ''); ?>>Không hữu ích</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tu_ngay" class="form-control form-control-sm" 
                                       value="<?php echo e(request('tu_ngay')); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="den_ngay" class="form-control form-control-sm" 
                                       value="<?php echo e(request('den_ngay')); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm">Tìm</button>
                                <a href="<?php echo e(route('admin.ai-chatbot.feedback.index')); ?>" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sinh viên</th>
                                    <th>Tin nhắn</th>
                                    <th>Đánh giá</th>
                                    <th>Lý do</th>
                                    <th>Thời gian</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($fb->id); ?></td>
                                        <td>
                                            <strong><?php echo e($fb->sinhVien->ma_sinh_vien); ?></strong><br>
                                            <small><?php echo e($fb->sinhVien->ho_ten); ?></small>
                                        </td>
                                        <td><?php echo e(Str::limit($fb->message->noi_dung, 50)); ?></td>
                                        <td>
                                            <?php if($fb->danh_gia == 'huu_ich'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-hand-thumbs-up"></i> Hữu ích
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-hand-thumbs-down"></i> Không hữu ích
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($fb->ly_do): ?>
                                                <?php echo e(Str::limit($fb->ly_do, 40)); ?>

                                                <?php if(mb_strlen($fb->ly_do) > 40): ?>
                                                    <br>
                                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1 view-reason" data-reason="<?php echo e(e($fb->ly_do)); ?>">
                                                        Xem lý do
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?php echo e($fb->created_at->format('d/m/Y H:i')); ?></small></td>
                                        <td>
                                            <a href="<?php echo e(route('admin.ai-chatbot.feedback.show', $fb)); ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php echo e($feedbacks->links()); ?>

                </div>
            </div>

            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Top KB được đánh giá tốt</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0">
                                <?php $__currentLoopData = $stats['top_knowledge_good']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="mb-2">
                                        <strong><?php echo e($kb->cau_hoi_mau); ?></strong>
                                        <span class="badge bg-success"><?php echo e($kb->feedback_count); ?> lượt</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Top KB cần cải thiện</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0">
                                <?php $__currentLoopData = $stats['top_knowledge_bad']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="mb-2">
                                        <strong><?php echo e($kb->cau_hoi_mau); ?></strong>
                                        <span class="badge bg-danger"><?php echo e($kb->feedback_count); ?> lượt</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<!-- Modal to show full reason -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Lý do đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reasonModalBody">
                <!-- content injected by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
        // Attach click handler to buttons to open the reason modal
        $(document).on('click', '.view-reason', function() {
                var reason = $(this).data('reason') || '';
                $('#reasonModalBody').text(reason);
                var modal = new bootstrap.Modal(document.getElementById('reasonModal'));
                modal.show();
        });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/ai-chatbot/feedback/index.blade.php ENDPATH**/ ?>