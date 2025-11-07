<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Models\DanhMuc\PhongHoc;
use App\Models\GiangVien;
use Carbon\Carbon;

class LichThiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy học kỳ hiện tại hoặc học kỳ mới nhất
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first() 
                ?? HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->first();

        if (!$hocKy) {
            $this->command->error('Không tìm thấy học kỳ nào! Vui lòng chạy HocKySeeder trước.');
            return;
        }

        // Lấy các lớp học phần của học kỳ này
        $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKy->id)
            ->with('monHoc')
            ->get();

        if ($lopHocPhans->isEmpty()) {
            $this->command->error('Không tìm thấy lớp học phần nào! Vui lòng chạy LopHocPhanSeeder trước.');
            return;
        }

        // Lấy phòng học
        $phongHocs = PhongHoc::all();
        
        if ($phongHocs->isEmpty()) {
            $this->command->error('Không tìm thấy phòng học nào! Vui lòng chạy PhongHocSeeder trước.');
            return;
        }

        // Lấy giảng viên
        $giangViens = GiangVien::with('user')->get();

        if ($giangViens->count() < 2) {
            $this->command->error('Cần ít nhất 2 giảng viên để phân công giám thị! Vui lòng chạy GiangVienSeeder trước.');
            return;
        }

        $this->command->info('Bắt đầu tạo dữ liệu lịch thi mẫu...');

        $lichThiData = [];
        $ngayThiStart = Carbon::now()->addDays(10); // Bắt đầu từ 10 ngày sau

        foreach ($lopHocPhans->take(20) as $index => $lopHocPhan) {
            // Tính ngày thi (mỗi lớp cách nhau 1-2 ngày)
            $ngayThi = $ngayThiStart->copy()->addDays(($index % 10)); // 10 ngày thi

            // Lấy giờ thi ngẫu nhiên
            $caThiOptions = [
                ['07:00', '09:00'], // Ca sáng sớm
                ['09:30', '11:30'], // Ca sáng
                ['13:00', '15:00'], // Ca chiều
                ['15:30', '17:30'], // Ca chiều muộn
            ];
            $caThi = $caThiOptions[array_rand($caThiOptions)];

            // Chọn phòng thi ngẫu nhiên
            $phongHoc = $phongHocs->random();

            // Chọn 2 giám thị ngẫu nhiên
            $giamThi = $giangViens->random(2);

            // Loại thi: ưu tiên cuối kỳ, sau đó giữa kỳ, ít thi lại
            $loaiThiWeight = [
                'cuoi_ky' => 60,  // 60%
                'giua_ky' => 35,  // 35%
                'thi_lai' => 5,   // 5%
            ];
            $rand = rand(1, 100);
            if ($rand <= 60) {
                $loaiThi = 'cuoi_ky';
            } elseif ($rand <= 95) {
                $loaiThi = 'giua_ky';
            } else {
                $loaiThi = 'thi_lai';
            }

            // Hình thức thi
            $hinhThucThiWeight = [
                'offline' => 70,  // 70%
                'online' => 15,   // 15%
                'hybrid' => 15,   // 15%
            ];
            $rand = rand(1, 100);
            if ($rand <= 70) {
                $hinhThucThi = 'offline';
            } elseif ($rand <= 85) {
                $hinhThucThi = 'online';
            } else {
                $hinhThucThi = 'hybrid';
            }

            // Số sinh viên dự thi (random từ 20-50)
            $soSinhVienDuThi = rand(20, 50);

            $lichThiData[] = [
                'lop_hoc_phan_id' => $lopHocPhan->id,
                'loai_thi' => $loaiThi,
                'ngay_thi' => $ngayThi->format('Y-m-d'),
                'gio_bat_dau' => $caThi[0],
                'gio_ket_thuc' => $caThi[1],
                'phong_thi_id' => $phongHoc->id,
                'so_sinh_vien_du_thi' => $soSinhVienDuThi,
                'giam_thi_1_id' => $giamThi[0]->id,
                'giam_thi_2_id' => $giamThi[1]->id,
                'hinh_thuc' => $hinhThucThi,
                'link_online' => $hinhThucThi !== 'offline' ? 'https://meet.google.com/abc-' . strtolower(\Illuminate\Support\Str::random(4)) : null,
                'de_thi_file' => null, // Sẽ upload sau
                'dap_an_file' => null, // Sẽ upload sau
                'ghi_chu' => $this->getGhiChu($loaiThi, $hinhThucThi),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert dữ liệu
        LichThi::insert($lichThiData);

        $this->command->info('✅ Đã tạo ' . count($lichThiData) . ' lịch thi mẫu cho học kỳ: ' . $hocKy->ten_hoc_ky);
        $this->command->info('📅 Lịch thi từ ngày: ' . $ngayThiStart->format('d/m/Y'));
        
        // Thống kê
        $stats = [
            'Tổng lịch thi' => count($lichThiData),
            'Giữa kỳ' => collect($lichThiData)->where('loai_thi', 'giua_ky')->count(),
            'Cuối kỳ' => collect($lichThiData)->where('loai_thi', 'cuoi_ky')->count(),
            'Thi lại' => collect($lichThiData)->where('loai_thi', 'thi_lai')->count(),
            'Offline' => collect($lichThiData)->where('hinh_thuc', 'offline')->count(),
            'Online' => collect($lichThiData)->where('hinh_thuc', 'online')->count(),
            'Hybrid' => collect($lichThiData)->where('hinh_thuc', 'hybrid')->count(),
        ];

        $this->command->table(['Loại', 'Số lượng'], 
            collect($stats)->map(fn($value, $key) => [$key, $value])->toArray()
        );
    }

    /**
     * Tạo ghi chú ngẫu nhiên
     */
    private function getGhiChu($loaiThi, $hinhThucThi): ?string
    {
        $ghiChuOptions = [
            'Sinh viên mang theo máy tính cá nhân',
            'Không được sử dụng tài liệu',
            'Được phép sử dụng tài liệu',
            'Thi trắc nghiệm trên giấy',
            'Thi tự luận',
            'Thời gian làm bài: 90 phút',
            'Thời gian làm bài: 120 phút',
            null, // Không có ghi chú
            null,
            null,
        ];

        if ($hinhThucThi === 'online') {
            return 'Sinh viên vào link thi đúng giờ. Chuẩn bị máy tính và kết nối internet ổn định.';
        }

        if ($loaiThi === 'thi_lai') {
            return 'Lịch thi lại. Sinh viên mang theo thẻ sinh viên và CMND/CCCD.';
        }

        return $ghiChuOptions[array_rand($ghiChuOptions)];
    }

    /**
     * Tạo lịch thi cho tất cả lớp học phần (nếu cần)
     */
    public function seedAll(): void
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->take(2)->get();

        foreach ($hocKys as $hocKy) {
            $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKy->id)->get();
            
            // Tạo lịch thi giữa kỳ
            foreach ($lopHocPhans as $lopHocPhan) {
                $this->createLichThi($lopHocPhan, 'giua_ky');
            }

            // Tạo lịch thi cuối kỳ
            foreach ($lopHocPhans as $lopHocPhan) {
                $this->createLichThi($lopHocPhan, 'cuoi_ky');
            }
        }

        $this->command->info('✅ Đã tạo lịch thi cho tất cả lớp học phần!');
    }

    /**
     * Tạo một lịch thi
     */
    private function createLichThi(LopHocPhan $lopHocPhan, string $loaiThi): void
    {
        $phongHoc = PhongHoc::inRandomOrder()->first();
        $giangViens = GiangVien::inRandomOrder()->take(2)->get();

        if (!$phongHoc || $giangViens->count() < 2) {
            return;
        }

        $ngayThi = $loaiThi === 'giua_ky' 
            ? Carbon::now()->addWeeks(6)  // Thi giữa kỳ sau 6 tuần
            : Carbon::now()->addWeeks(12); // Thi cuối kỳ sau 12 tuần

        LichThi::create([
            'lop_hoc_phan_id' => $lopHocPhan->id,
            'loai_thi' => $loaiThi,
            'ngay_thi' => $ngayThi,
            'gio_bat_dau' => '07:00',
            'gio_ket_thuc' => '09:00',
            'phong_thi_id' => $phongHoc->id,
            'so_sinh_vien_du_thi' => rand(20, 50),
            'giam_thi_1_id' => $giangViens[0]->id,
            'giam_thi_2_id' => $giangViens[1]->id,
            'hinh_thuc' => 'offline',
            'ghi_chu' => 'Sinh viên cần mang theo thẻ sinh viên và CMND/CCCD.',
        ]);
    }
}
