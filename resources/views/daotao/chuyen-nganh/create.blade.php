@extends('layouts.layout-daotao')

@section('content')
    <div class="container">
        <h2>Thêm Chuyên ngành mới</h2>

        <form action="{{ route('dao-tao.chuyen-nganh.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="ma_chuyen_nganh" class="form-label">Mã Chuyên ngành <span class="text-danger">*</span></label>
                <input type="text" name="ma_chuyen_nganh" id="ma_chuyen_nganh"
                    class="form-control @error('ma_chuyen_nganh') is-invalid @enderror" value="{{ old('ma_chuyen_nganh') }}"
                    placeholder="VD: CNPM, HTTT..." required>
                @error('ma_chuyen_nganh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="ten_chuyen_nganh" class="form-label">Tên Chuyên ngành <span class="text-danger">*</span></label>
                <input type="text" name="ten_chuyen_nganh" id="ten_chuyen_nganh"
                    class="form-control @error('ten_chuyen_nganh') is-invalid @enderror"
                    value="{{ old('ten_chuyen_nganh') }}" placeholder="VD: Công nghệ phần mềm..." required>
                @error('ten_chuyen_nganh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nganh_id" class="form-label">Ngành <span class="text-danger">*</span></label>
                <select name="nganh_id" id="nganh_id" class="form-select @error('nganh_id') is-invalid @enderror" required>
                    <option value="">-- Chọn ngành --</option>
                    @foreach ($nganhs as $nganh)
                        <option value="{{ $nganh->id }}" {{ old('nganh_id') == $nganh->id ? 'selected' : '' }}>
                            {{ $nganh->ten_nganh }} ({{ $nganh->khoa->ten_khoa ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('nganh_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tong_tin_chi_toi_thieu" class="form-label">Tổng tín chỉ tối thiểu</label>
                <input type="number" name="tong_tin_chi_toi_thieu" id="tong_tin_chi_toi_thieu"
                    class="form-control @error('tong_tin_chi_toi_thieu') is-invalid @enderror"
                    value="{{ old('tong_tin_chi_toi_thieu', 120) }}" min="0" max="200">
                @error('tong_tin_chi_toi_thieu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Số tín chỉ tối thiểu để tốt nghiệp (VD: 120, 140...)</small>
            </div>

            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả</label>
                <textarea name="mo_ta" id="mo_ta" class="form-control" rows="4">{{ old('mo_ta') }}</textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-success">Lưu</button>
                <a href="{{ route('dao-tao.chuyen-nganh.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
