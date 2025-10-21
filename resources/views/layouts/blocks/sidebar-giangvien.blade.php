<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('giangvien.dashboard') }}"><img src="{{ asset('assets/images/logo/logo.png') }}"
                            alt="Logo"></a>
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
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-book"></i>
                        <span>Lớp phụ trách</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Danh sách lớp HP</a></li>
                        <li class="submenu-item"><a href="#">Danh sách sinh viên</a></li>
                    </ul>
                </li>

                <!-- 3. LỊCH DẠY -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar-check"></i>
                        <span>Lịch dạy cá nhân</span>
                    </a>
                </li>

                <!-- 4. BUỔI HỌC -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar3"></i>
                        <span>Quản lý buổi học</span>
                    </a>
                </li>

                <!-- 5. ĐIỂM DANH -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Điểm danh</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Điểm danh sinh viên</a></li>
                        <li class="submenu-item"><a href="#">Báo cáo điểm danh</a></li>
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
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-pencil-square"></i>
                        <span>Nhập điểm</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Nhập điểm thành phần</a></li>
                        <li class="submenu-item"><a href="#">Kiểm tra nhập điểm</a></li>
                    </ul>
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
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Thi & Đề thi</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Lịch thi</a></li>
                        <li class="submenu-item"><a href="#">Đề thi & Đáp án</a></li>
                    </ul>
                </li>

                <!-- 10. LỚP CHỦ NHIỆM -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-house-door"></i>
                        <span>Lớp chủ nhiệm</span>
                    </a>
                </li>

                <!-- 11. THÔNG BÁO -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-bell"></i>
                        <span>Thông báo</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Nhận thông báo</a></li>
                        <li class="submenu-item"><a href="#">Gửi thông báo</a></li>
                    </ul>
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
