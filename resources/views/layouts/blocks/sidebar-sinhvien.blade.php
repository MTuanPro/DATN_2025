<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('sinh-vien.dashboard') }}">
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
                <li class="sidebar-title">SINH VIÊN</li>

                <li
                    class="sidebar-item {{ Request::is('sinh-vien') || Request::is('sinh-vien/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('sinh-vien.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>



                <!-- 2. CHƯƠNG TRÌNH ĐÀO TẠO -->
                <li class="sidebar-item has-sub {{ Request::is('sinh-vien/chuong-trinh-dao-tao*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Chương trình ĐT</span>
                    </a>
                    <ul class="submenu {{ Request::is('sinh-vien/chuong-trinh-dao-tao*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('sinh-vien/chuong-trinh-dao-tao') && !Request::is('sinh-vien/chuong-trinh-dao-tao/*') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}">CTĐT của lớp</a>
                        </li>
                        <li class="submenu-item {{ Request::is('sinh-vien/chuong-trinh-dao-tao/dieu-kien-tot-nghiep') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.dieu-kien-tot-nghiep') }}">Điều kiện tốt nghiệp</a>
                        </li>
                    </ul>
                </li>

                <!-- 3. ĐĂNG KÝ HỌC PHẦN -->
                <li class="sidebar-item has-sub {{ Request::is('sinh-vien/dang-ky-mon-hoc*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-journal-plus"></i>
                        <span>Đăng ký học phần</span>
                    </a>
                    <ul class="submenu {{ Request::is('sinh-vien/dang-ky-mon-hoc*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('sinh-vien/dang-ky-mon-hoc') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.dang-ky-mon-hoc.index') }}">Đăng ký môn học</a>
                        </li>
                        <li
                            class="submenu-item {{ Request::is('sinh-vien/dang-ky-mon-hoc/my-registrations') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.dang-ky-mon-hoc.my-registrations') }}">Lịch sử đăng ký</a>
                        </li>
                    </ul>
                </li>

                <!-- 4. LỚP CỦA TÔI -->
                <li class="sidebar-item {{ Request::is('sinh-vien/lop-hoc-phan*') || Request::is('sinh-vien/diem-danh*') ? 'active' : '' }}">
                    <a href="{{ route('sinh-vien.lop-hoc-phan.index') }}" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Lớp của tôi</span>
                    </a>
                </li>

                <!-- 5. KẾT QUẢ HỌC TẬP -->
                @php
                    // Active khi là trang điểm hoặc cảnh báo học vụ (không phải điểm danh)
                    $isKetQuaHocTap = (Request::is('sinh-vien/diem*') && !Request::is('sinh-vien/diem-danh*')) || Request::is('sinh-vien/canh-bao-hoc-vu*');
                @endphp
                <li class="sidebar-item has-sub {{ $isKetQuaHocTap ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-trophy"></i>
                        <span>Kết quả học tập</span>
                    </a>
                    <ul class="submenu {{ $isKetQuaHocTap ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('sinh-vien/diem') && !Request::is('sinh-vien/diem/*') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.diem.index') }}">Điểm từng học kỳ</a>
                        </li>
                        <li class="submenu-item {{ Request::is('sinh-vien/diem/bang-diem') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.diem.bang-diem') }}">Bảng điểm tổng hợp</a>
                        </li>
                        <li class="submenu-item {{ Request::is('sinh-vien/canh-bao-hoc-vu*') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.canh-bao-hoc-vu.index') }}">
                                Cảnh báo học vụ
                                @php
                                    $user = auth()->user();
                                    $sinhVienData = $user->sinhVien ?? \App\Models\DaoTao\SinhVien::where('user_id', $user->id)->first();
                                    $soCanhBao = $sinhVienData ? \App\Models\CanhBaoHocVu::where('sinh_vien_id', $sinhVienData->id)
                                        ->where('da_xu_ly', false)
                                        ->count() : 0;
                                @endphp
                                @if($soCanhBao > 0)
                                    <span class="badge bg-danger ms-auto">{{ $soCanhBao }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 8. HỌC PHÍ -->
                <li class="sidebar-item has-sub {{ Request::is('sinh-vien/hoc-phi*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cash-stack"></i>
                        <span>Học phí</span>
                    </a>
                    <ul class="submenu {{ Request::is('sinh-vien/hoc-phi*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('sinh-vien/hoc-phi') && !Request::is('sinh-vien/hoc-phi/*') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.hoc-phi.index') }}">Công nợ học phí</a>
                        </li>
                        <li class="submenu-item {{ Request::is('sinh-vien/hoc-phi/huong-dan') ? 'active' : '' }}">
                            <a href="{{ route('sinh-vien.hoc-phi.huong-dan') }}">Hướng dẫn nộp học phí</a>
                        </li>
                    </ul>
                </li>

                <!-- PHASE 10: THÔNG BÁO -->
                <li class="sidebar-item {{ Request::is('sinh-vien/thong-bao*') ? 'active' : '' }}">
                    <a href="{{ route('sinh-vien.thong-bao.index') }}" class='sidebar-link'>
                        <i class="bi bi-bell-fill"></i>
                        <span>Thông báo</span>
                        <span class="badge bg-danger ms-auto" id="notification-badge" style="display: none;">0</span>
                    </a>
                </li>

                <!-- 12.5. AI CHATBOT -->
                <li class="sidebar-item {{ Request::is('sinh-vien/chatbot*') ? 'active' : '' }}">
                    <a href="{{ route('sinh-vien.chatbot.index') }}" class='sidebar-link'>
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>AI Chat Bot</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
