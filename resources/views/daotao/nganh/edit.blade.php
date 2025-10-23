@extends('layouts.layout-daotao')

@section('content')
<div class="container">
    <h2>Chỉnh sửa Ngành</h2>

    {{-- Nút quay lại --}}
    <a href="{{ route('dao-tao.nganh.index') }}" class="btn btn-secondary mb-3">← Quay lại danh sách</a>

    {{-- Hiển thị lỗi --}}
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

    {{-- Form cập nhật ngành --}}
    <form action="{{ route('dao-tao.nganh.update', $nganh->id) }}" method="POST" class="card p-4 shadow-sm rounded-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="ma_nganh" class="form-label fw-bold">Mã Ngành <span class="text-danger">*</span></label>
            <input type="text" id="ma_nganh" name="ma_nganh" class="form-control"
                   value="{{ old('ma_nganh', $nganh->ma_nganh) }}" placeholder="Nhập mã ngành">
            @error('ma_nganh')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ten_nganh" class="form-label fw-bold">Tên Ngành <span class="text-danger">*</span></label>
            <input type="text" id="ten_nganh" name="ten_nganh" class="form-control"
                   value="{{ old('ten_nganh', $nganh->ten_nganh) }}" placeholder="Nhập tên ngành">
            @error('ten_nganh')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Dropdown chọn khoa --}}
        <div class="mb-3">
            <label for="khoa_id" class="form-label fw-bold">Khoa Quản Lý</label>
            <select name="khoa_id" id="khoa_id" class="form-select">
                <option value="">-- Chọn Khoa --</option>
                @foreach ($khoas as $khoa)
                    <option value="{{ $khoa->id }}" {{ old('khoa_id', $nganh->khoa_id) == $khoa->id ? 'selected' : '' }}>
                        {{ $khoa->ten_khoa }}
                    </option>
                @endforeach
            </select>
            @error('khoa_id')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mo_ta" class="form-label fw-bold">Mô Tả</label>
            <textarea id="mo_ta" name="mo_ta" rows="3" class="form-control"
                      placeholder="Nhập mô tả...">{{ old('mo_ta', $nganh->mo_ta) }}</textarea>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
            <a href="{{ route('dao-tao.nganh.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
