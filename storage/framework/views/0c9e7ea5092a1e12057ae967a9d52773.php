<?php $__env->startSection('title', 'Phân tích Feedback - AI Chatbot'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <h3>Phân tích Feedback</h3>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Thống kê 7 ngày gần nhất</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Ngày</th><th>Hữu ích</th><th>Không hữu ích</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $dailyStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($d['date']); ?></td>
                                    <td><?php echo e($d['huu_ich']); ?></td>
                                    <td><?php echo e($d['khong_huu_ich']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Thống kê theo chủ đề</div>
                <div class="card-body">
                    <ul>
                        <?php $__currentLoopData = $statsByChuDe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($s->chu_de); ?> - Hữu ích: <?php echo e($s->huu_ich); ?> | Không hữu ích: <?php echo e($s->khong_huu_ich); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <a href="<?php echo e(route('admin.ai-chatbot.feedback.index')); ?>" class="btn btn-secondary mt-3">Quay lại</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/ai-chatbot/feedback/analytics.blade.php ENDPATH**/ ?>