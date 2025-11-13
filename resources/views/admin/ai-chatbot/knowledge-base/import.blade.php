@extends('layouts.layout-admin')

@section('title', 'Import Knowledge Base')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Import Knowledge Base</h3>
                    <p class="text-subtitle text-muted">Import dữ liệu từ file Excel/CSV</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}">Knowledge Base</a></li>
                            <li class="breadcrumb-item active">Import</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('import_errors'))
            <div class="alert alert-warning">
                <strong>Có lỗi khi import:</strong>
                <ul class="mb-0">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Upload File</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.ai-chatbot.knowledge-base.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="file" class="form-label">Chọn file Excel/CSV</label>
                                    <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" 
                                           accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chấp nhận: .xlsx, .xls, .csv (Tối đa 2MB)</small>
                                </div>

                                <div class="alert alert-info">
                                    <strong><i class="bi bi-info-circle"></i> Lưu ý:</strong>
                                    <ul class="mb-0">
                                        <li>File phải có header ở dòng đầu tiên</li>
                                        <li>Dữ liệu bắt đầu từ dòng thứ 2</li>
                                        <li>Các cột bắt buộc: Chủ đề, Câu hỏi mẫu, Câu trả lời</li>
                                    </ul>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Import
                                </button>
                                <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Hướng dẫn</h5>
                        </div>
                        <div class="card-body">
                            <h6>Cấu trúc file Excel/CSV:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Chủ đề</th>
                                            <th>Danh mục</th>
                                            <th>Câu hỏi mẫu</th>
                                            <th>Câu trả lời</th>
                                            <th>Từ khóa</th>
                                            <th>Độ ưu tiên</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Đăng ký môn học</td>
                                            <td>FAQ</td>
                                            <td>Làm thế nào để đăng ký môn?</td>
                                            <td>Vào menu...</td>
                                            <td>đăng ký, dkmh</td>
                                            <td>100</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <strong>Cột bắt buộc:</strong>
                                <ul class="mb-0">
                                    <li><strong>Chủ đề</strong> (không được để trống)</li>
                                    <li><strong>Câu hỏi mẫu</strong> (không được để trống)</li>
                                    <li><strong>Câu trả lời</strong> (không được để trống)</li>
                                </ul>
                            </div>

                            <div class="mt-3">
                                <p><strong>Danh sách chủ đề:</strong></p>
                                <ul>
                                    <li>Đăng ký môn học</li>
                                    <li>Lịch học & Lịch thi</li>
                                    <li>Học phí</li>
                                    <li>Điểm & Kết quả học tập</li>
                                    <li>Quy chế đào tạo</li>
                                    <li>Thủ tục hành chính</li>
                                    <li>Chương trình đào tạo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
