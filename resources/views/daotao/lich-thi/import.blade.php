@extends('layouts.layout-daotao')

@section('title', 'Import Lịch thi từ Excel')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Import Lịch thi từ Excel</h3>
                    <p class="text-subtitle text-muted">Nhập hàng loạt lịch thi từ file Excel</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lich-thi.index') }}">Lịch thi</a></li>
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

        @if (session('errors') && is_array(session('errors')))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <strong>Các lỗi chi tiết:</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                        <li>Điền thông tin lịch thi vào file (bắt đầu từ dòng 2)</li>
                        <li>Đảm bảo các trường có dấu <span class="text-danger">*</span> không được để trống</li>
                        <li>
                            <strong>Lưu ý về thông tin:</strong>
                            <ul>
                                <li><strong>Mã lớp học phần:</strong> Phải tồn tại trong hệ thống (VD: CNTT101.01)</li>
                                <li><strong>Loại thi:</strong> giua_ky, cuoi_ky, hoặc thi_lai</li>
                                <li><strong>Ngày thi:</strong> Hỗ trợ nhiều định dạng: YYYY-MM-DD (2024-12-20), DD/MM/YYYY (20/12/2024), DD-MM-YYYY (20-12-2024)</li>
                                <li><strong>Tên ca học:</strong> Tên ca học có sẵn trong hệ thống (VD: Ca 1, Ca 2)</li>
                                <li><strong>Tên phòng:</strong> Tên hoặc mã phòng thi (có thể để trống)</li>
                                <li><strong>Số sinh viên dự thi:</strong> Số nguyên (có thể để trống, hệ thống sẽ tự động tính)</li>
                                <li><strong>Giám thị 1, 2:</strong> Tên hoặc mã giảng viên (có thể để trống)</li>
                                <li><strong>Hình thức:</strong> offline, online, hoặc hybrid (mặc định: offline)</li>
                                <li><strong>Link online:</strong> Bắt buộc nếu hình thức là online hoặc hybrid</li>
                            </ul>
                        </li>
                        <li>Upload file và nhấn Import</li>
                    </ol>

                    <div class="text-center mt-3">
                        <a href="{{ route('dao-tao.lich-thi.download-template') }}" class="btn btn-success btn-lg">
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
                    <form action="{{ route('dao-tao.lich-thi.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label">Chọn file Excel <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                name="file" accept=".xlsx,.xls,.csv,.txt" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Chấp nhận file .xlsx, .xls, .csv, .txt (tối đa 5MB)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-clock-history"></i>
                            <strong>Thời gian xử lý:</strong> Có thể mất vài phút tùy thuộc vào số lượng lịch thi.
                            Vui lòng không tắt trình duyệt trong khi import.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.lich-thi.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Lịch thi
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
                                    <td>ma_lop_hp</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Mã lớp học phần có sẵn</td>
                                    <td>CNTT101.01</td>
                                </tr>
                                <tr>
                                    <td>loai_thi</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>giua_ky/cuoi_ky/thi_lai</td>
                                    <td>cuoi_ky</td>
                                </tr>
                                <tr>
                                    <td>ngay_thi</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>YYYY-MM-DD, DD/MM/YYYY, DD-MM-YYYY</td>
                                    <td>2024-12-20 hoặc 20/12/2024</td>
                                </tr>
                                <tr>
                                    <td>ten_ca_hoc</td>
                                    <td><span class="badge bg-danger">Bắt buộc</span></td>
                                    <td>Tên ca học có sẵn</td>
                                    <td>Ca 1</td>
                                </tr>
                                <tr>
                                    <td>ten_phong</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Tên hoặc mã phòng</td>
                                    <td>P101 hoặc Phòng 101</td>
                                </tr>
                                <tr>
                                    <td>so_sinh_vien_du_thi</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Số nguyên (tự động tính nếu để trống)</td>
                                    <td>50</td>
                                </tr>
                                <tr>
                                    <td>ten_giam_thi_1</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Tên hoặc mã giảng viên</td>
                                    <td>Nguyễn Văn A</td>
                                </tr>
                                <tr>
                                    <td>ten_giam_thi_2</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Tên hoặc mã giảng viên</td>
                                    <td>Trần Thị B</td>
                                </tr>
                                <tr>
                                    <td>hinh_thuc</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>offline/online/hybrid, mặc định: offline</td>
                                    <td>offline</td>
                                </tr>
                                <tr>
                                    <td>link_online</td>
                                    <td><span class="badge bg-warning">Tùy chọn*</span></td>
                                    <td>URL (bắt buộc nếu hình thức online/hybrid)</td>
                                    <td>https://meet.google.com/xxx</td>
                                </tr>
                                <tr>
                                    <td>ghi_chu</td>
                                    <td><span class="badge bg-warning">Tùy chọn</span></td>
                                    <td>Chuỗi</td>
                                    <td>Lịch thi cuối kỳ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

