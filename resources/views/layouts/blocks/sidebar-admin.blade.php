<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/images/logo/logo.png') }}"
                            alt="Logo"></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">ADMIN - QUẢN TRỊ HỆ THỐNG</li>

                <li class="sidebar-item {{ Request::is('admin') || Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 1. TÀI KHOẢN & PHÂN QUYỀN -->
                <li
                    class="sidebar-item has-sub {{ Request::is('admin/users*') || Request::is('admin/roles*') || Request::is('admin/permissions*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Tài khoản & Phân quyền</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="#">Quản lý Users</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Quản lý Vai trò</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Gán Vai trò</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Nhóm quyền</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Quản lý Quyền</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Map Vai trò - Quyền</a>
                        </li>
                    </ul>
                </li>

                <!-- 2. NHÂN SỰ HỆ THỐNG -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>Nhân sự hệ thống</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="#">Quản lý Admin</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Quản lý Đào tạo</a>
                        </li>
                    </ul>
                </li>

                <!-- 3. THÔNG BÁO HỆ THỐNG -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-megaphone-fill"></i>
                        <span>Thông báo hệ thống</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="#">Quản lý Thông báo</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Người nhận</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Mẫu thông báo tự động</a>
                        </li>
                    </ul>
                </li>

                <!-- 4. AI CHATBOT -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-robot"></i>
                        <span>AI Chatbot</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="#">Knowledge Base</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Hội thoại</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Feedback</a>
                        </li>
                    </ul>
                </li>

                <!-- 5. NHẬT KÝ HOẠT ĐỘNG -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Nhật ký hoạt động</span>
                    </a>
                </li>

                <!-- 6. BÁO CÁO & THỐNG KÊ -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>
                </li>

                <!-- 7. CÀI ĐẶT & VẬN HÀNH -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Cài đặt & Vận hành</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="#">Cấu hình chung</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Cấu hình Email</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Cấu hình SMS</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Cấu hình thông báo</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Cấu hình bảo mật</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Quản lý dữ liệu</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#">Quản lý Logs</a>
                        </li>
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
