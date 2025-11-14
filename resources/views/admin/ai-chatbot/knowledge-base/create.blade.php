@extends('layouts.layout-admin')

@section('title', 'Thêm Knowledge Base')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Knowledge Base Mới</h3>
                    <p class="text-subtitle text-muted">Thêm câu hỏi và câu trả lời mới cho chatbot</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}">Knowledge Base</a></li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Knowledge Base</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ai-chatbot.knowledge-base.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chu_de" class="form-label required">Chủ đề <span class="text-danger">*</span></label>
                                    <select name="chu_de" id="chu_de" class="form-select @error('chu_de') is-invalid @enderror" required>
                                        <option value="">-- Chọn chủ đề --</option>
                                        <option value="Đăng ký môn học" {{ old('chu_de') == 'Đăng ký môn học' ? 'selected' : '' }}>Đăng ký môn học</option>
                                        <option value="Lịch học & Lịch thi" {{ old('chu_de') == 'Lịch học & Lịch thi' ? 'selected' : '' }}>Lịch học & Lịch thi</option>
                                        <option value="Học phí" {{ old('chu_de') == 'Học phí' ? 'selected' : '' }}>Học phí</option>
                                        <option value="Điểm & Kết quả học tập" {{ old('chu_de') == 'Điểm & Kết quả học tập' ? 'selected' : '' }}>Điểm & Kết quả học tập</option>
                                        <option value="Quy chế đào tạo" {{ old('chu_de') == 'Quy chế đào tạo' ? 'selected' : '' }}>Quy chế đào tạo</option>
                                        <option value="Thủ tục hành chính" {{ old('chu_de') == 'Thủ tục hành chính' ? 'selected' : '' }}>Thủ tục hành chính</option>
                                        <option value="Chương trình đào tạo" {{ old('chu_de') == 'Chương trình đào tạo' ? 'selected' : '' }}>Chương trình đào tạo</option>
                                        <option value="Khác" {{ old('chu_de') == 'Khác' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('chu_de')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="danh_muc" class="form-label">Danh mục</label>
                                    <select name="danh_muc" id="danh_muc" class="form-select @error('danh_muc') is-invalid @enderror">
                                        <option value="">-- Chọn danh mục --</option>
                                        <option value="FAQ" {{ old('danh_muc') == 'FAQ' ? 'selected' : '' }}>FAQ</option>
                                        <option value="Hướng dẫn" {{ old('danh_muc') == 'Hướng dẫn' ? 'selected' : '' }}>Hướng dẫn</option>
                                        <option value="Quy định" {{ old('danh_muc') == 'Quy định' ? 'selected' : '' }}>Quy định</option>
                                    </select>
                                    @error('danh_muc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cau_hoi_mau" class="form-label required">Câu hỏi mẫu <span class="text-danger">*</span></label>
                            <textarea name="cau_hoi_mau" id="cau_hoi_mau" rows="3" 
                                      class="form-control @error('cau_hoi_mau') is-invalid @enderror" 
                                      placeholder="Ví dụ: Làm thế nào để đăng ký môn học?" required>{{ old('cau_hoi_mau') }}</textarea>
                            @error('cau_hoi_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Câu hỏi mà sinh viên thường hỏi</small>
                        </div>

                        <div class="mb-3">
                            <label for="cau_tra_loi" class="form-label required">Câu trả lời <span class="text-danger">*</span></label>
                            <textarea name="cau_tra_loi" id="cau_tra_loi" rows="6" 
                                      class="form-control @error('cau_tra_loi') is-invalid @enderror" 
                                      placeholder="Nhập câu trả lời chi tiết..." required>{{ old('cau_tra_loi') }}</textarea>
                            @error('cau_tra_loi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hỗ trợ markdown và emoji. Sử dụng \n để xuống dòng.</small>
                        </div>

                        <div class="mb-3">
                            <label for="tu_khoa" class="form-label">Từ khóa</label>
                            <input type="text" name="tu_khoa" id="tu_khoa" 
                                   class="form-control @error('tu_khoa') is-invalid @enderror" 
                                   value="{{ old('tu_khoa') }}" 
                                   placeholder="đăng ký môn, đăng ký học phần, dkmh">
                            @error('tu_khoa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Các từ khóa liên quan, cách nhau bởi dấu phẩy</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="do_uu_tien" class="form-label">Độ ưu tiên</label>
                                    <input type="number" name="do_uu_tien" id="do_uu_tien" 
                                           class="form-control @error('do_uu_tien') is-invalid @enderror" 
                                           value="{{ old('do_uu_tien', 50) }}" min="0" max="100">
                                    @error('do_uu_tien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Từ 0-100, giá trị cao sẽ được ưu tiên hiển thị trước</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="kich_hoat" 
                                               id="kich_hoat" {{ old('kich_hoat', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kich_hoat">
                                            Kích hoạt
                                        </label>
                                    </div>
                                    <small class="text-muted">Chỉ những KB kích hoạt mới được bot sử dụng</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu
                            </button>
                            <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
