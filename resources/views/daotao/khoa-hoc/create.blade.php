@extends('layouts.layout-daotao')

@section('content')
    <div class="container">
        <h2>Thêm Khóa học mới</h2>

        <form action="{{ route('dao-tao.khoa-hoc.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="ten_khoa_hoc" class="form-label">Tên Khóa học <span class="text-danger">*</span></label>
                <input type="text" name="ten_khoa_hoc" id="ten_khoa_hoc"
                    class="form-control @error('ten_khoa_hoc') is-invalid @enderror" value="{{ old('ten_khoa_hoc') }}"
                    placeholder="VD: K17, K2021-2025..." required>
                @error('ten_khoa_hoc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Tên định danh khóa học (VD: K17, K18, K2021-2025)</small>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nam_bat_dau" class="form-label">Năm bắt đầu <span class="text-danger">*</span></label>
                    <input type="number" name="nam_bat_dau" id="nam_bat_dau"
                        class="form-control @error('nam_bat_dau') is-invalid @enderror"
                        value="{{ old('nam_bat_dau', date('Y')) }}" min="2000" max="2100" required>
                    @error('nam_bat_dau')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nam_ket_thuc" class="form-label">Năm kết thúc <span class="text-danger">*</span></label>
                    <input type="number" name="nam_ket_thuc" id="nam_ket_thuc"
                        class="form-control @error('nam_ket_thuc') is-invalid @enderror"
                        value="{{ old('nam_ket_thuc', date('Y') + 4) }}" min="2000" max="2100" required>
                    @error('nam_ket_thuc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="so_nam_dao_tao" class="form-label">Số năm đào tạo <span class="text-danger">*</span></label>
                    <input type="number" name="so_nam_dao_tao" id="so_nam_dao_tao"
                        class="form-control @error('so_nam_dao_tao') is-invalid @enderror"
                        value="{{ old('so_nam_dao_tao', 4) }}" min="1" max="10" required>
                    @error('so_nam_dao_tao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                <select name="trang_thai" id="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror"
                    required>
                    <option value="dang_hoc" {{ old('trang_thai') == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                    <option value="da_tot_nghiep" {{ old('trang_thai') == 'da_tot_nghiep' ? 'selected' : '' }}>Đã tốt
                        nghiệp</option>
                </select>
                @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả</label>
                <textarea name="mo_ta" id="mo_ta" class="form-control" rows="4">{{ old('mo_ta') }}</textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-success">Lưu</button>
                <a href="{{ route('dao-tao.khoa-hoc.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
