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

                <!-- LỚP GIẢNG DẠY -->
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

                <!-- 6. CẤU HÌNH ĐIỂM -->

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
                            <a href="{{ route('giangvien.lich-thi.lich-coi-thi') }}">Lịch coi thi</a>
                        </li>
                    </ul>
                </li>


                <!-- PHASE 10: THÔNG BÁO -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/thong-bao*') || Request::is('giang-vien/yeu-cau-diem-danh-bu*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-bell-fill"></i>
                        <span>Thông báo</span>
                        <span class="badge bg-danger ms-auto" id="notification-badge" style="display: none;">0</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/thong-bao*') || Request::is('giang-vien/yeu-cau-diem-danh-bu*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('giang-vien/thong-bao*') && !Request::is('giang-vien/yeu-cau-diem-danh-bu*') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.thong-bao.index') }}">Danh sách thông báo</a>
                        </li>
                        <li class="submenu-item {{ Request::is('giang-vien/yeu-cau-diem-danh-bu*') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.yeu-cau-diem-danh-bu.index') }}">Yêu cầu điểm danh bù</a>
                        </li>
                    </ul>
                </li>

                <!-- PHASE 8.5: CẢNH BÁO HỌC VỤ -->
                <li class="sidebar-item {{ Request::is('giang-vien/canh-bao-hoc-vu*') ? 'active' : '' }}">
                    <a href="{{ route('giangvien.canh-bao-hoc-vu.index') }}" class='sidebar-link'>
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Cảnh báo học vụ</span>
                        @php
                            $user = auth()->user();
                            $giangVien = $user->giangVien ?? \App\Models\GiangVien::where('user_id', $user->id)->first();
                            $sinhVienIds = [];
                            
                            if ($giangVien) {
                                // Sinh viên trong lớp giảng dạy (qua bảng trung gian)
                                $lopHocPhanIds = \DB::table('lop_hoc_phan_giang_vien')
                                    ->where('giang_vien_id', $giangVien->id)
                                    ->pluck('lop_hoc_phan_id');
                                    
                                if ($lopHocPhanIds->isNotEmpty()) {
                                    $svTrongLopHocPhan = \DB::table('lop_hoc_phan_sinh_vien')
                                        ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                                        ->pluck('sinh_vien_id')
                                        ->toArray();
                                    $sinhVienIds = array_merge($sinhVienIds, $svTrongLopHocPhan);
                                }
                                
                                $sinhVienIds = array_unique($sinhVienIds);
                            }
                            
                            $canhBaoCount = !empty($sinhVienIds) 
                                ? \App\Models\CanhBaoHocVu::whereIn('sinh_vien_id', $sinhVienIds)
                                    ->where('da_xu_ly', false)
                                    ->count() 
                                : 0;
                        @endphp
                        @if($canhBaoCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $canhBaoCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- 12. BÁO CÁO GIẢNG DẠY -->
                <li class="sidebar-item has-sub {{ Request::is('giang-vien/bao-cao*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-graph-up"></i>
                        <span>Báo cáo giảng dạy</span>
                    </a>
                    <ul class="submenu {{ Request::is('giang-vien/bao-cao*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('giang-vien/bao-cao') && !Request::is('giang-vien/bao-cao/*') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.bao-cao.index') }}">Tổng quan</a>
                        </li>
                        <li class="submenu-item {{ Request::is('giang-vien/bao-cao/phan-tich-diem') ? 'active' : '' }}">
                            <a href="{{ route('giangvien.bao-cao.phan-tich-diem') }}">Phân tích điểm</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
