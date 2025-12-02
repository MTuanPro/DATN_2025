<?php $__env->startSection('title', 'Quản lý Knowledge Base - AI Chatbot'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Knowledge Base</h3>
                    <p class="text-subtitle text-muted">Quản lý cơ sở kiến thức cho AI Chatbot</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Knowledge Base</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="card-title mb-0">Danh sách Knowledge Base</h5>
                        </div>
                        <div class="col-md-8 text-end">
                            <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.statistics')); ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-graph-up"></i> Thống kê
                            </a>
                            <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.import.form')); ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-upload"></i> Import
                            </a>
                            <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.export')); ?><?php echo e(request()->getQueryString() ? '?' . request()->getQueryString() : ''); ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Export
                            </a>
                            <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form method="GET" action="<?php echo e(route('admin.ai-chatbot.knowledge-base.index')); ?>" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Tìm kiếm..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="chu_de" class="form-select form-select-sm">
                                    <option value="">-- Chủ đề --</option>
                                    <?php $__currentLoopData = $chuDeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chuDe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($chuDe); ?>" <?php echo e(request('chu_de') == $chuDe ? 'selected' : ''); ?>>
                                            <?php echo e($chuDe); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="danh_muc" class="form-select form-select-sm">
                                    <option value="">-- Danh mục --</option>
                                    <?php $__currentLoopData = $danhMucList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danhMuc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($danhMuc); ?>" <?php echo e(request('danh_muc') == $danhMuc ? 'selected' : ''); ?>>
                                            <?php echo e($danhMuc); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="kich_hoat" class="form-select form-select-sm">
                                    <option value="">-- Trạng thái --</option>
                                    <option value="1" <?php echo e(request('kich_hoat') === '1' ? 'selected' : ''); ?>>Kích hoạt</option>
                                    <option value="0" <?php echo e(request('kich_hoat') === '0' ? 'selected' : ''); ?>>Vô hiệu hóa</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                                <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Chủ đề</th>
                                    <th width="25%">Câu hỏi mẫu</th>
                                    <th width="15%">Từ khóa</th>
                                    <th width="8%">Ưu tiên</th>
                                    <th width="8%">Truy cập</th>
                                    <th width="8%">Hữu ích</th>
                                    <th width="8%">Trạng thái</th>
                                    <th width="8%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $knowledgeBase; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($kb->id); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($kb->chu_de); ?></span>
                                            <?php if($kb->danh_muc): ?>
                                                <br><small class="text-muted"><?php echo e($kb->danh_muc); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(Str::limit($kb->cau_hoi_mau, 60)); ?></td>
                                        <td><small class="text-muted"><?php echo e(Str::limit($kb->tu_khoa, 40)); ?></small></td>
                                        <td><span class="badge bg-warning"><?php echo e($kb->do_uu_tien); ?></span></td>
                                        <td><?php echo e($kb->luot_truy_cap); ?></td>
                                        <td><?php echo e($kb->huu_ich); ?> <small>(<?php echo e($kb->tyLeHuuIch()); ?>%)</small></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-status" type="checkbox" 
                                                       data-id="<?php echo e($kb->id); ?>" <?php echo e($kb->kich_hoat ? 'checked' : ''); ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.show', $kb)); ?>" 
                                                   class="btn btn-info" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.ai-chatbot.knowledge-base.edit', $kb)); ?>" 
                                                   class="btn btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                        <form method="POST" action="<?php echo e(route('admin.ai-chatbot.knowledge-base.destroy', $kb)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa kiến thức này?');">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-center">
                        <?php echo e($knowledgeBase->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>

    
    <form id="delete-form" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Toggle status
    $('.toggle-status').on('change', function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        
        $.ajax({
            url: `/admin/ai-chatbot/knowledge-base/${id}/toggle-activate`,
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra!');
            }
        });
    });

    // Delete
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        
        if (confirm('Bạn có chắc chắn muốn xóa kiến thức này?')) {
            const form = $('#delete-form');
            form.attr('action', `/admin/ai-chatbot/knowledge-base/${id}`);
            form.submit();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/ai-chatbot/knowledge-base/index.blade.php ENDPATH**/ ?>