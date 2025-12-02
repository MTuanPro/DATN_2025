<?php $__env->startSection('title', 'Báo cáo tiến độ giảng dạy'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Báo cáo tiến độ giảng dạy</h3>
                <p class="text-subtitle text-muted">Thống kê số buổi đã dạy/tổng buổi theo từng lớp</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.bao-cao.index')); ?>">Báo cáo</a></li>
                        <li class="breadcrumb-item active">Tiến độ giảng dạy</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Filters và Export -->
    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('giangvien.bao-cao.tien-do')); ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Học kỳ</label>
                    <select name="hoc_ky_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($hk->id); ?>" <?php echo e($hocKyId == $hk->id ? 'selected' : ''); ?>>
                            <?php echo e($hk->ten_hoc_ky); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lớp học phần</label>
                    <select name="lop_hoc_phan_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php $__currentLoopData = $allLopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($lhp->id); ?>" <?php echo e($lopHocPhanId == $lhp->id ? 'selected' : ''); ?>>
                            <?php echo e($lhp->ma_lop_hp); ?> - <?php echo e($lhp->monHoc->ten_mon ?? ''); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                    <a href="<?php echo e(route('giangvien.bao-cao.tien-do')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Xuất
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('giangvien.bao-cao.export-excel', ['loai' => 'tien-do'] + request()->all())); ?>">
                                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('giangvien.bao-cao.export-pdf', ['loai' => 'tien-do'] + request()->all())); ?>">
                                    <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng thống kê -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Thống kê tiến độ giảng dạy</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã lớp</th>
                            <th>Môn học</th>
                            <th>Học kỳ</th>
                            <th class="text-center">Tổng buổi</th>
                            <th class="text-center">Đã dạy</th>
                            <th class="text-center">Chưa dạy</th>
                            <th class="text-center">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $tongTongBuoi = 0; $tongDaDay = 0; $tongChuaDay = 0; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $thongKe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $tongTongBuoi += $item['tong_buoi'];
                            $tongDaDay += $item['da_day'];
                            $tongChuaDay += $item['chua_day'];
                        ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($item['lop']->ma_lop_hp); ?></strong></td>
                            <td><?php echo e($item['lop']->monHoc->ten_mon ?? ''); ?></td>
                            <td>
                                <?php if($item['lop']->hocKy): ?>
                                <?php echo e($item['lop']->hocKy->ten_hoc_ky); ?>

                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo e($item['tong_buoi']); ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?php echo e($item['da_day']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning"><?php echo e($item['chua_day']); ?></span>
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-<?php echo e($item['ti_le'] >= 75 ? 'success' : ($item['ti_le'] >= 50 ? 'warning' : 'danger')); ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo e($item['ti_le']); ?>%">
                                        <strong><?php echo e($item['ti_le']); ?>%</strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                        </tr>
                        <?php endif; ?>
                        <?php if(count($thongKe) > 0): ?>
                        <tr class="table-info fw-bold">
                            <td colspan="4" class="text-end">TỔNG CỘNG:</td>
                            <td class="text-center"><?php echo e($tongTongBuoi); ?></td>
                            <td class="text-center"><?php echo e($tongDaDay); ?></td>
                            <td class="text-center"><?php echo e($tongChuaDay); ?></td>
                            <td class="text-center">
                                <?php
                                    $tongTiLe = $tongTongBuoi > 0 ? round(($tongDaDay / $tongTongBuoi) * 100, 2) : 0;
                                ?>
                                <?php echo e($tongTiLe); ?>%
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <?php if(count($thongKe) > 0): ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Biểu đồ tiến độ</h4>
        </div>
        <div class="card-body">
            <div id="chart-tien-do"></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
<?php if(count($thongKe) > 0): ?>
// Dữ liệu cho biểu đồ
const categories = <?php echo json_encode(array_map(fn($item) => $item['lop']->ma_lop_hp, $thongKe), 512) ?>;
const daDayData = <?php echo json_encode(array_map(fn($item) => $item['da_day'], $thongKe), 512) ?>;
const chuaDayData = <?php echo json_encode(array_map(fn($item) => $item['chua_day'], $thongKe), 512) ?>;

// Biểu đồ cột
const chartOptions = {
    series: [
        {
            name: 'Đã dạy',
            data: daDayData
        },
        {
            name: 'Chưa dạy',
            data: chuaDayData
        }
    ],
    chart: {
        type: 'bar',
        height: 400,
        stacked: true,
        toolbar: {
            show: true
        }
    },
    colors: ['#28a745', '#ffc107'],
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: categories,
        labels: {
            rotate: -45
        }
    },
    yaxis: {
        title: {
            text: 'Số buổi'
        }
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " buổi"
            }
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'left'
    }
};

const chart = new ApexCharts(document.querySelector("#chart-tien-do"), chartOptions);
chart.render();
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/bao-cao/tien-do.blade.php ENDPATH**/ ?>