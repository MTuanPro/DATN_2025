<?php
/**
 * Script thêm cột response_data vào bảng lich_su_dong_hoc_phi
 * Chạy: php add_column.php
 */

$host = 'localhost';
$dbname = 's_mis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Đang kết nối database...\n";
    
    // Kiểm tra cột đã tồn tại chưa
    $checkSql = "SHOW COLUMNS FROM `lich_su_dong_hoc_phi` LIKE 'response_data'";
    $result = $pdo->query($checkSql);
    
    if ($result->rowCount() > 0) {
        echo "✓ Cột 'response_data' đã tồn tại!\n";
    } else {
        echo "Đang thêm cột 'response_data'...\n";
        
        $alterSql = "ALTER TABLE `lich_su_dong_hoc_phi` 
                     ADD COLUMN `response_data` JSON NULL 
                     COMMENT 'Dữ liệu phản hồi từ cổng thanh toán' 
                     AFTER `ghi_chu`";
        
        $pdo->exec($alterSql);
        echo "✓ Đã thêm cột 'response_data' thành công!\n";
    }
    
    echo "\n✅ Hoàn tất!\n";
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
