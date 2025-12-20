@extends('layouts.layout-admin')

@section('title', 'Sửa Knowledge Base')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Knowledge Base</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin knowledge base</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}">Knowledge Base</a></li>
                            <li class="breadcrumb-item active">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Knowledge Base #{{ $knowledgeBase->id }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ai-chatbot.knowledge-base.update', $knowledgeBase) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chu_de" class="form-label required">Chủ đề <span class="text-danger">*</span></label>
                                    <select name="chu_de" id="chu_de" class="form-select @error('chu_de') is-invalid @enderror" required>
                                        <option value="">-- Chọn chủ đề --</option>
                                        <option value="Đăng ký môn học" {{ old('chu_de', $knowledgeBase->chu_de) == 'Đăng ký môn học' ? 'selected' : '' }}>Đăng ký môn học</option>
                                        <option value="Lịch học & Lịch thi" {{ old('chu_de', $knowledgeBase->chu_de) == 'Lịch học & Lịch thi' ? 'selected' : '' }}>Lịch học & Lịch thi</option>
                                        <option value="Học phí" {{ old('chu_de', $knowledgeBase->chu_de) == 'Học phí' ? 'selected' : '' }}>Học phí</option>
                                        <option value="Điểm & Kết quả học tập" {{ old('chu_de', $knowledgeBase->chu_de) == 'Điểm & Kết quả học tập' ? 'selected' : '' }}>Điểm & Kết quả học tập</option>
                                        <option value="Quy chế đào tạo" {{ old('chu_de', $knowledgeBase->chu_de) == 'Quy chế đào tạo' ? 'selected' : '' }}>Quy chế đào tạo</option>
                                        <option value="Thủ tục hành chính" {{ old('chu_de', $knowledgeBase->chu_de) == 'Thủ tục hành chính' ? 'selected' : '' }}>Thủ tục hành chính</option>
                                        <option value="Chương trình đào tạo" {{ old('chu_de', $knowledgeBase->chu_de) == 'Chương trình đào tạo' ? 'selected' : '' }}>Chương trình đào tạo</option>
                                        <option value="Khác" {{ old('chu_de', $knowledgeBase->chu_de) == 'Khác' ? 'selected' : '' }}>Khác</option>
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
                                        <option value="FAQ" {{ old('danh_muc', $knowledgeBase->danh_muc) == 'FAQ' ? 'selected' : '' }}>FAQ</option>
                                        <option value="Hướng dẫn" {{ old('danh_muc', $knowledgeBase->danh_muc) == 'Hướng dẫn' ? 'selected' : '' }}>Hướng dẫn</option>
                                        <option value="Quy định" {{ old('danh_muc', $knowledgeBase->danh_muc) == 'Quy định' ? 'selected' : '' }}>Quy định</option>
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
                                      class="form-control @error('cau_hoi_mau') is-invalid @enderror" required>{{ old('cau_hoi_mau', $knowledgeBase->cau_hoi_mau) }}</textarea>
                            @error('cau_hoi_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cau_tra_loi" class="form-label required">Câu trả lời <span class="text-danger">*</span></label>
                            <textarea name="cau_tra_loi" id="cau_tra_loi" rows="6" 
                                      class="form-control @error('cau_tra_loi') is-invalid @enderror" required>{{ old('cau_tra_loi', $knowledgeBase->cau_tra_loi) }}</textarea>
                            @error('cau_tra_loi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tu_khoa" class="form-label">Từ khóa</label>
                            <input type="text" name="tu_khoa" id="tu_khoa" 
                                   class="form-control @error('tu_khoa') is-invalid @enderror" 
                                   value="{{ old('tu_khoa', $knowledgeBase->tu_khoa) }}">
                            @error('tu_khoa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="do_uu_tien" class="form-label">Độ ưu tiên</label>
                                    <input type="number" name="do_uu_tien" id="do_uu_tien" 
                                           class="form-control @error('do_uu_tien') is-invalid @enderror" 
                                           value="{{ old('do_uu_tien', $knowledgeBase->do_uu_tien) }}" min="0" max="100">
                                    @error('do_uu_tien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="kich_hoat" 
                                               id="kich_hoat" {{ old('kich_hoat', $knowledgeBase->kich_hoat) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kich_hoat">
                                            Kích hoạt
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Thông tin:</strong><br>
                            - Lượt truy cập: {{ $knowledgeBase->luot_truy_cap }}<br>
                            - Hữu ích: {{ $knowledgeBase->huu_ich }} ({{ $knowledgeBase->tyLeHuuIch() }}%)<br>
                            - Cập nhật lần cuối: {{ $knowledgeBase->ngay_cap_nhat?->format('d/m/Y H:i') }}<br>
                            - Người tạo: {{ $knowledgeBase->nguoiTao?->name ?? 'N/A' }}
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnUpdate">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const btnUpdate = document.getElementById('btnUpdate');
    
    form.addEventListener('submit', function(e) {
        // Disable button để tránh submit nhiều lần
        btnUpdate.disabled = true;
        btnUpdate.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...';
        
        // Kiểm tra validation
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            btnUpdate.disabled = false;
            btnUpdate.innerHTML = '<i class="bi bi-save"></i> Cập nhật';
            form.classList.add('was-validated');
            
            // Hiển thị thông báo lỗi
            alert('Vui lòng điền đầy đủ các trường bắt buộc!');
            return false;
        }
    });
    
    // Debug: Log form data khi submit
    form.addEventListener('submit', function(e) {
        const formData = new FormData(form);
        console.log('Form data:', Object.fromEntries(formData));
    });
});
</script>
@endpush
