<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - S-MIS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .message {
            color: #666;
            margin-bottom: 30px;
        }

        .button-container {
            text-align: center;
            margin: 40px 0;
        }

        .verify-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .verify-button:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
        }

        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }

        .alternative-link {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .alternative-link p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }

        .alternative-link a {
            color: #667eea;
            word-break: break-all;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .warning p {
            margin: 5px 0;
            font-size: 14px;
            color: #856404;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }

        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }

            .verify-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">📧</div>
            <h1>Xác thực Email</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">Xin chào <strong>{{ $user->name }}</strong>,</p>

            <p class="message">
                Chào mừng bạn đến với hệ thống quản lý sinh viên <strong>S-MIS</strong>!<br>
                Tài khoản của bạn đã được tạo thành công với email: <strong>{{ $user->email }}</strong>
            </p>

            <p class="message">
                Để hoàn tất quá trình đăng ký và bắt đầu sử dụng hệ thống, vui lòng xác thực địa chỉ email của bạn bằng
                cách nhấn vào nút bên dưới:
            </p>

            <!-- Button -->
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✓ Xác thực Email ngay
                </a>
            </div>

            <div class="divider"></div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong>Nếu nút bên trên không hoạt động, vui lòng copy link sau vào trình duyệt:</strong></p>
                <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
            </div>

            <!-- Warning -->
            <div class="warning">
                <p><strong>⚠️ Lưu ý bảo mật:</strong></p>
                <p>• Link xác thực này sẽ hết hạn sau <strong>60 phút</strong></p>
                <p>• Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này</p>
                <p>• Không chia sẻ link này cho bất kỳ ai</p>
            </div>

            <p class="message">
                Sau khi xác thực thành công, bạn có thể đăng nhập vào hệ thống S-MIS và trải nghiệm đầy đủ các tính
                năng.
            </p>

            <p class="message">
                Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với bộ phận hỗ trợ qua email: <a
                    href="mailto:support@smis.edu.vn">support@smis.edu.vn</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>
                Email này được gửi tự động từ hệ thống S-MIS<br>
                © {{ date('Y') }} S-MIS - Student Management Information System<br>
                <a href="{{ url('/') }}">{{ url('/') }}</a>
            </p>
        </div>
    </div>
</body>

</html>
