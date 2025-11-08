<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('giangvien.dashboard') }}">
                        <h3 class="mb-0" style="color: #435ebe;"><i class="bi bi-mortarboard-fill me-2"></i>S-MIS</h3>
                    </a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">GIẢNG VIÊN</li>

                <li
                    class="sidebar-item {{ Request::is('giang-vien') || Request::is('giang-vien/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 1. THÔNG TIN CÁ NHÂN -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                </li>

                <!-- 2. LỚP PHỤ TRÁCH -->
                <li class="sidebar-item {{ Request::is('giang-vien/lop-giang-day*') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.lop-giang-day.index') }}" class='sidebar-link'>
                        <i class="bi bi-book"></i>
                        <span>Lớp giảng dạy</span>
                    </a>
                </li>

                <!-- 3. LỊCH DẠY -->
                <li class="sidebar-item {{ Request::is('giang-vien/lich-day*') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.schedule.index') }}" class='sidebar-link'>
                        <i class="bi bi-calendar-check"></i>
                        <span>Lịch dạy cá nhân</span>
                    </a>
                </li>

                <!-- 4. BUỔI HỌC -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/buoi-hoc*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar3"></i>
                        <span>Quản lý buổi học</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/buoi-hoc*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('giang-vien/buoi-hoc') && !Request::is('giang-vien/buoi-hoc/lich-su') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.buoi-hoc.index') }}">Danh sách buổi học</a>
                        </li>
                        <li class="submenu-item {{ Request::is('giang-vien/buoi-hoc/lich-su') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.buoi-hoc.history') }}">Lịch sử giảng dạy</a>
                        </li>
                    </ul>
                </li>

                <!-- 5. ĐIỂM DANH -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/diem-danh*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Điểm danh</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/diem-danh*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('giang-vien/diem-danh') && !Request::is('giang-vien/diem-danh/bao-cao') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.diem-danh.index') }}">Điểm danh sinh viên</a>
                        </li>
                        <li class="submenu-item {{ Request::is('giang-vien/diem-danh/bao-cao') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.diem-danh.report') }}">Báo cáo điểm danh</a>
                        </li>
                    </ul>
                </li>

                <!-- 6. CẤU HÌNH ĐIỂM -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-sliders"></i>
                        <span>Cấu hình điểm</span>
                    </a>
                </li>

                <!-- 7. NHẬP ĐIỂM -->
                <li class="sidebar-item {{ Request::is('giang-vien/nhap-diem*') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.nhap-diem.index') }}" class='sidebar-link'>
                        <i class="bi bi-pencil-square"></i>
                        <span>Nhập điểm</span>
                    </a>
                </li>

                <!-- 8. KẾT QUẢ HỌC TẬP -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-trophy"></i>
                        <span>Kết quả học tập</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Bảng điểm tổng kết</a></li>
                        <li class="submenu-item"><a href="#">Phân tích điểm</a></li>
                        <li class="submenu-item"><a href="#">Xuất bảng điểm</a></li>
                    </ul>
                </li>

                <!-- 9. THI & ĐỀ THI -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/lich-thi*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Thi & Đề thi</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/lich-thi*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('giang-vien/lich-thi') && !Request::is('giang-vien/lich-thi/lich-coi-thi') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.lich-thi.index') }}">Lịch thi</a>
                        </li>
                        <li class="submenu-item {{ Request::is('giang-vien/lich-thi/lich-coi-thi') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.lich-coi-thi') }}">Lịch coi thi</a>
                        </li>
                    </ul>
                </li>

                <!-- 10. LỚP CHỦ NHIỆM -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/lop-chu-nhiem*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-house-door"></i>
                        <span>Lớp chủ nhiệm</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/lop-chu-nhiem*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('giang-vien/lop-chu-nhiem') && !Request::is('giang-vien/lop-chu-nhiem/*') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.lop-chu-nhiem.index') }}">Danh sách lớp</a>
                        </li>
                    </ul>
                </li>

                <!-- PHASE 10: THÔNG BÁO -->
                <li class="sidebar-item {{ Request::is('giang-vien/thong-bao*') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.thong-bao.index') }}" class='sidebar-link'>
                        <i class="bi bi-bell-fill"></i>
                        <span>Thông báo</span>
                        <span class="badge bg-danger ms-auto" id="notification-badge" style="display: none;">0</span>
                    </a>
                </li>

                <!-- 12. BÁO CÁO -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo cá nhân</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
