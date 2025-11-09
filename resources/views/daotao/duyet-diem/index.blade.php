@extends('layouts.layout-daotao')

@section('title', 'Duyệt điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Duyệt điểm</h3>
                    <p class="text-subtitle text-muted">Quản lý và duyệt điểm các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Duyệt điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Filter --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dao-tao.duyet-diem.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        @foreach ($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ $hocKyId == $hk->id ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="da_khoa_diem" {{ $trangThai == 'da_khoa_diem' ? 'selected' : '' }}>Đã
                                            khóa điểm</option>
                                        <option value="da_duyet_diem" {{ $trangThai == 'da_duyet_diem' ? 'selected' : '' }}>
                                            Đã duyệt</option>
                                        <option value="dang_hoc" {{ $trangThai == 'dang_hoc' ? 'selected' : '' }}>Đang học
                                        </option>
</select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách lớp --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách lớp học phần</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>SV có điểm</th>
                                    <th>Tiến độ</th>
                                    <th>Điểm TB</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhans as $lop)
                                    <tr>
                                        <td><strong>{{ $lop['ma_lop_hp'] }}</strong></td>
                                        <td>{{ $lop['ten_lop_hp'] }}</td>
                                        <td>{{ $lop['mon_hoc'] }}</td>
                                        <td>{{ $lop['hoc_ky'] }}</td>
                                        <td class="text-center">
                                            {{ $lop['sv_co_diem'] }}/{{ $lop['tong_sv'] }}
</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar 
                                                @if ($lop['ty_le'] >= 100) bg-success
                                                @elseif($lop['ty_le'] >= 50) bg-info
                                                @else bg-warning @endif"
                                                    role="progressbar" style="width: {{ $lop['ty_le'] }}%;">
                                                    {{ $lop['ty_le'] }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($lop['diem_tb'])
                                                <strong class="text-primary">{{ $lop['diem_tb'] }}</strong>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lop['trang_thai'] === 'da_khoa_diem')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @elseif($lop['trang_thai'] === 'da_duyet_diem')
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @elseif($lop['trang_thai'] === 'dang_hoc')
                                                <span class="badge bg-info">Đang học</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $lop['trang_thai'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('dao-tao.duyet-diem.show', $lop['id']) }}"
                                                class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Chưa có lớp học phần nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
</table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script>
        let table1 = document.querySelector('#table1');
        if (table1) {
            let dataTable = new simpleDatatables.DataTable(table1);
        }
    </script>
@endpush