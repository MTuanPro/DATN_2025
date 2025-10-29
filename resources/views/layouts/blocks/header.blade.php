<header class="mb-3" style="padding-bottom: 4rem;">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>


<!-- Top header: notifications, avatar -->
<div class="top-header d-flex align-items-center justify-content-between px-3 py-2 bg-white shadow-sm mb-3">
    <div class="d-flex align-items-center">
        <!-- Search removed -->
    </div>

    <div class="d-flex align-items-center ms-auto">
        <button class="btn btn-link text-decoration-none text-muted me-2 d-none d-md-inline" title="Toggle dark mode">
            <i class="bi bi-moon"></i>
        </button>

        <!-- Notification Bell Dropdown -->
        <div class="dropdown me-2">
            <button class="btn btn-link position-relative text-muted" id="notificationDropdown"
                data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                <i class="bi bi-bell fs-5"></i>
                @if (isset($soThongBaoChuaDoc) && $soThongBaoChuaDoc > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $soThongBaoChuaDoc > 99 ? '99+' : $soThongBaoChuaDoc }}
                    </span>
                @endif
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown"
                style="width: 350px; max-height: 500px; overflow-y: auto;">
                <li class="dropdown-header d-flex justify-content-between align-items-center">
                    <strong>Thông báo</strong>
                    @if (isset($soThongBaoChuaDoc) && $soThongBaoChuaDoc > 0)
                        <span class="badge bg-primary rounded-pill">{{ $soThongBaoChuaDoc }} mới</span>
                    @endif
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                @if (isset($thongBaoChuaDoc) && $thongBaoChuaDoc->count() > 0)
                    @foreach ($thongBaoChuaDoc as $nguoiNhan)
                        @if ($nguoiNhan->thongBao)
                            @php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $showRoute = in_array('admin', $roles)
                                    ? 'admin.thong-bao.show'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.show'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.show'
                                            : 'sinhvien.thong-bao.show'));
                            @endphp
                            <li>
                                <a class="dropdown-item {{ !$nguoiNhan->da_doc ? 'bg-light' : '' }}"
                                    href="{{ route($showRoute, $nguoiNhan->thongBao->id) }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-2">
                                            <i
                                                class="bi bi-{{ $nguoiNhan->thongBao->loai_thong_bao == 'tin_gap'
                                                    ? 'exclamation-circle text-danger'
                                                    : ($nguoiNhan->thongBao->loai_thong_bao == 'tin_tuc'
                                                        ? 'newspaper text-info'
                                                        : 'megaphone text-primary') }} fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">{{ Str::limit($nguoiNhan->thongBao->tieu_de, 50) }}
                                            </h6>
                                            <p class="mb-1 text-muted small">
                                                {{ Str::limit(strip_tags($nguoiNhan->thongBao->noi_dung), 80) }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i>
                                                {{ $nguoiNhan->thongBao->ngay_gui->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endif
                    @endforeach

                    @php
                        $indexRoute = in_array('admin', $roles)
                            ? 'admin.thong-bao.index'
                            : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                ? 'dao-tao.thong-bao.index'
                                : (in_array('giang_vien', $roles)
                                    ? 'giangvien.thong-bao.index'
                                    : 'sinhvien.thong-bao.index'));
                    @endphp

                    <li>
                        <a class="dropdown-item text-center small text-primary" href="{{ route($indexRoute) }}">
                            <strong>Xem tất cả thông báo</strong>
                        </a>
                    </li>
                @else
                    <li>
                        <div class="dropdown-item text-center text-muted py-4">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                            <p class="mb-0">Không có thông báo mới</p>
                        </div>
                    </li>
                @endif
            </ul>
        </div>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="user" width="36" height="36"
                    class="rounded-circle me-2">
                <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Form logout ẩn -->
<form action="{{ route('logout') }}" method="POST" id="logout-form-header" style="display: none;">
    @csrf
</form>
