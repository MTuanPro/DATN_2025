<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\CanhBaoHocVu;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class TaoCanhBaoVangNhieuCommand extends Command
{
    protected $signature = 'canh-bao:tao-vang-nhieu {--lop-hoc-phan-id= : ID lớp học phần cụ thể}';
    protected $description = 'Tạo cảnh báo vắng nhiều cho tất cả sinh viên có tỷ lệ vắng > 20%';

    public function handle()
    {
        $this->info('🔧 Bắt đầu tạo cảnh báo vắng nhiều...');
        $this->newLine();

        $query = LopHocPhanSinhVien::with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy', 'sinhVien'])
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);

        if ($this->option('lop-hoc-phan-id')) {
            $query->where('lop_hoc_phan_id', $this->option('lop-hoc-phan-id'));
        }

        $lopHocPhanSinhViens = $query->get();

        $this->info("Tìm thấy {$lopHocPhanSinhViens->count()} lớp học phần sinh viên cần kiểm tra");
        $this->newLine();

        $bar = $this->output->createProgressBar($lopHocPhanSinhViens->count());
        $bar->start();

        $success = 0;
        $skipped = 0;

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            try {
                if (!$lhpsv->sinhVien || !$lhpsv->lopHocPhan || !$lhpsv->lopHocPhan->hocKy) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $sinhVien = $lhpsv->sinhVien;
                $lopHocPhan = $lhpsv->lopHocPhan;
                $hocKy = $lopHocPhan->hocKy;

                // Lấy tổng số buổi học
                $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('trang_thai', '!=', 'huy')
                    ->count();

                if ($tongBuoiHoc == 0) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Tính thống kê điểm danh
                $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                    ->selectRaw('
                        COUNT(*) as tong_buoi_diem_danh,
                        SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                        SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                        SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                        SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                    ')
                    ->first();

                $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
                $vang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
                
                // Tính tỷ lệ vắng
                $tyLeVang = ($vang / $tongBuoiHoc) * 100;
                $tyLeCoMat = ($coMat / $tongBuoiHoc) * 100;

                // Nếu vắng > 20% → tạo cảnh báo
                if ($tyLeVang > 20) {
                    // Kiểm tra xem đã có cảnh báo chưa
                    $canhBaoTonTai = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
                        ->where('hoc_ky_id', $hocKy->id)
                        ->where('loai_canh_bao', 'vang_nhieu')
                        ->where('trang_thai', 'chua_xu_ly')
                        ->where('ghi_chu', 'like', "%{$lopHocPhan->ma_lop_hp}%")
                        ->first();

                    $thongKe = [
                        'tong_buoi' => $tongBuoiHoc,
                        'co_mat' => $coMat,
                        'vang' => $vang,
                        'di_tre' => $diemDanhStats ? ($diemDanhStats->di_tre ?? 0) : 0,
                        'nghi_phep' => $diemDanhStats ? ($diemDanhStats->nghi_phep ?? 0) : 0,
                        'ty_le' => $tyLeCoMat,
                    ];

                    // Xác định mức độ cảnh báo
                    $mucDo = 'canh_cao';
                    if ($tyLeCoMat < 60) {
                        $mucDo = 'dinh_chi';
                    } elseif ($tyLeCoMat < 70) {
                        $mucDo = 'canh_cao';
                    }

                    if ($canhBaoTonTai) {
                        // Cập nhật cảnh báo hiện có
                        $canhBaoTonTai->update([
                            'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
                            'muc_do' => $mucDo,
                            'ngay_canh_bao' => now(),
                        ]);
                    } else {
                        // Tạo cảnh báo mới
                        CanhBaoHocVu::create([
                            'sinh_vien_id' => $sinhVien->id,
                            'hoc_ky_id' => $hocKy->id,
                            'loai_canh_bao' => 'vang_nhieu',
                            'muc_do' => $mucDo,
                            'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
                            'ngay_canh_bao' => now(),
                            'trang_thai' => 'chua_xu_ly',
                            'ghi_chu' => "Tự động tạo từ hệ thống điểm danh. Môn: {$lopHocPhan->monHoc->ten_mon} - Lớp: {$lopHocPhan->ma_lop_hp}",
                        ]);
                    }

                    $success++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("\nLỗi khi xử lý LHP SV ID {$lhpsv->id}: " . $e->getMessage());
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Đã tạo/cập nhật: $success cảnh báo");
        $this->info("⏭️  Bỏ qua: $skipped");
        $this->info('Hoàn thành!');

        return 0;
    }

    protected function taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat): string
    {
        $lyDo = "Vắng quá 20% số buổi học. ";
        $lyDo .= "Tổng số buổi học: {$thongKe['tong_buoi']}, ";
        $lyDo .= "Có mặt: {$thongKe['co_mat']}, ";
        $lyDo .= "Vắng: {$thongKe['vang']}, ";
        $lyDo .= "Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%";
        
        return $lyDo;
    }
}

