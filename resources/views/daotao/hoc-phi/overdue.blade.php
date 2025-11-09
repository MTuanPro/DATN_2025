@extends('layouts.layout-daotao')

@section('title', 'Danh sách Nợ quá hạn')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Nợ quá hạn</h3>
                    <p class="text-subtitle text-muted">Sinh viên nợ học phí quá hạn - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nợ quá hạn</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Học kỳ</th>
                                    <th>Hạn đóng</th>
                                    <th>Số tiền nợ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hocPhis as $index => $hp)
                                    <tr>
                                        <td>{{ $hocPhis->firstItem() + $index }}</td>
                                        <td><strong>{{ $hp->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $hp->sinhVien->ho_ten }}</td>
                                        <td>{{ $hp->hocKy->ten_hoc_ky }}</td>
                                        <td><span class="text-danger">{{ $hp->han_dong->format('d/m/Y') }}</span></td>
                                        <td><strong class="text-danger">{{ number_format($hp->so_tien_con_lai, 0, ',', '.') }} đ</strong></td>
                                        <td>
                                            <a href="{{ route('dao-tao.hoc-phi.show', $hp->id) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-check-circle" style="font-size: 3rem; color: green;"></i>
                                            <p class="text-muted mt-2">Không có sinh viên nợ quá hạn</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($hocPhis->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $hocPhis->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
