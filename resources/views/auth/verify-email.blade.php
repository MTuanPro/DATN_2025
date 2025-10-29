<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - S-MIS</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/auth.css') }}">
</head>

<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="{{ url('/') }}">
                            <h2 class="text-primary">S-MIS</h2>
                        </a>
                    </div>
                    <h1 class="auth-title">Xác thực Email</h1>
                    <p class="auth-subtitle mb-4">
                        Vui lòng xác thực địa chỉ email của bạn để tiếp tục sử dụng hệ thống.
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Link xác thực mới đã được gửi đến email của bạn!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-envelope-check" style="font-size: 4rem; color: #435ebe;"></i>
                            </div>
                            <p class="text-center text-muted mb-4">
                                <strong>Email của bạn:</strong> {{ Auth::user()->email }}
                            </p>
                            <p class="text-center small text-muted mb-4">
                                Chúng tôi đã gửi một email xác thực đến địa chỉ email trên.
                                Vui lòng kiểm tra hộp thư (kể cả thư mục Spam) và click vào link trong email để xác
                                thực.
                            </p>

                            <form method="POST" action="{{ route('verification.send') }}" class="text-center">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="bi bi-arrow-clockwise"></i> Gửi lại Email xác thực
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-muted">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
</body>

</html>
