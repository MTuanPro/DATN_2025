@extends('layouts.layout-daotao')

@section('title', 'Hồ Sơ Người Dùng')

@section('content')
<div class="page-heading">
    <h3>Hồ Sơ Người Dùng</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông Tin Cá Nhân</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="avatar-preview mb-3">
                                    @if($user->anh_dai_dien)
                                        <img src="{{ asset('storage/' . $user->anh_dai_dien) }}" 
                                             alt="Avatar" 
                                             class="rounded-circle" 
                                             width="150" 
                                             height="150"
                                             id="avatar-preview">
                                    @else
                                        <img src="{{ asset('assets/images/faces/1.jpg') }}" 
                                             alt="Avatar" 
                                             class="rounded-circle" 
                                             width="150" 
                                             height="150"
                                             id="avatar-preview">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="anh_dai_dien" class="form-label">Ảnh Đại Diện</label>
                                    <input type="file" 
                                           class="form-control @error('anh_dai_dien') is-invalid @enderror" 
                                           id="anh_dai_dien" 
                                           name="anh_dai_dien"
                                           accept="image/*"
                                           onchange="previewAvatar(event)">
                                    @error('anh_dai_dien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="so_dien_thoai" class="form-label">Số Điện Thoại</label>
                                    <input type="text" 
                                           class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                           id="so_dien_thoai" 
                                           name="so_dien_thoai" 
                                           value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}">
                                    @error('so_dien_thoai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Đổi Mật Khẩu</h5>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật Khẩu Hiện Tại</label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chỉ điền nếu muốn đổi mật khẩu</small>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật Khẩu Mới</label>
                                    <input type="password" 
                                           class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" 
                                           name="new_password">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Xác Nhận Mật Khẩu Mới</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation">
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Lưu Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function previewAvatar(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('avatar-preview');
        preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
