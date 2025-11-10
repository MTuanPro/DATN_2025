@extends('layouts.layout-daotao')

@section('title', 'Cài Đặt')

@section('content')
<div class="page-heading">
    <h3>Cài Đặt</h3>
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
                    <h4 class="card-title">Giao Diện & Hiển Thị</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Chủ Đề Giao Diện</label>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="theme" 
                                       id="theme-light" 
                                       value="light" 
                                       {{ session('theme', 'light') == 'light' ? 'checked' : '' }}>
                                <label class="form-check-label" for="theme-light">
                                    <i class="bi bi-sun"></i> Chế Độ Sáng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="theme" 
                                       id="theme-dark" 
                                       value="dark" 
                                       {{ session('theme') == 'dark' ? 'checked' : '' }}>
                                <label class="form-check-label" for="theme-dark">
                                    <i class="bi bi-moon"></i> Chế Độ Tối
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Thông Báo</h5>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="notifications_email" 
                                       name="notifications_email"
                                       {{ session('notifications_email', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifications_email">
                                    <i class="bi bi-envelope"></i> Nhận thông báo qua Email
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="notifications_browser" 
                                       name="notifications_browser"
                                       {{ session('notifications_browser', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifications_browser">
                                    <i class="bi bi-bell"></i> Nhận thông báo trên Trình duyệt
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu Cài Đặt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Auto-apply theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-dark').checked = true;
    }
});

// Theme toggle listener
document.querySelectorAll('input[name="theme"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'dark') {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });
});
</script>
@endsection
