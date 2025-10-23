@extends('layouts.layout-daotao')

@section('content')
<div class="container">
    <h2>Danh sách Khoa</h2>

    {{-- Nút thêm mới --}}
    <a href="{{ route('dao-tao.khoa.create') }}" class="btn btn-primary mb-3">+ Thêm Khoa</a>

    {{-- Form tìm kiếm và lọc --}}
    <form action="{{ route('dao-tao.khoa.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" 
                   value="{{ request('keyword') }}" placeholder="Tìm theo mã hoặc tên khoa...">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="">-- Sắp xếp --</option>
                <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Theo ID</option>
                <option value="ten_khoa" {{ request('sort') == 'ten_khoa' ? 'selected' : '' }}>Theo Tên khoa</option>
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
            <a href="{{ route('dao-tao.khoa.index') }}" class="btn btn-secondary">Làm mới</a>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Mã Khoa</th>
                <th>Tên Khoa</th>
                <th>Trưởng Khoa</th>
                <th width="160">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($khoas as $khoa)
            <tr>
                <td>{{ $khoa->id }}</td>
                <td>{{ $khoa->ma_khoa }}</td>
                <td>{{ $khoa->ten_khoa }}</td>
                <td>{{ $khoa->truong_khoa_id ?? '-' }}</td>
                <td>
                    <a href="{{ route('dao-tao.khoa.edit', $khoa->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('dao-tao.khoa.destroy', $khoa->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa khoa này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $khoas->appends(request()->query())->links() }}
    </div>
</div>
@endsection
