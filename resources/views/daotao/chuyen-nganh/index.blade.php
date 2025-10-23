@extends('layouts.layout-daotao')

@section('content')
    <div class="container">
        <h2>Danh sách Chuyên ngành</h2>

        {{-- Nút thêm mới --}}
        <a href="{{ route('dao-tao.chuyen-nganh.create') }}" class="btn btn-primary mb-3">+ Thêm Chuyên ngành</a>

        {{-- Form tìm kiếm và lọc --}}
        <form action="{{ route('dao-tao.chuyen-nganh.index') }}" method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                    placeholder="Tìm theo mã hoặc tên...">
            </div>
            <div class="col-md-3">
                <select name="nganh_id" class="form-select">
                    <option value="">-- Tất cả ngành --</option>
                    @foreach ($nganhs as $nganh)
                        <option value="{{ $nganh->id }}" {{ request('nganh_id') == $nganh->id ? 'selected' : '' }}>
                            {{ $nganh->ten_nganh }} ({{ $nganh->khoa->ten_khoa ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Theo ID</option>
                    <option value="ten_chuyen_nganh" {{ request('sort') == 'ten_chuyen_nganh' ? 'selected' : '' }}>Theo Tên
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="direction" class="form-select">
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success">Tìm</button>
                <a href="{{ route('dao-tao.chuyen-nganh.index') }}" class="btn btn-secondary">Làm mới</a>
            </div>
        </form>

        {{-- Bảng danh sách --}}
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">ID</th>
                    <th>Mã Chuyên ngành</th>
                    <th>Tên Chuyên ngành</th>
                    <th>Ngành</th>
                    <th>Khoa</th>
                    <th>Tổng TC tối thiểu</th>
                    <th width="160">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chuyenNganhs as $cn)
                    <tr>
                        <td>{{ $cn->id }}</td>
                        <td><strong>{{ $cn->ma_chuyen_nganh }}</strong></td>
                        <td>{{ $cn->ten_chuyen_nganh }}</td>
                        <td>{{ $cn->nganh->ten_nganh ?? '-' }}</td>
                        <td>{{ $cn->nganh->khoa->ten_khoa ?? '-' }}</td>
                        <td class="text-center">{{ $cn->tong_tin_chi_toi_thieu ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dao-tao.chuyen-nganh.edit', $cn->id) }}"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('dao-tao.chuyen-nganh.destroy', $cn->id) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xóa chuyên ngành này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center">
            {{ $chuyenNganhs->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
