@extends('layouts.layout-daotao')

@section('title', 'Import Sinh viên từ Excel')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import Sinh viên từ Excel</h3>
                    <p class="text-subtitle text-muted">Nhập hàng loạt sinh viên từ file Excel</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.sinh-vien.index') }}">Sinh viên</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Import Excel</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <!-- Hướng dẫn -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn Import</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Tải file template Excel bên dưới</li>
                        <li>Điền thông tin sinh viên vào file (bắt đầu từ dòng 2)</li>
                        <li>Đảm bảo các trường có dấu <span class="text-danger">*</span> không được để trống</li>
                        <li>
                            <strong>Lưu ý về mã:</strong>
                            <ul>
                                <li><strong>Mã lớp:</strong> Phải tồn tại trong hệ thống (VD: CNTT-K15)</li>
                                <li><strong>Mã khóa học:</strong> VD: K15, K16...</li>
                                <li><strong>Mã ngành:</strong> VD: CNTT, KTPM...</li>
                                <li><strong>Mã trạng thái:</strong> DANG_HOC, BAO_LUU, THOI_HOC...</li>
                            </ul>
                        </li>
                        <li>Upload file và nhấn Import</li>
                        <li><strong>Mật khẩu mặc định:</strong> Là Mã sinh viên</li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Chú ý:</strong> Hệ thống sẽ tự động tạo tài khoản đăng nhập cho sinh viên với:
                        <ul class="mb-0 mt-2">
                            <li>Email: Theo file Excel</li>
                            <li>Mật khẩu: Mã sinh viên</li>
                            <li>Vai trò: Sinh viên</li>
                        </ul>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('dao-tao.sinh-vien.download-template') }}" class="btn btn-success btn-lg">
                            <i class="bi bi-download"></i> Tải Template Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Import -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload File Excel</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.sinh-vien.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label">Chọn file Excel <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                name="file" accept=".xlsx,.xls" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Chấp nhận file .xlsx, .xls (tối đa 5MB)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-clock-history"></i>
                            <strong>Thời gian xử lý:</strong> Có thể mất vài phút tùy thuộc vào số lượng sinh viên.
                            Vui lòng không tắt trình duyệt trong khi import.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.sinh-vien.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Sinh viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Các trường trong template -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Danh sách trường trong file Excel</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Tên cột</th>
                                    <th>Bắt buộc</th>
                                    <th>Định dạng</th>
                                    <th>Ví dụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mã SV</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi, không trùng</td>
                                    <td>2021600001</td>
                                </tr>
                                <tr>
                                    <td>Họ tên</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Chuỗi</td>
                                    <td>Nguyễn Văn A</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Email, không trùng</td>
                                    <td>nva@example.com</td>
                                </tr>
                                <tr>
                                    <td>Ngày sinh</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>YYYY-MM-DD</td>
                                    <td>2003-01-15</td>
                                </tr>
                                <tr>
                                    <td>Giới tính</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>nam/nu/khac</td>
                                    <td>nam</td>
                                </tr>
                                <tr>
                                    <td>SĐT</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Số điện thoại</td>
                                    <td>0901234567</td>
                                </tr>
                                <tr>
                                    <td>CCCD</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>12 số, không trùng</td>
                                    <td>001203012345</td>
                                </tr>
                                <tr>
                                    <td>Mã lớp</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã lớp có sẵn</td>
                                    <td>CNTT-K15</td>
                                </tr>
                                <tr>
                                    <td>Mã khóa học</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã khóa có sẵn</td>
                                    <td>K15</td>
                                </tr>
                                <tr>
                                    <td>Mã ngành</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã ngành có sẵn</td>
                                    <td>CNTT</td>
                                </tr>
                                <tr>
                                    <td>Kỳ hiện tại</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Số từ 1-8</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td>Mã trạng thái</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã trạng thái có sẵn</td>
                                    <td>DANG_HOC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
