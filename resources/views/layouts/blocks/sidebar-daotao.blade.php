<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('dao-tao.dashboard') }}">
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
                <li class="sidebar-title">ĐÀO TẠO - QUẢN LÝ NGHIỆP VỤ</li>

                <li
                    class="sidebar-item {{ Request::is('dao-tao') || Request::is('dao-tao/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 1. DANH MỤC & CTĐT -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-folder-fill"></i>
                        <span>Danh mục & CTĐT</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="{{route('dao-tao.khoa.index')}}">Quản lý Khoa</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.nganh.index') }}">Quản lý Ngành</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.chuyen-nganh.index') }}">Quản lý Chuyên
                                ngành</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.trinh-do.index') }}">Quản lý Trình độ</a>
                        </li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}">Trạng thái
                                học tập</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.phong-hoc.index') }}">Quản lý Phòng học</a>
                        </li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.mon-hoc.index') }}">Quản lý Môn học</a>
                        </li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.chuong-trinh-khung.index') }}">Chương trình
                                khung</a></li>
                    </ul>
                </li>

                <!-- 2. NIÊN KHÓA & HỌC KỲ -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar3"></i>
                        <span>Niên khóa & Học kỳ</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="{{ route('dao-tao.khoa-hoc.index') }}">Quản lý Khóa học</a>
                        </li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.hoc-ky.index') }}">Quản lý Học kỳ</a></li>
                    </ul>
                </li>

                <!-- 3. LỚP HÀNH CHÍNH & SINH VIÊN -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Lớp HC & Sinh viên</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="{{ route('dao-tao.lop-hanh-chinh.index') }}">Quản lý Lớp hành
                                chính</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.sinh-vien.index') }}">Quản lý Sinh viên</a>
                        </li>
                    </ul>
                </li>

                <!-- 4. GIẢNG VIÊN -->
                <li class="sidebar-item {{ Request::is('dao-tao/giang-vien*') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.giang-vien.index') }}" class='sidebar-link'>
                        <i class="bi bi-person-workspace"></i>
                        <span>Quản lý Giảng viên</span>
                    </a>
                </li>

                <!-- 5. LỚP HỌC PHẦN -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-book"></i>
                        <span>Lớp học phần</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Quản lý Lớp HP</a></li>
                        <li class="submenu-item"><a href="#">Phân công GV</a></li>
                    </ul>
                </li>

                <!-- 6. LỊCH DẠY -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar-check"></i>
                        <span>Lịch dạy</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Lịch học cố định (TKB)</a></li>
                        <li class="submenu-item"><a href="#">Lịch học chi tiết</a></li>
                    </ul>
                </li>

                <!-- 7. LỊCH THI -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-pencil-square"></i>
                        <span>Quản lý Lịch thi</span>
                    </a>
                </li>

                <!-- 8. ĐĂNG KÝ & XẾP LỚP -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-card-checklist"></i>
                        <span>Đăng ký & Xếp lớp</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Đăng ký môn học tạm</a></li>
                        <li class="submenu-item"><a href="#">Quy tắc xếp lớp</a></li>
                        <li class="submenu-item"><a href="#">Xếp lớp tự động</a></li>
                        <li class="submenu-item"><a href="#">Lịch sử xếp lớp</a></li>
                    </ul>
                </li>

                <!-- 9. CẤU HÌNH ĐIỂM -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-sliders"></i>
                        <span>Cấu hình điểm</span>
                    </a>
                </li>

                <!-- 10. HỌC PHÍ -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cash-coin"></i>
                        <span>Học phí</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Cấu hình học phí</a></li>
                        <li class="submenu-item"><a href="#">Học phí học kỳ</a></li>
                        <li class="submenu-item"><a href="#">Chi tiết học phí môn</a></li>
                        <li class="submenu-item"><a href="#">Lịch sử đóng học phí</a></li>
                    </ul>
                </li>

                <!-- 11. CẢNH BÁO HỌC VỤ -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Cảnh báo học vụ</span>
                    </a>
                </li>

                <!-- 12. THÔNG BÁO -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-bell"></i>
                        <span>Thông báo</span>
                    </a>
                </li>

                <!-- 13. BÁO CÁO & DASHBOARD -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="#">Thống kê sinh viên</a></li>
                        <li class="submenu-item"><a href="#">Thống kê đăng ký</a></li>
                        <li class="submenu-item"><a href="#">Thống kê giảng viên</a></li>
                        <li class="submenu-item"><a href="#">Thống kê phòng học</a></li>
                        <li class="submenu-item"><a href="#">Thống kê kết quả HT</a></li>
                        <li class="submenu-item"><a href="#">Thống kê học phí</a></li>
                        <li class="submenu-item"><a href="#">Thống kê cảnh báo</a></li>
                    </ul>
                </li>

                <li class="sidebar-title">TÀI KHOẢN</li>

                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
