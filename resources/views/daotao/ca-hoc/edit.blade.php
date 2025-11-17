@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa Ca học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa Ca học</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin ca học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.ca-hoc.index') }}">Ca học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra!</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square"></i> Cập nhật thông tin Ca học
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.ca-hoc.update', $caHoc->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten_ca" class="form-label">
                                        Tên ca học <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('ten_ca') is-invalid @enderror" 
                                           id="ten_ca" 
                                           name="ten_ca" 
                                           value="{{ old('ten_ca', $caHoc->ten_ca) }}"
                                           placeholder="VD: Ca 1, Ca sáng..."
                                           required>
                                    @error('ten_ca')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Nhập tên gọi của ca học</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="thu_tu" class="form-label">
                                        Thứ tự <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('thu_tu') is-invalid @enderror" 
                                           id="thu_tu" 
                                           name="thu_tu" 
                                           value="{{ old('thu_tu', $caHoc->thu_tu) }}"
                                           min="1"
                                           max="20"
                                           placeholder="VD: 1, 2, 3..."
                                           required>
                                    @error('thu_tu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Thứ tự ca học trong ngày (từ 1-20)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gio_bat_dau" class="form-label">
                                        Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" 
                                           class="form-control @error('gio_bat_dau') is-invalid @enderror" 
                                           id="gio_bat_dau" 
                                           name="gio_bat_dau" 
                                           value="{{ old('gio_bat_dau', date('H:i', strtotime($caHoc->gio_bat_dau))) }}"
                                           required>
                                    @error('gio_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Thời gian bắt đầu ca học</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gio_ket_thuc" class="form-label">
                                        Giờ kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" 
                                           class="form-control @error('gio_ket_thuc') is-invalid @enderror" 
                                           id="gio_ket_thuc" 
                                           name="gio_ket_thuc" 
                                           value="{{ old('gio_ket_thuc', date('H:i', strtotime($caHoc->gio_ket_thuc))) }}"
                                           required>
                                    @error('gio_ket_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Thời gian kết thúc ca học</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="trang_thai" 
                                               name="trang_thai"
                                               {{ old('trang_thai', $caHoc->trang_thai) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="trang_thai">
                                            <strong>Trạng thái hoạt động</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Bật để ca học có thể được sử dụng trong hệ thống
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                                    <textarea class="form-control @error('ghi_chu') is-invalid @enderror" 
                                              id="ghi_chu" 
                                              name="ghi_chu" 
                                              rows="3"
                                              placeholder="Nhập ghi chú về ca học (không bắt buộc)">{{ old('ghi_chu', $caHoc->ghi_chu) }}</textarea>
                                    @error('ghi_chu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">VD: Ca học buổi sáng - Tiết 1,2</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Lưu ý quan trọng:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Các ca học không được trùng khoảng thời gian với nhau.</li>
                                <li>Việc thay đổi thời gian ca học có thể ảnh hưởng đến các lịch học đã được xếp.</li>
                                <li>Vui lòng kiểm tra lại các lịch học sau khi cập nhật.</li>
                            </ul>
                        </div>

                        @php
                            $existingCaHoc = \App\Models\CaHoc::where('id', '!=', $caHoc->id)->orderBy('gio_bat_dau')->get();
                        @endphp
                        @if($existingCaHoc->count() > 0)
                        <div class="alert alert-light border">
                            <strong><i class="bi bi-clock-history"></i> Các ca học khác:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($existingCaHoc as $ca)
                                <li>
                                    <strong>{{ $ca->ten_ca }}</strong>: 
                                    {{ date('H:i', strtotime($ca->gio_bat_dau)) }} - {{ date('H:i', strtotime($ca->gio_ket_thuc)) }}
                                    @if(!$ca->trang_thai)
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle"></i> Đảm bảo thời gian ca học này không trùng với các ca học trên.
                            </small>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i> 
                                    Cập nhật lần cuối: {{ $caHoc->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dao-tao.ca-hoc.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Cập nhật
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    // Tính toán và hiển thị khoảng thời gian khi thay đổi giờ
    const gioBatDau = document.getElementById('gio_bat_dau');
    const gioKetThuc = document.getElementById('gio_ket_thuc');

    function calculateDuration() {
        if (gioBatDau.value && gioKetThuc.value) {
            const start = new Date('2000-01-01 ' + gioBatDau.value);
            const end = new Date('2000-01-01 ' + gioKetThuc.value);
            const diff = (end - start) / 60000; // minutes
            
            if (diff > 0) {
                console.log('Khoảng thời gian: ' + diff + ' phút');
            }
        }
    }

    gioBatDau.addEventListener('change', calculateDuration);
    gioKetThuc.addEventListener('change', calculateDuration);
</script>
@endpush

