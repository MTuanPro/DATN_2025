@extends('layouts.layout-daotao')

@section('content')
<div class="container">
    <h2>Chỉnh sửa Khoa</h2>

    {{-- Nút quay lại --}}
    <a href="{{ route('dao-tao.khoa.index') }}" class="btn btn-secondary mb-3">← Quay lại danh sách</a>

    {{-- Hiển thị thông báo lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Lỗi!</strong> Vui lòng kiểm tra lại các trường sau:<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form chỉnh sửa khoa --}}
    <form action="{{ route('dao-tao.khoa.update', $khoa->id) }}" method="POST" class="card p-4 shadow-sm rounded-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="ma_khoa" class="form-label fw-bold">Mã Khoa <span class="text-danger">*</span></label>
            <input type="text" id="ma_khoa" name="ma_khoa" class="form-control"
                   value="{{ old('ma_khoa', $khoa->ma_khoa) }}" placeholder="Nhập mã khoa, ví dụ: CNTT">
            @error('ma_khoa')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ten_khoa" class="form-label fw-bold">Tên Khoa <span class="text-danger">*</span></label>
            <input type="text" id="ten_khoa" name="ten_khoa" class="form-control"
                   value="{{ old('ten_khoa', $khoa->ten_khoa) }}" placeholder="Nhập tên khoa, ví dụ: Công nghệ Thông tin">
            @error('ten_khoa')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="truong_khoa_id" class="form-label fw-bold">Trưởng Khoa (tùy chọn)</label>
            <input type="text" id="truong_khoa_id" name="truong_khoa_id" class="form-control"
                   value="{{ old('truong_khoa_id', $khoa->truong_khoa_id) }}" placeholder="Nhập ID trưởng khoa (nếu có)">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
            <a href="{{ route('dao-tao.khoa.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
