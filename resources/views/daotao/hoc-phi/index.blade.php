@extends('layouts.layout-daotao')

@section('title', 'Quản lý Học phí')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Học phí</h3>
                <p class="text-subtitle text-muted">Quản lý học phí sinh viên - PHASE 8</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Học phí</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('dao-tao.hoc-phi.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="MSSV, họ tên..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="hoc_ky_id" class="form-select form-select-sm">
                            <option value="">-- Học kỳ --</option>
                            @foreach ($hocKys as $hk)
                            <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="trang_thai" class="form-select form-select-sm">
                            <option value="">-- Trạng thái --</option>
                            <option value="chua_nop" {{ request('trang_thai') == 'chua_nop' ? 'selected' : '' }}>Chưa nộp</option>
                            <option value="da_nop_mot_phan" {{ request('trang_thai') == 'da_nop_mot_phan' ? 'selected' : '' }}>Nộp 1 phần</option>
                            <option value="da_nop_du" {{ request('trang_thai') == 'da_nop_du' ? 'selected' : '' }}>Đã nộp đủ</option>
                            <option value="qua_han" {{ request('trang_thai') == 'qua_han' ? 'selected' : '' }}>Quá hạn</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                    </div>
                </form>



                <!-- <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('dao-tao.hoc-phi.index') }}" method="GET" class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="MSSV, họ tên..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="hoc_ky_id" class="form-select form-select-sm">
                                        <option value="">-- Học kỳ --</option>
                                        @foreach ($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="trang_thai" class="form-select form-select-sm">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="chua_nop" {{ request('trang_thai') == 'chua_nop' ? 'selected' : '' }}>Chưa nộp</option>
                                        <option value="da_nop_mot_phan" {{ request('trang_thai') == 'da_nop_mot_phan' ? 'selected' : '' }}>Nộp 1 phần</option>
                                        <option value="da_nop_du" {{ request('trang_thai') == 'da_nop_du' ? 'selected' : '' }}>Đã nộp đủ</option>
                                        <option value="qua_han" {{ request('trang_thai') == 'qua_han' ? 'selected' : '' }}>Quá hạn</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
 -->

                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>MSSV</th>
                                            <th>Họ tên</th>
                                            <th>Học kỳ</th>
                                            <th>Tổng số tiền</th>
                                            <th>Đã đóng</th>
                                            <th>Còn lại</th>
                                            <th>Hạn đóng</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($hocPhis as $index => $hp)
                                        <tr>
                                            <td>{{ $hocPhis->firstItem() + $index }}</td>
                                            <td><strong>{{ $hp->sinhVien->ma_sinh_vien }}</strong></td>
                                            <td>{{ $hp->sinhVien->ho_ten }}</td>
                                            <td>{{ $hp->hocKy->ten_hoc_ky }}</td>
                                            <td>{{ number_format($hp->tong_so_tien, 0, ',', '.') }} đ</td>
                                            <td><span class="text-success">{{ number_format($hp->so_tien_da_dong, 0, ',', '.') }} đ</span></td>
                                            <td><span class="text-danger">{{ number_format($hp->so_tien_con_lai, 0, ',', '.') }} đ</span></td>
                                            <td>{{ $hp->han_dong->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($hp->trang_thai == 'da_nop_du')
                                                <span class="badge bg-success">Đã nộp đủ</span>
                                                @elseif ($hp->trang_thai == 'da_nop_mot_phan')
                                                <span class="badge bg-warning">Nộp 1 phần</span>
                                                @elseif ($hp->trang_thai == 'qua_han')
                                                <span class="badge bg-danger">Quá hạn</span>
                                                @else
                                                <span class="badge bg-secondary">Chưa nộp</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dao-tao.hoc-phi.show', $hp->id) }}" class="btn btn-info btn-sm" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                                <p class="text-muted mt-2">Chưa có dữ liệu học phí</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($hocPhis->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $hocPhis->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
            @endsection