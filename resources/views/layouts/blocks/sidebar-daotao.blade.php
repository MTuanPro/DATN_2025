<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('dao-tao.dashboard') }}">
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

                {{-- Dashboard --}}
                <li
                    class="sidebar-item {{ Request::is('dao-tao') || Request::is('dao-tao/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.dashboard') }}" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- 1. DANH MỤC & CTĐT --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/khoa', 'dao-tao/khoa/*', 'dao-tao/nganh*', 'dao-tao/chuyen-nganh*', 'dao-tao/mon-hoc*', 'dao-tao/chuong-trinh-khung*', 'dao-tao/trinh-do*', 'dao-tao/trang-thai-hoc-tap*', 'dao-tao/phong-hoc*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-folder-fill"></i>
                        <span>Danh mục & CTĐT</span>
                    </a>
                    <ul
                        class="submenu {{ Request::is('dao-tao/khoa', 'dao-tao/khoa/*', 'dao-tao/nganh*', 'dao-tao/chuyen-nganh*', 'dao-tao/mon-hoc*', 'dao-tao/chuong-trinh-khung*', 'dao-tao/trinh-do*', 'dao-tao/trang-thai-hoc-tap*', 'dao-tao/phong-hoc*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('dao-tao/khoa', 'dao-tao/khoa/*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.khoa.index') }}">Quản lý Khoa</a></li>
                        <li class="submenu-item {{ Request::is('dao-tao/nganh*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.nganh.index') }}">Quản lý Ngành</a></li>
                        <li class="submenu-item {{ Request::is('dao-tao/chuyen-nganh*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.chuyen-nganh.index') }}">Quản lý Chuyên
                                ngành</a></li>
                        <li class="submenu-item {{ Request::is('dao-tao/trinh-do*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.trinh-do.index') }}">Quản lý Trình độ</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/trang-thai-hoc-tap*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}">Trạng thái
                                học tập</a></li>
                        <li class="submenu-item {{ Request::is('dao-tao/phong-hoc*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.phong-hoc.index') }}">Quản lý Phòng học</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/mon-hoc*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.mon-hoc.index') }}">Quản lý Môn học</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/chuong-trinh-khung*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.chuong-trinh-khung.index') }}">Chương trình
                                khung</a></li>
                    </ul>
                </li>

                {{-- 2. NIÊN KHÓA & HỌC KỲ --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/khoa-hoc*', 'dao-tao/hoc-ky*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-calendar3"></i>
                        <span>Niên khóa & Học kỳ</span>
                    </a>
                    <ul class="submenu {{ Request::is('dao-tao/khoa-hoc*', 'dao-tao/hoc-ky*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('dao-tao/khoa-hoc*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.khoa-hoc.index') }}">Quản lý Khóa học</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/hoc-ky*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.hoc-ky.index') }}">Quản lý Học kỳ</a></li>
                    </ul>
                </li>

                {{-- 3. LỚP HÀNH CHÍNH & SINH VIÊN --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/lop-hanh-chinh*', 'dao-tao/sinh-vien*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>Lớp hành chính & Sinh viên</span>
                    </a>
                    <ul
                        class="submenu {{ Request::is('dao-tao/lop-hanh-chinh*', 'dao-tao/sinh-vien*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('dao-tao/lop-hanh-chinh*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.lop-hanh-chinh.index') }}">Quản lý Lớp hành
                                chính</a></li>
                        <li class="submenu-item {{ Request::is('dao-tao/sinh-vien*') ? 'active' : '' }}"><a
                                href="{{ route('dao-tao.sinh-vien.index') }}">Quản lý Sinh viên</a>
                        </li>
                    </ul>
                </li>

                {{-- 4. GIẢNG VIÊN --}}
                <li class="sidebar-item {{ Request::is('dao-tao/giang-vien*') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.giang-vien.index') }}"
                        class="sidebar-link {{ Request::is('dao-tao/giang-vien*') ? 'active' : '' }}">
                        <i class="bi bi-person-workspace"></i>
                        <span>Quản lý Giảng viên</span>
                    </a>
                </li>

                {{-- 5. LỚP HỌC PHẦN --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/lop-hoc-phan*', 'dao-tao/lich-co-dinh*', 'dao-tao/lich-chi-tiet*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-book"></i>
                        <span>Lớp học phần</span>
                    </a>
                    <ul
                        class="submenu {{ Request::is('dao-tao/lop-hoc-phan*', 'dao-tao/lich-co-dinh*', 'dao-tao/lich-chi-tiet*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('dao-tao/lop-hoc-phan*') && Request::segment(3) !== 'xem-lich' ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Quản lý Lớp học phần</a>
                        </li>
                    </ul>
                </li>

                {{-- 6. THỜI KHÓA BIỂU --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/lich-co-dinh*', 'dao-tao/lich-chi-tiet*', 'dao-tao/lop-hoc-phan*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-calendar-check"></i>
                        <span>Thời khóa biểu</span>
                    </a>
                    <ul
                        class="submenu {{ Request::is('dao-tao/lich-co-dinh*', 'dao-tao/lich-chi-tiet*', 'dao-tao/lop-hoc-phan*') ? 'active' : '' }}">
                        <li class="submenu-item"><a href="#">Lịch theo Phòng học <!-- Đang phát triển --></a></li>
                        <li class="submenu-item"><a href="#">Lịch theo Giảng viên <!-- Đang phát triển --></a>
                        </li>
                    </ul>
                </li>

                {{-- 7. LỊCH THI --}}
                <li class="sidebar-item has-sub {{ Request::is('dao-tao/lich-thi*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-pencil-square"></i>
                        <span>Quản lý Lịch thi</span>
                    </a>
                    <ul class="submenu {{ Request::is('dao-tao/lich-thi*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('dao-tao/lich-thi') && !Request::is('dao-tao/lich-thi/create') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.lich-thi.index') }}">Danh sách Lịch thi</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/lich-thi/create') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.lich-thi.create') }}">Tạo Lịch thi mới</a>
                        </li>
                    </ul>
                </li>

                {{-- 8. ĐĂNG KÝ & XẾP LỚP --}}
                <li class="sidebar-item has-sub {{ Request::is('dao-tao/xep-lop*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-card-checklist"></i>
                        <span>Đăng ký & Xếp lớp</span>
                    </a>
                    <ul class="submenu {{ Request::is('dao-tao/xep-lop*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ Request::is('dao-tao/xep-lop') && !Request::is('dao-tao/xep-lop/waiting-list') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.xep-lop.index') }}">Quản lý xếp lớp</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/xep-lop/waiting-list') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.xep-lop.waiting-list') }}">Danh sách chờ</a>
                        </li>
                    </ul>
                </li>

                {{-- 9. CẤU HÌNH ĐIỂM --}}
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-sliders"></i>
                        <span>Cấu hình điểm <!-- Đang cập nhật --></span>
                    </a>
                </li>

                {{-- 10. HỌC PHÍ --}}
                <li class="sidebar-item {{ Request::is('dao-tao/duyet-diem*') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.duyet-diem.index') }}" class="sidebar-link">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Duyệt điểm</span>
                    </a>
                </li>
                <li class="sidebar-item has-sub {{ Request::is('dao-tao/hoc-phi*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-cash-coin"></i>
                        <span>Học phí</span>
                    </a>
                    <ul class="submenu {{ Request::is('dao-tao/hoc-phi*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('dao-tao/hoc-phi/cau-hinh*') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.hoc-phi.cau-hinh.index') }}">Cấu hình học phí</a>
                        </li>
                        <li
                            class="submenu-item {{ Request::is('dao-tao/hoc-phi') && !Request::is('dao-tao/hoc-phi/cau-hinh*', 'dao-tao/hoc-phi/statistics', 'dao-tao/hoc-phi/overdue') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.hoc-phi.index') }}">Quản lý học phí</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/hoc-phi/statistics') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.hoc-phi.statistics') }}">Thống kê học phí</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/hoc-phi/overdue') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.hoc-phi.overdue') }}">Nợ quá hạn</a>
                        </li>
                    </ul>
                </li>

                {{-- PHASE 10: QUẢN LÝ THÔNG BÁO --}}
                <li
                    class="sidebar-item has-sub {{ Request::is('dao-tao/thong-bao*', 'dao-tao/mau-thong-bao*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-bell-fill"></i>
                        <span>Thông báo hệ thống</span>
                    </a>
                    <ul
                        class="submenu {{ Request::is('dao-tao/thong-bao*', 'dao-tao/mau-thong-bao*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('dao-tao/thong-bao*') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.thong-bao.index') }}">Quản lý thông báo</a>
                        </li>
                        <li class="submenu-item {{ Request::is('dao-tao/mau-thong-bao*') ? 'active' : '' }}">
                            <a href="{{ route('dao-tao.mau-thong-bao.index') }}">Mẫu thông báo tự động</a>
                        </li>
                    </ul>
                </li>

                {{-- PHASE 8.5: CẢNH BÁO HỌC VỤ --}}
                <li class="sidebar-item {{ Request::is('dao-tao/canh-bao-hoc-vu*') ? 'active' : '' }}">
                    <a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}" class="sidebar-link">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        <span>Cảnh báo Học vụ</span>
                        @php
                            $soCanhBaoChuaXuLy = \App\Models\CanhBaoHocVu::where('trang_thai', 'chua_xu_ly')->count();
                        @endphp
                        @if($soCanhBaoChuaXuLy > 0)
                            <span class="badge bg-danger ms-auto">{{ $soCanhBaoChuaXuLy }}</span>
                        @endif
                    </a>
                </li>

                {{-- 12. BÁO CÁO & THỐNG KÊ --}}
                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.sinh-vien') }}">Thống kê sinh
                                viên</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.dang-ky') }}">Thống kê đăng
                                ký</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.tai-giang-vien') }}">Thống kê
                                giảng viên</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.phong-hoc') }}">Thống kê phòng
                                học</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.ket-qua') }}">Thống kê kết quả
                                học tập</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.hoc-phi') }}">Thống kê học
                                phí</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.canh-bao') }}">Thống kê cảnh
                                báo</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.diem-danh') }}">Thống kê điểm
                                danh</a></li>
                        <li class="submenu-item"><a href="{{ route('dao-tao.bao-cao.xep-lop') }}">Thống kê xếp
                                lớp</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
