<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\GiangVien;
use App\Mail\CanhBaoDiemDanhMail;
use App\Mail\BaoCaoSinhVienYeuMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckAttendanceWarning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-warnings 
                            {--lop-hoc-phan-id= : ID lớp học phần cụ thể}
                            {--threshold=80 : Ngưỡng tỷ lệ chuyên cần (mặc định 80%)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và gửi cảnh báo chuyên cần cho sinh viên có tỷ lệ vắng cao';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== BẮT ĐẦU KIỂM TRA CẢNH BÁO CHUYÊN CẦN ===');
        $this->info('Thời gian: ' . Carbon::now()->format('d/m/Y H:i:s'));
        
        $lopHocPhanId = $this->option('lop-hoc-phan-id');
        $threshold = (float) $this->option('threshold');
        
        $this->info("Ngưỡng cảnh báo: < {$threshold}%");
        
        // Lấy danh sách lớp học phần cần kiểm tra
        $lopHocPhanQuery = LopHocPhan::with('monHoc')
            ->where('trang_thai', 'dang_hoc');
        
        if ($lopHocPhanId) {
            $lopHocPhanQuery->where('id', $lopHocPhanId);
            $this->info("Kiểm tra lớp học phần ID: {$lopHocPhanId}");
        } else {
            $this->info("Kiểm tra tất cả lớp học phần đang học");
        }
        
        $danhSachLopHocPhan = $lopHocPhanQuery->get();
        
        if ($danhSachLopHocPhan->isEmpty()) {
            $this->warn('Không tìm thấy lớp học phần nào.');
            return Command::SUCCESS;
        }
        
        $this->info("Tìm thấy {$danhSachLopHocPhan->count()} lớp học phần");
        
        $tongSinhVienCanhBao = 0;
        $tongEmailDaGui = 0;
        $danhSachCanhBaoToanBo = [];
        
        foreach ($danhSachLopHocPhan as $lopHocPhan) {
            $this->line("---");
            $this->info("Đang xử lý: {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon}");
            
            // Tổng số buổi học đã diễn ra
            $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('ngay_hoc', '<=', Carbon::now())
                ->count();
            
            if ($tongBuoiHoc == 0) {
                $this->comment("  → Chưa có buổi học nào, bỏ qua");
                continue;
            }
            
            $this->info("  Tổng buổi học: {$tongBuoiHoc}");
            
            // Lấy danh sách sinh viên
            $sinhViens = LopHocPhanSinhVien::with('sinhVien.nganh')
                ->where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('trang_thai', 'dang_hoc')
                ->get();
            
            $this->info("  Số sinh viên: {$sinhViens->count()}");
            
            $danhSachCanhBao = [];
            $soLuongCanhBao = 0;
            
            foreach ($sinhViens as $sv) {
                if (!$sv->sinhVien) {
                    continue;
                }
                
                // Thống kê điểm danh
                $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                    ->selectRaw('
                        COUNT(*) as tong_buoi_diem_danh,
                        SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                        SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                        SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                        SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                    ')
                    ->first();
                
                // Tính tỷ lệ chuyên cần
                $tyLeCoMat = $tongBuoiHoc > 0 
                    ? round(($diemDanhStats->co_mat / $tongBuoiHoc) * 100, 1) 
                    : 0;
                
                // Nếu tỷ lệ < threshold
                if ($tyLeCoMat < $threshold) {
                    $thongKe = [
                        'tong_buoi' => $tongBuoiHoc,
                        'co_mat' => $diemDanhStats->co_mat,
                        'vang' => $diemDanhStats->vang,
                        'di_tre' => $diemDanhStats->di_tre,
                        'nghi_phep' => $diemDanhStats->nghi_phep,
                        'ty_le' => $tyLeCoMat,
                    ];
                    
                    $danhSachCanhBao[] = [
                        'sinh_vien' => $sv->sinhVien,
                        'lop_hoc_phan' => $lopHocPhan,
                        'thong_ke' => $thongKe,
                    ];
                    
                    $danhSachCanhBaoToanBo[] = [
                        'sinh_vien' => $sv->sinhVien,
                        'lop_hoc_phan' => $lopHocPhan,
                        'thong_ke' => $thongKe,
                    ];
                    
                    $soLuongCanhBao++;
                    
                    // Gửi email cho sinh viên
                    if ($sv->sinhVien->email) {
                        try {
                            Mail::to($sv->sinhVien->email)->send(
                                new CanhBaoDiemDanhMail($sv->sinhVien, $lopHocPhan, $thongKe)
                            );
                            $tongEmailDaGui++;
                            $this->comment("  ✓ Đã gửi email → {$sv->sinhVien->ma_sinh_vien} ({$tyLeCoMat}%)");
                        } catch (\Exception $e) {
                            $this->error("  ✗ Lỗi gửi email → {$sv->sinhVien->ma_sinh_vien}: {$e->getMessage()}");
                            Log::error('Lỗi gửi email cảnh báo: ' . $e->getMessage(), [
                                'sinh_vien_id' => $sv->sinhVien->id,
                                'lop_hoc_phan_id' => $lopHocPhan->id,
                            ]);
                        }
                    } else {
                        $this->warn("  ! Sinh viên {$sv->sinhVien->ma_sinh_vien} không có email");
                    }
                }
            }
            
            $tongSinhVienCanhBao += $soLuongCanhBao;
            
            if ($soLuongCanhBao > 0) {
                $this->warn("  → Cần cảnh báo: {$soLuongCanhBao} sinh viên");
            } else {
                $this->comment("  → Không có sinh viên nào cần cảnh báo");
            }
        }
        
        // Gửi báo cáo cho giảng viên chủ nhiệm
        if (count($danhSachCanhBaoToanBo) > 0) {
            $this->line("---");
            $this->info("Đang gửi báo cáo cho giảng viên chủ nhiệm...");
            $soEmailGVCN = $this->sendReportToHomeRoomTeachers($danhSachCanhBaoToanBo);
            $this->info("Đã gửi {$soEmailGVCN} email cho GVCN");
        }
        
        $this->line("---");
        $this->info('=== KẾT QUẢ ===');
        $this->info("Tổng sinh viên cần cảnh báo: {$tongSinhVienCanhBao}");
        $this->info("Tổng email đã gửi: {$tongEmailDaGui}");
        $this->info('Hoàn thành lúc: ' . Carbon::now()->format('d/m/Y H:i:s'));
        
        return Command::SUCCESS;
    }
    
    /**
     * Gửi báo cáo cho giảng viên chủ nhiệm
     */
    private function sendReportToHomeRoomTeachers($danhSachCanhBao)
    {
        // Nhóm sinh viên theo giảng viên chủ nhiệm
        $nhomTheoGVCN = [];
        
        foreach ($danhSachCanhBao as $item) {
            $sinhVien = $item['sinh_vien'];
            $nganh = $sinhVien->nganh;
            
            if ($nganh && $nganh->giang_vien_chu_nhiem_id) {
                $gvcnId = $nganh->giang_vien_chu_nhiem_id;
                
                if (!isset($nhomTheoGVCN[$gvcnId])) {
                    $nhomTheoGVCN[$gvcnId] = [];
                }
                
                $nhomTheoGVCN[$gvcnId][] = $item;
            }
        }
        
        $soEmailDaGui = 0;
        
        // Gửi email cho từng giảng viên chủ nhiệm
        foreach ($nhomTheoGVCN as $gvcnId => $danhSach) {
            $giangVienChuNhiem = GiangVien::find($gvcnId);
            
            if ($giangVienChuNhiem && $giangVienChuNhiem->email) {
                try {
                    Mail::to($giangVienChuNhiem->email)->send(
                        new BaoCaoSinhVienYeuMail($giangVienChuNhiem, $danhSach)
                    );
                    $soEmailDaGui++;
                    $this->comment("  ✓ Đã gửi báo cáo → {$giangVienChuNhiem->ho_ten} (" . count($danhSach) . " SV)");
                } catch (\Exception $e) {
                    $this->error("  ✗ Lỗi gửi email GVCN → {$giangVienChuNhiem->ho_ten}: {$e->getMessage()}");
                    Log::error('Lỗi gửi email báo cáo GVCN: ' . $e->getMessage(), [
                        'giang_vien_id' => $gvcnId,
                        'so_sinh_vien' => count($danhSach),
                    ]);
                }
            }
        }
        
        return $soEmailDaGui;
    }
}
