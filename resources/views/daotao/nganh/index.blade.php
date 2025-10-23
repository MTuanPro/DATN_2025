@extends('layouts.layout-daotao')

@section('content')
<div class="container">
    <h2>Danh sách Ngành</h2>

    {{-- Nút thêm mới --}}
    <a href="{{ route('dao-tao.nganh.create') }}" class="btn btn-primary mb-3">+ Thêm Ngành</a>

    {{-- Hiển thị thông báo thành công hoặc lỗi --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form tìm kiếm và lọc --}}
    <form action="{{ route('dao-tao.nganh.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" 
                   value="{{ request('keyword') }}" placeholder="Tìm theo mã hoặc tên ngành...">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="">-- Sắp xếp --</option>
                <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Theo ID</option>
                <option value="ten_nganh" {{ request('sort') == 'ten_nganh' ? 'selected' : '' }}>Theo Tên ngành</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="direction" class="form-select">
                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success">Tìm kiếm</button>
            <a href="{{ route('dao-tao.nganh.index') }}" class="btn btn-secondary">Làm mới</a>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Mã Ngành</th>
                <th>Tên Ngành</th>
                <th>Khoa</th>
                <th>Mô tả</th>
                <th width="160">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nganhs as $nganh)
            <tr>
                <td>{{ $nganh->id }}</td>
                <td>{{ $nganh->ma_nganh }}</td>
                <td>{{ $nganh->ten_nganh }}</td>
                <td>{{ $nganh->khoa->ten_khoa ?? '-' }}</td>
                <td>{{ $nganh->mo_ta ?? '-' }}</td>
                <td>
                    <a href="{{ route('dao-tao.nganh.edit', $nganh->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('dao-tao.nganh.destroy', $nganh->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa ngành này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $nganhs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
