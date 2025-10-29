<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Mật khẩu - S-MIS</title>
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

        .reset-button {
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

        .reset-button:hover {
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
            font-size: 11px;
        }

        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .warning-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .warning-box li {
            margin: 5px 0;
            color: #856404;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="icon">🔐</div>
            <h1>Reset Mật khẩu</h1>
        </div>

        <div class="email-body">
            <div class="greeting">
                Xin chào <strong>{{ $userName }}</strong>,
            </div>

            <div class="message">
                <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn
                    trong hệ thống <strong>S-MIS</strong>.</p>
            </div>

            <div class="button-container">
                <a href="{{ $resetLink }}" class="reset-button">
                    🔑 TẠO MẬT KHẨU MỚI
                </a>
            </div>

            <div class="divider"></div>

            <div class="alternative-link">
                <p><strong>Hoặc copy đường link sau vào trình duyệt:</strong></p>
                <a href="{{ $resetLink }}">{{ $resetLink }}</a>
            </div>

            <div class="warning-box">
                <p><strong>⚠️ Lưu ý quan trọng:</strong></p>
                <ul>
                    <li>Link này sẽ <strong>hết hiệu lực sau 60 phút</strong></li>
                    <li>Nếu bạn không yêu cầu reset mật khẩu, vui lòng bỏ qua email này</li>
                    <li>Không chia sẻ link này với bất kỳ ai</li>
                    <li>Mật khẩu mới phải đáp ứng yêu cầu bảo mật của hệ thống</li>
                </ul>
            </div>

            <p style="margin-top: 30px; color: #666;">
                Trân trọng,<br>
                <strong>Đội ngũ S-MIS</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>📧 Email tự động, vui lòng không trả lời</p>
            <p>&copy; {{ date('Y') }} S-MIS - Student Management Information System</p>
        </div>
    </div>
</body>

</html>
