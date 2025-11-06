@extends('layouts.layout-giangvien')

@section('title', 'Nhập điểm')

@section('content')
<div class="page-heading">
    <h3>Nhập điểm: {{ $lopHocPhan->ten_lop_hp }}</h3>
</div>

<section class="section">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>{{ $lopHocPhan->monHoc->ten_mon }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Mã lớp:</strong> {{ $lopHocPhan->ma_lop_hp }}</p>
            <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }}</p>
            <p><strong>Số sinh viên:</strong> {{ $sinhViens->count() }}</p>
            
            @if ($daKhoaDiem)
                <div class="alert alert-danger">Lớp này đã khóa điểm</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Danh sách sinh viên</h5>
        </div>
        <div class="card-body">
            @if ($cauHinhs->isEmpty())
                <div class="alert alert-warning">Chưa có cấu hình đầu điểm</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>MSSV</th>
                                <th>Họ tên</th>
                                <th>Điểm TK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sinhViens as $index => $lhpsv)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lhpsv->sinhVien->ma_sinh_vien }}</td>
                                <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                <td>
                                    @if ($lhpsv->ketQuaHocTap)
                                        {{ number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <a href="{{ route('giangvien.nhap-diem.index') }}" class="btn btn-secondary mt-3">
                Quay lại
            </a>
        </div>
    </div>
</section>
@endsection
