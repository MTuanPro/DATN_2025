<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('sinhvien.dashboard') }}"><img src="{{ asset('assets/images/logo/logo.png') }}"
                            alt="Logo"></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">SINH VIÊN</li>

                <li
                    class="sidebar-item {{ Request::is('sinh-vien') || Request::is('sinh-vien/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('sinhvien.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 1. HỒ SƠ SINH VIÊN -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-badge"></i>
                        <span>Hồ sơ sinh viên</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Thông tin cơ bản</a></li>
                        <li class="submenu-item"><a href="#">Hồ sơ nhập học</a></li>
                        <li class="submenu-item"><a href="#">Ảnh đại diện & tài liệu</a></li>
                    </ul>
                </li>

                <!-- 2. CHƯƠNG TRÌNH ĐÀO TẠO -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Chương trình ĐT</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">CTĐT của lớp</a></li>
                        <li class="submenu-item"><a href="#">Điều kiện tốt nghiệp</a></li>
                    </ul>
                </li>

                <!-- 3. ĐĂNG KÝ HỌC PHẦN -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-journal-plus"></i>
                        <span>Đăng ký học phần</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Đăng ký HP</a></li>
                        <li class="submenu-item"><a href="#">Lịch sử đăng ký</a></li>
                        <li class="submenu-item"><a href="#">Học phần đã đăng ký</a></li>
                    </ul>
                </li>

                <!-- 4. LỚP HỌC PHẦN -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-book"></i>
                        <span>Lớp học phần</span>
                    </a>
                </li>

                <!-- 5. THỜI KHÓA BIỂU & LỊCH THI -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar-week"></i>
                        <span>TKB & Lịch thi</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Thời khóa biểu</a></li>
                        <li class="submenu-item"><a href="#">Lịch thi</a></li>
                        <li class="submenu-item"><a href="#">Xuất lịch (PDF/iCal)</a></li>
                    </ul>
                </li>

                <!-- 6. ĐIỂM DANH -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Lịch sử điểm danh</span>
                    </a>
                </li>

                <!-- 7. KẾT QUẢ HỌC TẬP -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-trophy"></i>
                        <span>Kết quả học tập</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Điểm từng học kỳ</a></li>
                        <li class="submenu-item"><a href="#">Điểm tổng kết</a></li>
                        <li class="submenu-item"><a href="#">Xếp loại học tập</a></li>
                        <li class="submenu-item"><a href="#">Xuất bảng điểm</a></li>
                    </ul>
                </li>

                <!-- 8. HỌC PHÍ -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cash-stack"></i>
                        <span>Học phí</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Công nợ học phí</a></li>
                        <li class="submenu-item"><a href="#">Lịch sử thanh toán</a></li>
                        <li class="submenu-item"><a href="#">Miễn giảm học phí</a></li>
                    </ul>
                </li>

                <!-- 9. CẢNH BÁO HỌC VỤ -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Cảnh báo học vụ</span>
                    </a>
                </li>

                <!-- 10. THÔNG BÁO -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-bell"></i>
                        <span>Thông báo</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Thông báo chung</a></li>
                        <li class="submenu-item"><a href="#">Thông báo lớp</a></li>
                        <li class="submenu-item"><a href="#">Thông báo cá nhân</a></li>
                    </ul>
                </li>

                <!-- 11. AI CHATBOT -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-robot"></i>
                        <span>AI Trợ lý ảo</span>
                    </a>
                </li>

                <!-- 12. TRA CỨU -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-search"></i>
                        <span>Tra cứu</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Tra học phần</a></li>
                        <li class="submenu-item"><a href="#">Tra giảng viên</a></li>
                        <li class="submenu-item"><a href="#">Tra phòng học</a></li>
                    </ul>
                </li>

                <!-- 13. XUẤT DỮ LIỆU -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-download"></i>
                        <span>Xuất dữ liệu</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Xuất bảng điểm</a></li>
                        <li class="submenu-item"><a href="#">Xuất TKB</a></li>
                        <li class="submenu-item"><a href="#">Giấy xác nhận SV</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
