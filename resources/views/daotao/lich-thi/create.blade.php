@extends('layouts.layout-daotao')

@section('title', 'Thêm Lịch thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Thêm Lịch thi</h3>
                <p class="text-subtitle text-muted">Tạo lịch thi mới cho lớp học phần</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.lich-thi.index') }}">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Có lỗi xảy ra:</h4>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin lịch thi</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.lich-thi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lop_hoc_phan_id">Lớp học phần <span class="text-danger">*</span></label>
                                <select name="lop_hoc_phan_id" id="lop_hoc_phan_id" class="form-select @error('lop_hoc_phan_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn lớp học phần --</option>
                                    @foreach($lopHocPhans as $lhp)
                                        <option value="{{ $lhp->id }}" {{ old('lop_hoc_phan_id') == $lhp->id ? 'selected' : '' }}>
                                            {{ $lhp->ma_lop_hp }} - {{ $lhp->monHoc->ten_mon }} ({{ $lhp->hocKy->ten_hoc_ky }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('lop_hoc_phan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="loai_thi">Loại thi <span class="text-danger">*</span></label>
                                <select name="loai_thi" id="loai_thi" class="form-select @error('loai_thi') is-invalid @enderror" required>
                                    <option value="">-- Chọn loại thi --</option>
                                    <option value="giua_ky" {{ old('loai_thi') == 'giua_ky' ? 'selected' : '' }}>Giữa kỳ</option>
                                    <option value="cuoi_ky" {{ old('loai_thi') == 'cuoi_ky' ? 'selected' : '' }}>Cuối kỳ</option>
                                    <option value="thi_lai" {{ old('loai_thi') == 'thi_lai' ? 'selected' : '' }}>Thi lại</option>
                                </select>
                                @error('loai_thi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ngay_thi">Ngày thi <span class="text-danger">*</span></label>
                                <input type="date" name="ngay_thi" id="ngay_thi" class="form-control @error('ngay_thi') is-invalid @enderror" 
                                       value="{{ old('ngay_thi') }}" required>
                                @error('ngay_thi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="gio_bat_dau">Giờ bắt đầu <span class="text-danger">*</span></label>
                                <input type="time" name="gio_bat_dau" id="gio_bat_dau" class="form-control @error('gio_bat_dau') is-invalid @enderror" 
                                       value="{{ old('gio_bat_dau') }}" required>
                                @error('gio_bat_dau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="gio_ket_thuc">Giờ kết thúc <span class="text-danger">*</span></label>
                                <input type="time" name="gio_ket_thuc" id="gio_ket_thuc" class="form-control @error('gio_ket_thuc') is-invalid @enderror" 
                                       value="{{ old('gio_ket_thuc') }}" required>
                                @error('gio_ket_thuc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="phong_thi_id">Phòng thi</label>
                                <select name="phong_thi_id" id="phong_thi_id" class="form-select @error('phong_thi_id') is-invalid @enderror">
                                    <option value="">-- Chọn phòng thi --</option>
                                    @foreach($phongHocs as $phong)
                                        <option value="{{ $phong->id }}" {{ old('phong_thi_id') == $phong->id ? 'selected' : '' }}>
                                            {{ $phong->ten_phong }} ({{ $phong->suc_chua }} chỗ)
                                        </option>
                                    @endforeach
                                </select>
                                @error('phong_thi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="so_sinh_vien_du_thi">SL dự thi</label>
                                <input type="number" name="so_sinh_vien_du_thi" id="so_sinh_vien_du_thi" 
                                       class="form-control @error('so_sinh_vien_du_thi') is-invalid @enderror" 
                                       value="{{ old('so_sinh_vien_du_thi') }}" min="0">
                                @error('so_sinh_vien_du_thi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="giam_thi_1_id">Giám thị 1</label>
                                <select name="giam_thi_1_id" id="giam_thi_1_id" class="form-select @error('giam_thi_1_id') is-invalid @enderror">
                                    <option value="">-- Chọn giám thị 1 --</option>
                                    @foreach($giangViens as $gv)
                                        <option value="{{ $gv->id }}" {{ old('giam_thi_1_id') == $gv->id ? 'selected' : '' }}>
                                            {{ $gv->ho_ten }} - {{ $gv->ma_giang_vien }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('giam_thi_1_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="giam_thi_2_id">Giám thị 2</label>
                                <select name="giam_thi_2_id" id="giam_thi_2_id" class="form-select @error('giam_thi_2_id') is-invalid @enderror">
                                    <option value="">-- Chọn giám thị 2 --</option>
                                    @foreach($giangViens as $gv)
                                        <option value="{{ $gv->id }}" {{ old('giam_thi_2_id') == $gv->id ? 'selected' : '' }}>
                                            {{ $gv->ho_ten }} - {{ $gv->ma_giang_vien }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('giam_thi_2_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hinh_thuc_thi">Hình thức thi <span class="text-danger">*</span></label>
                                <select name="hinh_thuc_thi" id="hinh_thuc_thi" class="form-select @error('hinh_thuc_thi') is-invalid @enderror" required>
                                    <option value="">-- Chọn hình thức --</option>
                                    <option value="offline" {{ old('hinh_thuc_thi') == 'offline' ? 'selected' : '' }}>Thi tại trường</option>
                                    <option value="online" {{ old('hinh_thuc_thi') == 'online' ? 'selected' : '' }}>Thi trực tuyến</option>
                                    <option value="hybrid" {{ old('hinh_thuc_thi') == 'hybrid' ? 'selected' : '' }}>Kết hợp</option>
                                </select>
                                @error('hinh_thuc_thi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="link_thi_online">Link thi online</label>
                                <input type="url" name="link_thi_online" id="link_thi_online" 
                                       class="form-control @error('link_thi_online') is-invalid @enderror" 
                                       value="{{ old('link_thi_online') }}" placeholder="https://...">
                                @error('link_thi_online')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="de_thi_file">Đề thi (PDF, DOC, DOCX - Max 10MB)</label>
                                <input type="file" name="de_thi_file" id="de_thi_file" class="form-control @error('de_thi_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                @error('de_thi_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dap_an_file">Đáp án (PDF, DOC, DOCX - Max 10MB)</label>
                                <input type="file" name="dap_an_file" id="dap_an_file" class="form-control @error('dap_an_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                @error('dap_an_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ghi_chu">Ghi chú</label>
                        <textarea name="ghi_chu" id="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror" 
                                  placeholder="Ghi chú về lịch thi...">{{ old('ghi_chu') }}</textarea>
                        @error('ghi_chu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu lịch thi
                        </button>
                        <a href="{{ route('dao-tao.lich-thi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
