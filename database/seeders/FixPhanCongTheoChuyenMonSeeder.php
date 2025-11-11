<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Models\DaoTao;
use Carbon\Carbon;

class FixPhanCongTheoChuyenMonSeeder extends Seeder
{
    /**
     * Phân công giảng viên theo đúng chuyên môn
     */
    public function run(): void
    {
        echo "🔄 Đang sửa phân công giảng viên theo chuyên môn...\n";

        // Xóa phân công cũ
        DB::table('lop_hoc_phan_giang_vien')->truncate();
        echo "✅ Đã xóa phân công cũ\n";

        $daoTaos = DaoTao::all();
        $nguoiPhanCongId = $daoTaos->first()?->id;

        // Mapping chuyên môn giảng viên với môn học
        $chuyenMonMapping = [
            // CNTT
            'GV001' => [ // Lập trình Web, Cơ sở dữ liệu
                'keywords' => ['lập trình', 'web', 'cơ sở dữ liệu', 'database', 'php', 'java', 'javascript', 'html', 'css', 'phần mềm', 'software', 'oop', 'hướng đối tượng', 'thực tập', 'đồ án'],
            ],
            'GV002' => [ // Trí tuệ nhân tạo, Machine Learning
                'keywords' => ['trí tuệ', 'ai', 'machine learning', 'học máy', 'deep learning', 'python', 'data science', 'xử lý ảnh', 'image', 'thị giác', 'computer vision', 'thực tập', 'khóa luận'],
            ],
            'GV003' => [ // Mạng máy tính, An toàn thông tin
                'keywords' => ['mạng', 'network', 'an toàn', 'bảo mật', 'security', 'hệ thống', 'hạ tầng', 'hệ điều hành', 'operating system', 'cloud', 'điện toán đám mây', 'thực tập', 'đồ án'],
            ],
            
            // Kinh tế
            'GV004' => [ // Quản trị kinh doanh, Marketing
                'keywords' => ['quản trị', 'kinh doanh', 'marketing', 'quản lý', 'business', 'chiến lược', 'kinh tế', 'thương mại', 'commerce', 'kế toán', 'tài chính', 'thực tập', 'khóa luận'],
            ],
            'GV006' => [ // Phân tích dữ liệu, Business Intelligence
                'keywords' => ['phân tích', 'thống kê', 'data', 'business intelligence', 'bi', 'excel', 'tài chính', 'dữ liệu', 'cấu trúc dữ liệu', 'giải thuật', 'algorithm', 'thực tập', 'đồ án'],
            ],
            
            // Ngoại ngữ
            'GV005' => [ // Tiếng Anh giao tiếp, TOEIC
                'keywords' => ['tiếng anh', 'english', 'toeic', 'ielts', 'giao tiếp', 'nghe', 'nói', 'thực tập'],
            ],
            
            // Môn chung - Chính trị - Tư tưởng (CHỈ DẠY ĐÚNG MÔN NÀY)
            'GV007' => [ // Triết học Mác - Lênin, Tư tưởng Hồ Chí Minh
                'keywords' => ['triết học', 'tư tưởng', 'đường lối', 'chủ nghĩa', 'lịch sử đảng', 'mác', 'lênin', 'hồ chí minh', 'chính trị', 'kinh tế chính trị'],
            ],
            
            // Giáo dục thể chất (CHỈ DẠY ĐÚNG MÔN NÀY)
            'GV008' => [ // Giáo dục thể chất, Thể thao
                'keywords' => ['giáo dục thể chất', 'thể dục', 'thể thao', 'gdtc'],
            ],
            
            // Giáo dục quốc phòng (CHỈ DẠY ĐÚNG MÔN NÀY)
            'GV009' => [ // Giáo dục quốc phòng - An ninh
                'keywords' => ['giáo dục quốc phòng', 'quốc phòng', 'an ninh', 'gdqp'],
            ],
        ];

        // KHÔNG CÒN KHÁI NIỆM MÔN CHUNG - Tất cả đều có giảng viên chuyên trách

        $lopHocPhans = LopHocPhan::with('monHoc')->get();
        $giangViens = GiangVien::all()->keyBy('ma_giang_vien');

        $phanCongs = [];
        $soLopKhongTimThayGV = 0;

        foreach ($lopHocPhans as $lop) {
            $tenMon = strtolower($lop->monHoc->ten_mon ?? '');
            $maMon = strtolower($lop->monHoc->ma_mon ?? '');
            
            // Tìm giảng viên phù hợp theo chuyên môn
            $maxScore = 0;
            $giangVienPhuHop = null;
            
            foreach ($chuyenMonMapping as $maGV => $config) {
                $score = 0;
                
                foreach ($config['keywords'] as $keyword) {
                    if (str_contains($tenMon, $keyword) || str_contains($maMon, $keyword)) {
                        $score++;
                    }
                }
                
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $giangVienPhuHop = $giangViens->get($maGV);
                }
            }

            // Nếu không tìm thấy giảng viên phù hợp, chọn ngẫu nhiên
            if (!$giangVienPhuHop) {
                $giangVienPhuHop = $giangViens->random();
                $soLopKhongTimThayGV++;
                echo "⚠️  Lớp {$lop->ma_lop_hp} - Môn: {$tenMon} - Không tìm được GV phù hợp, chọn ngẫu nhiên\n";
            }

            // Phân công giảng viên chính
            $phanCongs[] = [
                'lop_hoc_phan_id' => $lop->id,
                'giang_vien_id' => $giangVienPhuHop->id,
                'vai_tro' => 'giang_vien_chinh',
                'phan_cong_giang_day' => 'Phụ trách giảng dạy toàn bộ môn học',
                'ngay_phan_cong' => Carbon::now()->subDays(rand(1, 30)),
                'nguoi_phan_cong_id' => $nguoiPhanCongId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 20% lớp có trợ giảng (chọn từ cùng chuyên môn nếu có)
            if (rand(1, 100) <= 20) {
                $troGiang = $giangViens
                    ->where('id', '!=', $giangVienPhuHop->id)
                    ->random();

                $phanCongs[] = [
                    'lop_hoc_phan_id' => $lop->id,
                    'giang_vien_id' => $troGiang->id,
                    'vai_tro' => 'tro_giang',
                    'phan_cong_giang_day' => 'Hỗ trợ thực hành và chấm bài tập',
                    'ngay_phan_cong' => Carbon::now()->subDays(rand(1, 30)),
                    'nguoi_phan_cong_id' => $nguoiPhanCongId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($phanCongs)) {
            // Insert theo batch để tăng tốc độ
            foreach (array_chunk($phanCongs, 100) as $chunk) {
                DB::table('lop_hoc_phan_giang_vien')->insert($chunk);
            }
            
            echo "\n✅ Đã phân công {" . count($phanCongs) . "} lần\n";
            echo "📊 Tổng lớp: " . $lopHocPhans->count() . "\n";
            echo "📊 Số lớp không tìm được GV phù hợp: {$soLopKhongTimThayGV}\n";
            
            // Thống kê số lớp mỗi giảng viên
            $thongKe = DB::table('lop_hoc_phan_giang_vien')
                ->select('giang_vien_id', DB::raw('count(*) as so_lop'))
                ->groupBy('giang_vien_id')
                ->get();
            
            echo "\n📊 Thống kê số lớp mỗi giảng viên:\n";
            foreach ($thongKe as $tk) {
                $gv = $giangViens->firstWhere('id', $tk->giang_vien_id);
                echo "   - {$gv->ma_giang_vien} ({$gv->ho_ten}): {$tk->so_lop} lớp\n";
            }
        } else {
            echo "ℹ️  Không có phân công mới để tạo\n";
        }
    }
}
