@extends('layouts.layout-sinhvien')

@section('title', 'Đăng ký môn học - Tạo đăng ký')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Tạo đăng ký môn học mới</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('sinhvien.dang-ky-mon-hoc.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Môn học</label>
                        <select name="mon_hoc_id" class="form-select" required>
                            <option value="">-- Chọn môn học --</option>
                                @foreach ($monHocs as $mh)
                                    <option value="{{ $mh->id }}">{{ $mh->ma_mon }} - {{ $mh->ten_mon }} ({{ $mh->so_tin_chi }} TC)</option>
                                @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Học kỳ</label>
                        <select name="hoc_ky_id" class="form-select" required>
                            <option value="">-- Chọn học kỳ --</option>
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}">{{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <button class="btn btn-primary">Đăng ký</button>
                        <a href="{{ route('sinhvien.dang-ky-mon-hoc.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
