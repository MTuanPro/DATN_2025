@extends('layouts.layout-daotao')

@section('title', 'Import giảng viên từ Excel')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import giảng viên từ Excel</h3>
                    <p class="text-subtitle text-muted">Tải lên file Excel để thêm nhiều giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.giang-vien.index') }}">Giảng viên</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Import</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hướng dẫn import</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Lưu ý quan trọng:</h5>
                                <ol class="mb-0">
                                    <li>File import phải ở định dạng <strong>.xlsx</strong> hoặc <strong>.csv</strong></li>
                                    <li>Tải file mẫu bên dưới để xem cấu trúc dữ liệu</li>
                                    <li>Không thay đổi tên các cột trong file mẫu</li>
                                    <li>Mã giảng viên và Email phải là duy nhất (không trùng lặp)</li>
                                    <li>Các trường có dấu (*) là bắt buộc phải nhập</li>
                                    <li>Định dạng ngày sinh: <code>YYYY-MM-DD</code> (VD: 1990-05-15)</li>
                                    <li>Giới tính: <code>Nam</code>, <code>Nữ</code> hoặc <code>Khác</code></li>
                                </ol>
                            </div>

                            <div class="mb-3">
                                <a href="{{ route('dao-tao.giang-vien.download-template') }}" class="btn btn-success">
                                    <i class="bi bi-download"></i> Tải file mẫu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tải lên file Excel</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dao-tao.giang-vien.import') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="file" class="form-label">Chọn file Excel <span
                                            class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chấp nhận file: .xlsx, .xls, .csv (Tối đa 5MB)</small>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload"></i> Bắt đầu Import
                                    </button>
                                    <a href="{{ route('dao-tao.giang-vien.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Cấu trúc file Excel mẫu</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ma_giang_vien *</th>
                                            <th>ho_ten *</th>
                                            <th>email *</th>
                                            <th>khoa_id *</th>
                                            <th>trinh_do_id</th>
                                            <th>so_dien_thoai</th>
                                            <th>ngay_sinh</th>
                                            <th>gioi_tinh</th>
                                            <th>dia_chi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>GV001</td>
                                            <td>Nguyễn Văn A</td>
                                            <td>gv001@example.com</td>
                                            <td>1</td>
                                            <td>2</td>
                                            <td>0912345678</td>
                                            <td>1985-05-15</td>
                                            <td>Nam</td>
                                            <td>Hà Nội</td>
                                        </tr>
                                        <tr>
                                            <td>GV002</td>
                                            <td>Trần Thị B</td>
                                            <td>gv002@example.com</td>
                                            <td>2</td>
                                            <td>3</td>
                                            <td>0987654321</td>
                                            <td>1990-10-20</td>
                                            <td>Nữ</td>
                                            <td>TP. HCM</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">
                                <strong>Ghi chú:</strong>
                                khoa_id và trinh_do_id là ID tương ứng trong hệ thống.
                                Vui lòng kiểm tra danh sách Khoa và Trình độ trước khi import.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
