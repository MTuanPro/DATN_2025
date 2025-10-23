<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocTienQuyetSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID của các môn học theo mã môn
        $getMonHocId = function ($maMon) {
            $mon = DB::table('mon_hoc')->where('ma_mon', $maMon)->first();
            return $mon ? $mon->id : null;
        };

        // Định nghĩa quan hệ tiên quyết logic
        $tienQuyets = [
            // Cấu trúc dữ liệu cần Nhập môn lập trình
            [
                'mon_hoc' => 'IT102',
                'mon_tien_quyet' => 'IT101',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần nắm vững lập trình cơ bản trước khi học cấu trúc dữ liệu',
            ],
            // Lập trình hướng đối tượng cần Nhập môn lập trình
            [
                'mon_hoc' => 'IT103',
                'mon_tien_quyet' => 'IT101',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Nắm vững lập trình cơ bản trước khi học OOP',
            ],
            // Cơ sở dữ liệu cần Nhập môn lập trình
            [
                'mon_hoc' => 'IT104',
                'mon_tien_quyet' => 'IT101',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần biết lập trình để viết SQL và kết nối database',
            ],
            // Phát triển ứng dụng Web cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT201',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'OOP là nền tảng cho framework Laravel/PHP',
            ],
            // Phát triển ứng dụng Web cần Cơ sở dữ liệu
            [
                'mon_hoc' => 'IT201',
                'mon_tien_quyet' => 'IT104',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Web app luôn cần kết nối database',
            ],
            // Lập trình ứng dụng di động cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT202',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Flutter/React Native sử dụng OOP',
            ],
            // Lập trình ứng dụng di động nên học Phát triển Web trước
            [
                'mon_hoc' => 'IT202',
                'mon_tien_quyet' => 'IT201',
                'loai_tien_quyet' => 'khuyen_nghi',
                'dieu_kien_qua_mon' => false,
                'ghi_chu' => 'Kiến thức Web giúp ích cho mobile app',
            ],
            // Công nghệ phần mềm cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT203',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần hiểu OOP để áp dụng design pattern',
            ],
            // Trí tuệ nhân tạo cần Cấu trúc dữ liệu
            [
                'mon_hoc' => 'IT204',
                'mon_tien_quyet' => 'IT102',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần hiểu cấu trúc dữ liệu để xây dựng mô hình AI',
            ],
            // Trí tuệ nhân tạo cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT204',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'TensorFlow, PyTorch sử dụng OOP',
            ],
            // An toàn bảo mật cần Mạng máy tính
            [
                'mon_hoc' => 'IT205',
                'mon_tien_quyet' => 'IT106',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần hiểu mạng để triển khai bảo mật',
            ],
            // An toàn bảo mật nên học Hệ điều hành
            [
                'mon_hoc' => 'IT205',
                'mon_tien_quyet' => 'IT105',
                'loai_tien_quyet' => 'khuyen_nghi',
                'dieu_kien_qua_mon' => false,
                'ghi_chu' => 'Hiểu OS giúp bảo mật hệ thống tốt hơn',
            ],
            // Xử lý ảnh số cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT301',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'OpenCV sử dụng Python/C++ OOP',
            ],
            // Khoa học dữ liệu cần Cơ sở dữ liệu
            [
                'mon_hoc' => 'IT302',
                'mon_tien_quyet' => 'IT104',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Data Science làm việc với database lớn',
            ],
            // Khoa học dữ liệu nên học Trí tuệ nhân tạo
            [
                'mon_hoc' => 'IT302',
                'mon_tien_quyet' => 'IT204',
                'loai_tien_quyet' => 'khuyen_nghi',
                'dieu_kien_qua_mon' => false,
                'ghi_chu' => 'AI và Data Science có nhiều điểm chung',
            ],
            // Lập trình game cần Lập trình hướng đối tượng
            [
                'mon_hoc' => 'IT303',
                'mon_tien_quyet' => 'IT103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Game engine sử dụng OOP',
            ],
            // IoT cần Mạng máy tính
            [
                'mon_hoc' => 'IT304',
                'mon_tien_quyet' => 'IT106',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'IoT thiết bị cần kết nối mạng',
            ],
            // IoT cần Nhập môn lập trình
            [
                'mon_hoc' => 'IT304',
                'mon_tien_quyet' => 'IT101',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Lập trình Arduino/Raspberry Pi',
            ],
            // Blockchain cần Mạng máy tính
            [
                'mon_hoc' => 'IT305',
                'mon_tien_quyet' => 'IT106',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Blockchain hoạt động trên mạng P2P',
            ],
            // Blockchain cần Cơ sở dữ liệu
            [
                'mon_hoc' => 'IT305',
                'mon_tien_quyet' => 'IT104',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Blockchain là distributed database',
            ],
            // Thực tập doanh nghiệp cần nhiều môn cơ bản
            [
                'mon_hoc' => 'IT401',
                'mon_tien_quyet' => 'IT201',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần biết làm Web app để thực tập',
            ],
            [
                'mon_hoc' => 'IT401',
                'mon_tien_quyet' => 'IT104',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Database là kỹ năng bắt buộc',
            ],
            // Đồ án tốt nghiệp cần rất nhiều môn
            [
                'mon_hoc' => 'IT402',
                'mon_tien_quyet' => 'IT401',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Phải thực tập trước khi làm ĐATN',
            ],
            [
                'mon_hoc' => 'IT402',
                'mon_tien_quyet' => 'IT203',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Cần nắm vững quy trình phát triển phần mềm',
            ],

            // Khoa Kinh tế: Kinh tế vĩ mô cần Kinh tế vi mô
            [
                'mon_hoc' => 'EC102',
                'mon_tien_quyet' => 'EC101',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Vi mô là nền tảng cho vĩ mô',
            ],
            // Marketing cần Quản trị học
            [
                'mon_hoc' => 'EC104',
                'mon_tien_quyet' => 'EC103',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Marketing là một chức năng quản trị',
            ],
            // Tiếng Anh 2 cần Tiếng Anh 1
            [
                'mon_hoc' => 'DC008',
                'mon_tien_quyet' => 'DC007',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Học tuần tự từ A1 lên A2',
            ],
            // Giáo dục thể chất 2 cần Giáo dục thể chất 1
            [
                'mon_hoc' => 'DC010',
                'mon_tien_quyet' => 'DC009',
                'loai_tien_quyet' => 'bat_buoc',
                'dieu_kien_qua_mon' => true,
                'ghi_chu' => 'Học tuần tự',
            ],
        ];

        foreach ($tienQuyets as $tq) {
            $monHocId = $getMonHocId($tq['mon_hoc']);
            $monTienQuyetId = $getMonHocId($tq['mon_tien_quyet']);

            if ($monHocId && $monTienQuyetId && $monHocId != $monTienQuyetId) {
                // Kiểm tra xem đã tồn tại chưa
                $exists = DB::table('mon_hoc_tien_quyet')
                    ->where('mon_hoc_id', $monHocId)
                    ->where('mon_tien_quyet_id', $monTienQuyetId)
                    ->exists();

                if (!$exists) {
                    DB::table('mon_hoc_tien_quyet')->insert([
                        'mon_hoc_id' => $monHocId,
                        'mon_tien_quyet_id' => $monTienQuyetId,
                        'loai_tien_quyet' => $tq['loai_tien_quyet'],
                        'dieu_kien_qua_mon' => $tq['dieu_kien_qua_mon'],
                        'ghi_chu' => $tq['ghi_chu'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
