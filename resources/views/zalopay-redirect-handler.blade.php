<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đang xử lý thanh toán...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đang xử lý thanh toán...</h2>
        <div class="spinner"></div>
        <p>Vui lòng đợi trong giây lát...</p>
    </div>
    
    <script>
        // Tự động redirect về callback handler với tất cả tham số
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const returncode = urlParams.get('returncode');
            const appTransId = urlParams.get('apptransid');
            const zptransid = urlParams.get('zptransid');
            
            // Nếu có tham số ZaloPay, redirect về callback handler
            if (returncode || appTransId || zptransid) {
                const callbackUrl = '/payment/zalopay/callback?' + window.location.search.substring(1);
                console.log('Redirecting to callback handler:', callbackUrl);
                window.location.href = callbackUrl;
            } else {
                // Nếu không có tham số, redirect về trang học phí
                window.location.href = '/sinh-vien/hoc-phi';
            }
        })();
    </script>
</body>
</html>

