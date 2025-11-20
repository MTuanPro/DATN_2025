<?php
/**
 * Script kiểm tra môn tiên quyết trong database
 */

$host = 'localhost';
$dbname = 's_mis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== KIỂM TRA MÔN TIÊN QUYẾT ===\n\n";
    
    // 1. Kiểm tra bảng mon_hoc_tien_quyet có dữ liệu không
    $sql = "SELECT COUNT(*) as total FROM mon_hoc_tien_quyet";
    $result = $pdo->query($sql)->fetch();
    echo "1. Tổng số bản ghi môn tiên quyết: " . $result['total'] . "\n\n";
    
    if ($result['total'] == 0) {
        echo "⚠️ KHÔNG CÓ DỮ LIỆU MÔN TIÊN QUYẾT!\n";
        echo "Cần chạy seeder hoặc thêm dữ liệu thủ công\n\n";
        
        // Gợi ý tạo dữ liệu mẫu
        echo "=== TẠO DỮ LIỆU MẪU ===\n";
        echo "Ví dụ: Tiếng Anh 2 yêu cầu hoàn thành Tiếng Anh 1\n\n";
        
        // Lấy ID của Tiếng Anh 1 và 2
        $ta1 = $pdo->query("SELECT id, ma_mon, ten_mon FROM mon_hoc WHERE ma_mon LIKE '%TA01%' OR ten_mon LIKE '%Tiếng Anh 1%' LIMIT 1")->fetch();
        $ta2 = $pdo->query("SELECT id, ma_mon, ten_mon FROM mon_hoc WHERE ma_mon LIKE '%TA02%' OR ten_mon LIKE '%Tiếng Anh 2%' LIMIT 1")->fetch();
        
        if ($ta1 && $ta2) {
            echo "Tìm thấy:\n";
            echo "- Tiếng Anh 1: ID={$ta1['id']}, Mã={$ta1['ma_mon']}, Tên={$ta1['ten_mon']}\n";
            echo "- Tiếng Anh 2: ID={$ta2['id']}, Mã={$ta2['ma_mon']}, Tên={$ta2['ten_mon']}\n\n";
            
            echo "Thêm quan hệ tiên quyết:\n";
            $insertSql = "INSERT INTO mon_hoc_tien_quyet (mon_hoc_id, mon_tien_quyet_id, loai_tien_quyet, dieu_kien_qua_mon, created_at, updated_at) 
                         VALUES ({$ta2['id']}, {$ta1['id']}, 'bat_buoc', 1, NOW(), NOW())";
            
            $pdo->exec($insertSql);
            echo "✓ Đã thêm: Tiếng Anh 2 yêu cầu hoàn thành Tiếng Anh 1\n\n";
            
            // Thêm thêm vài ví dụ
            $gdtc2 = $pdo->query("SELECT id FROM mon_hoc WHERE ma_mon LIKE '%GDTC02%' LIMIT 1")->fetch();
            $gdtc1 = $pdo->query("SELECT id FROM mon_hoc WHERE ma_mon LIKE '%GDTC01%' LIMIT 1")->fetch();
            
            if ($gdtc1 && $gdtc2) {
                $pdo->exec("INSERT INTO mon_hoc_tien_quyet (mon_hoc_id, mon_tien_quyet_id, loai_tien_quyet, dieu_kien_qua_mon, created_at, updated_at) 
                           VALUES ({$gdtc2['id']}, {$gdtc1['id']}, 'bat_buoc', 1, NOW(), NOW())");
                echo "✓ Đã thêm: Giáo dục thể chất 2 yêu cầu hoàn thành Giáo dục thể chất 1\n";
            }
            
            $ta3 = $pdo->query("SELECT id FROM mon_hoc WHERE ma_mon LIKE '%TA03%' LIMIT 1")->fetch();
            if ($ta3 && $ta2) {
                $pdo->exec("INSERT INTO mon_hoc_tien_quyet (mon_hoc_id, mon_tien_quyet_id, loai_tien_quyet, dieu_kien_qua_mon, created_at, updated_at) 
                           VALUES ({$ta3['id']}, {$ta2['id']}, 'bat_buoc', 1, NOW(), NOW())");
                echo "✓ Đã thêm: Tiếng Anh 3 yêu cầu hoàn thành Tiếng Anh 2\n";
            }
            
            echo "\n✅ Đã tạo dữ liệu mẫu thành công!\n\n";
        }
    }
    
    // 2. Hiển thị các môn có tiên quyết
    echo "=== DANH SÁCH MÔN CÓ TIÊN QUYẾT ===\n";
    $sql = "SELECT 
                m1.ma_mon as ma_mon_hoc,
                m1.ten_mon as ten_mon_hoc,
                m2.ma_mon as ma_tien_quyet,
                m2.ten_mon as ten_tien_quyet,
                mtq.loai_tien_quyet,
                mtq.dieu_kien_qua_mon
            FROM mon_hoc_tien_quyet mtq
            JOIN mon_hoc m1 ON mtq.mon_hoc_id = m1.id
            JOIN mon_hoc m2 ON mtq.mon_tien_quyet_id = m2.id
            ORDER BY m1.ma_mon";
    
    $result = $pdo->query($sql);
    $count = 0;
    while ($row = $result->fetch()) {
        $count++;
        echo "{$count}. {$row['ma_mon_hoc']} ({$row['ten_mon_hoc']})\n";
        echo "   → Yêu cầu: {$row['ma_tien_quyet']} ({$row['ten_tien_quyet']})\n";
        echo "   → Loại: {$row['loai_tien_quyet']}, Qua môn: " . ($row['dieu_kien_qua_mon'] ? 'Có' : 'Không') . "\n\n";
    }
    
    if ($count == 0) {
        echo "Không có môn nào có tiên quyết!\n";
    }
    
    echo "\n✅ Hoàn tất kiểm tra!\n";
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
