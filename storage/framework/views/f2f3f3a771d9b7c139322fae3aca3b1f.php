<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="<?php echo e(route('dao-tao.dashboard')); ?>">
                        <h3 class="mb-0" style="color: #435ebe;">
                            <i class="bi bi-mortarboard-fill me-2"></i>S-MIS
                        </h3>
                    </a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">

                <li class="sidebar-title">ĐÀO TẠO - QUẢN LÝ NGHIỆP VỤ</li>

                
                <li
                    class="sidebar-item <?php echo e(Request::is('dao-tao') || Request::is('dao-tao/dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dao-tao.dashboard')); ?>" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                
                <li
                    class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/khoa', 'dao-tao/khoa/*', 'dao-tao/nganh*', 'dao-tao/chuyen-nganh*', 'dao-tao/mon-hoc*', 'dao-tao/chuong-trinh-khung*', 'dao-tao/trinh-do*', 'dao-tao/trang-thai-hoc-tap*', 'dao-tao/phong-hoc*', 'dao-tao/ca-hoc*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-folder-fill"></i>
                        <span>Danh mục & CTĐT</span>
                    </a>
                    <ul
                        class="submenu <?php echo e(Request::is('dao-tao/khoa', 'dao-tao/khoa/*', 'dao-tao/nganh*', 'dao-tao/chuyen-nganh*', 'dao-tao/mon-hoc*', 'dao-tao/chuong-trinh-khung*', 'dao-tao/trinh-do*', 'dao-tao/trang-thai-hoc-tap*', 'dao-tao/phong-hoc*', 'dao-tao/ca-hoc*') ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/khoa', 'dao-tao/khoa/*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.khoa.index')); ?>">Quản lý Khoa</a></li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/nganh*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.nganh.index')); ?>">Quản lý Ngành</a></li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/chuyen-nganh*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.chuyen-nganh.index')); ?>">Quản lý Chuyên
                                ngành</a></li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/trinh-do*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.trinh-do.index')); ?>">Quản lý Trình độ</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/trang-thai-hoc-tap*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.trang-thai-hoc-tap.index')); ?>">Trạng thái
                                học tập</a></li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/phong-hoc*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.phong-hoc.index')); ?>">Quản lý Phòng học</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/ca-hoc*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.ca-hoc.index')); ?>"><i class=""></i> Quản lý Ca học</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/mon-hoc*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.mon-hoc.index')); ?>">Quản lý Môn học</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/chuong-trinh-khung*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.chuong-trinh-khung.index')); ?>">Chương trình
                                khung</a></li>
                    </ul>
                </li>

                
                <li
                    class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/khoa-hoc*', 'dao-tao/hoc-ky*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-calendar3"></i>
                        <span>Niên khóa & Học kỳ</span>
                    </a>
                    <ul class="submenu <?php echo e(Request::is('dao-tao/khoa-hoc*', 'dao-tao/hoc-ky*') ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/khoa-hoc*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.khoa-hoc.index')); ?>">Quản lý Khóa học</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/hoc-ky*') ? 'active' : ''); ?>"><a
                                href="<?php echo e(route('dao-tao.hoc-ky.index')); ?>">Quản lý Học kỳ</a></li>
                    </ul>
                </li>

                
                <?php
                    $isQuanLyNguoiDung = Request::is('dao-tao/sinh-vien*') || Request::is('dao-tao/giang-vien*');
                ?>
                <li class="sidebar-item has-sub <?php echo e($isQuanLyNguoiDung ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-people-fill"></i>
                        <span>Quản lý Người dùng</span>
                    </a>
                    <ul class="submenu <?php echo e($isQuanLyNguoiDung ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/sinh-vien*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.sinh-vien.index')); ?>">
                                <i class="bi bi-people"></i> Quản lý Sinh viên
                            </a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/giang-vien*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.giang-vien.index')); ?>">
                                <i class="bi bi-person-workspace"></i> Quản lý Giảng viên
                            </a>
                        </li>
                    </ul>
                </li>

                
                <?php
                    $isLopHocPhan = Request::is('dao-tao/lop-hoc-phan*') && 
                                   !Request::is('dao-tao/lop-hoc-phan/*/lich-co-dinh*') && 
                                   !Request::is('dao-tao/lop-hoc-phan/*/lich-chi-tiet*');
                ?>
                <li class="sidebar-item has-sub <?php echo e($isLopHocPhan ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-book"></i>
                        <span>Lớp học phần</span>
                    </a>
                    <ul class="submenu <?php echo e($isLopHocPhan ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/lop-hoc-phan') || (Request::is('dao-tao/lop-hoc-phan/*') && Request::segment(3) !== 'lich-co-dinh' && Request::segment(3) !== 'lich-chi-tiet' && Request::segment(3) !== 'phan-cong' && Request::segment(3) !== 'cau-hinh-diem') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.lop-hoc-phan.index')); ?>">Quản lý Lớp học phần</a>
                        </li>
                    </ul>
                </li>

                
                <li class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/lich-thi*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-pencil-square"></i>
                        <span>Quản lý Lịch thi</span>
                    </a>
                    <ul class="submenu <?php echo e(Request::is('dao-tao/lich-thi*') ? 'active' : ''); ?>">
                        <li
                            class="submenu-item <?php echo e(Request::is('dao-tao/lich-thi') && !Request::is('dao-tao/lich-thi/create') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.lich-thi.index')); ?>">Danh sách Lịch thi</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/lich-thi/create') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.lich-thi.create')); ?>">Tạo Lịch thi mới</a>
                        </li>
                    </ul>
                </li>

                
                <li class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/xep-lop*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-card-checklist"></i>
                        <span>Đăng ký & Xếp lớp</span>
                    </a>
                    <ul class="submenu <?php echo e(Request::is('dao-tao/xep-lop*') ? 'active' : ''); ?>">
                        <li
                            class="submenu-item <?php echo e(Request::is('dao-tao/xep-lop') && !Request::is('dao-tao/xep-lop/waiting-list') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.xep-lop.index')); ?>">Quản lý xếp lớp</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/xep-lop/waiting-list') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.xep-lop.waiting-list')); ?>">Danh sách chờ</a>
                        </li>
                    </ul>
                </li>

                
                <?php
                    $isDuyetDiem = Request::is('dao-tao/duyet-diem*');
                    $isQuanLyGuiDiem = Request::is('dao-tao/duyet-diem/quan-ly-gui-diem*');
                    $isDuyetDiemIndex = Request::is('dao-tao/duyet-diem') && !$isQuanLyGuiDiem && !Request::is('dao-tao/duyet-diem/*/duyet');
                ?>
                <li class="sidebar-item has-sub <?php echo e($isDuyetDiem ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Duyệt điểm</span>
                    </a>
                    <ul class="submenu <?php echo e($isDuyetDiem ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e($isDuyetDiemIndex ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.duyet-diem.index')); ?>">Danh sách duyệt điểm</a>
                        </li>
                        <li class="submenu-item <?php echo e($isQuanLyGuiDiem ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.duyet-diem.quan-ly-gui-diem')); ?>">Quản lý mở/đóng gửi điểm</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/hoc-phi*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-cash-coin"></i>
                        <span>Học phí</span>
                    </a>
                    <ul class="submenu <?php echo e(Request::is('dao-tao/hoc-phi*') ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/hoc-phi/cau-hinh*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.hoc-phi.cau-hinh.index')); ?>">Cấu hình học phí</a>
                        </li>
                        <li
                            class="submenu-item <?php echo e(Request::is('dao-tao/hoc-phi') && !Request::is('dao-tao/hoc-phi/cau-hinh*', 'dao-tao/hoc-phi/statistics', 'dao-tao/hoc-phi/overdue') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.hoc-phi.index')); ?>">Quản lý học phí</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/hoc-phi/statistics') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.hoc-phi.statistics')); ?>">Thống kê học phí</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/hoc-phi/overdue') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.hoc-phi.overdue')); ?>">Nợ quá hạn</a>
                        </li>
                    </ul>
                </li>

                
                <li
                    class="sidebar-item has-sub <?php echo e(Request::is('dao-tao/thong-bao*', 'dao-tao/mau-thong-bao*') ? 'active' : ''); ?>">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-bell-fill"></i>
                        <span>Thông báo hệ thống</span>
                    </a>
                    <ul
                        class="submenu <?php echo e(Request::is('dao-tao/thong-bao*', 'dao-tao/mau-thong-bao*') ? 'active' : ''); ?>">
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/thong-bao*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.thong-bao.index')); ?>">Quản lý thông báo</a>
                        </li>
                        <li class="submenu-item <?php echo e(Request::is('dao-tao/mau-thong-bao*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dao-tao.mau-thong-bao.index')); ?>">Mẫu thông báo tự động</a>
                        </li>
                    </ul>
                </li>

                
                <li class="sidebar-item <?php echo e(Request::is('dao-tao/canh-bao-hoc-vu*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dao-tao.canh-bao-hoc-vu.index')); ?>" class="sidebar-link">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        <span>Cảnh báo Học vụ</span>
                        <?php
                            $soCanhBaoChuaXuLy = \App\Models\CanhBaoHocVu::where('trang_thai', 'chua_xu_ly')->count();
                        ?>
                        <?php if($soCanhBaoChuaXuLy > 0): ?>
                            <span class="badge bg-danger ms-auto"><?php echo e($soCanhBaoChuaXuLy); ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                
                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.sinh-vien')); ?>">Thống kê sinh
                                viên</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.dang-ky')); ?>">Thống kê đăng
                                ký</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.tai-giang-vien')); ?>">Thống kê
                                giảng viên</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.phong-hoc')); ?>">Thống kê phòng
                                học</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.ket-qua')); ?>">Thống kê kết quả
                                học tập</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.hoc-phi')); ?>">Thống kê học
                                phí</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.canh-bao')); ?>">Thống kê cảnh
                                báo</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.diem-danh')); ?>">Thống kê điểm
                                danh</a></li>
                        <li class="submenu-item"><a href="<?php echo e(route('dao-tao.bao-cao.xep-lop')); ?>">Thống kê xếp
                                lớp</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Admin\DATN_2025\resources\views/layouts/blocks/sidebar-daotao.blade.php ENDPATH**/ ?>