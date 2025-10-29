@extends('layouts.layout-daotao')

@section('content')
    <div class="container">
        <h2>Danh sách Khóa học</h2>

        {{-- Nút thêm mới --}}
        <a href="{{ route('dao-tao.khoa-hoc.create') }}" class="btn btn-primary mb-3">+ Thêm Khóa học</a>

        {{-- Form tìm kiếm và lọc --}}
        <form action="{{ route('dao-tao.khoa-hoc.index') }}" method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                    placeholder="Tìm theo tên khóa học...">
            </div>
            <div class="col-md-2">
                <select name="trang_thai" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="dang_hoc" {{ request('trang_thai') == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                    <option value="da_tot_nghiep" {{ request('trang_thai') == 'da_tot_nghiep' ? 'selected' : '' }}>Đã tốt
                        nghiệp</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="nam_bat_dau" {{ request('sort') == 'nam_bat_dau' ? 'selected' : '' }}>Theo Năm bắt đầu
                    </option>
                    <option value="ten_khoa_hoc" {{ request('sort') == 'ten_khoa_hoc' ? 'selected' : '' }}>Theo Tên</option>
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
                <a href="{{ route('dao-tao.khoa-hoc.index') }}" class="btn btn-secondary">Làm mới</a>
            </div>
        </form>

        {{-- Bảng danh sách --}}
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">ID</th>
                    <th>Tên Khóa học</th>
                    <th>Năm bắt đầu</th>
                    <th>Năm kết thúc</th>
                    <th>Số năm đào tạo</th>
                    <th>Trạng thái</th>
                    <th width="160">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($khoaHocs as $kh)
                    <tr>
                        <td>{{ $kh->id }}</td>
                        <td><strong>{{ $kh->ten_khoa_hoc }}</strong></td>
                        <td class="text-center">{{ $kh->nam_bat_dau }}</td>
                        <td class="text-center">{{ $kh->nam_ket_thuc }}</td>
                        <td class="text-center">{{ $kh->so_nam_dao_tao }} năm</td>
                        <td>
                            @if ($kh->trang_thai == 'dang_hoc')
                                <span class="badge bg-success">Đang học</span>
                            @else
                                <span class="badge bg-secondary">Đã tốt nghiệp</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dao-tao.khoa-hoc.edit', $kh->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('dao-tao.khoa-hoc.destroy', $kh->id) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xóa khóa học này?')">Xóa</button>
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
            {{ $khoaHocs->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
