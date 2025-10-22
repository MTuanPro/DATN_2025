<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Khôi phục mật khẩu - S-MIS</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background-color: #f4f4f4;
            border-radius: 5px;
            padding: 30px;
        }

        .header {
            background-color: #435ebe;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -30px -30px 20px -30px;
        }

        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }

        .password-box {
            background-color: #f8f9fa;
            border-left: 4px solid #435ebe;
            padding: 15px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #435ebe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Khôi phục mật khẩu</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $userName }}</strong>,</p>

            <p>Chúng tôi đã nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.</p>

            <p>Mật khẩu mới của bạn là:</p>

            <div class="password-box">
                {{ $newPassword }}
            </div>

            <p><strong>Lưu ý quan trọng:</strong></p>
            <ul>
                <li>Vui lòng đổi mật khẩu ngay sau khi đăng nhập lần đầu</li>
                <li>Không chia sẻ mật khẩu này với bất kỳ ai</li>
                <li>Mật khẩu phân biệt chữ hoa/chữ thường</li>
            </ul>

            <p>Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng liên hệ với quản trị viên ngay lập tức.</p>

            <p>Trân trọng,<br>

                <strong>Hệ thống S-MIS</strong>

            </p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>

            <p>&copy; {{ date('Y') }} S-MIS. All rights reserved.</p>

        </div>
    </div>
</body>

</html>
