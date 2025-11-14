<?php

namespace App\Services;

use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Models\User;
use App\Models\DaoTao\SinhVien;
use App\Jobs\SendBulkNotificationJob;
use App\Notifications\ThongBaoMoi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Tạo và gửi thông báo
     * 
     * @param array $data
     * @param bool $useQueue
     * @return ThongBao
     */
    public function createNotification(array $data, bool $useQueue = true): ThongBao
    {
        DB::beginTransaction();
        try {
            // Tạo thông báo
            $thongBao = ThongBao::create($data);

            // Lấy danh sách người nhận
            $nguoiNhanIds = $this->getNguoiNhanIds($thongBao);

            if ($useQueue && count($nguoiNhanIds) > 100) {
                // Sử dụng Queue cho số lượng lớn
                SendBulkNotificationJob::dispatch($thongBao->id, $nguoiNhanIds)
                    ->onQueue('notifications');
            } else {
                // Xử lý trực tiếp cho số lượng nhỏ
                $this->createNguoiNhanRecords($thongBao->id, $nguoiNhanIds);
            }

            // Gửi Laravel Notification (database + broadcast)
            if ($thongBao->gui_web_notification ?? true) {
                $this->sendLaravelNotification($thongBao, $nguoiNhanIds);
            }

            DB::commit();
            return $thongBao;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo thông báo: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Tạo thông báo tự động từ hệ thống
     * 
     * @param string $loaiThongBao
     * @param string $tieuDe
     * @param string $noiDung
     * @param array $nguoiNhanIds
     * @param array $options
     * @return ThongBao
     */
    public function createAutoNotification(
        string $loaiThongBao,
        string $tieuDe,
        string $noiDung,
        array $nguoiNhanIds,
        array $options = []
    ): ThongBao {
        $data = array_merge([
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
            'loai_nguon' => 'tu_dong',
            'loai_thong_bao' => $loaiThongBao,
            'muc_do_quan_trong' => 'binh_thuong',
            'doi_tuong' => 'custom',
            'nguoi_gui_id' => null,
            'ngay_gui' => now(),
            'trang_thai' => 'cong_khai',
            'gui_web_notification' => true,
            'gui_email' => false,
            'gui_sms' => false,
        ], $options);

        DB::beginTransaction();
        try {
            $thongBao = ThongBao::create($data);

            // Gửi thông báo
            if (count($nguoiNhanIds) > 100) {
                SendBulkNotificationJob::dispatch($thongBao->id, $nguoiNhanIds)
                    ->onQueue('notifications');
            } else {
                $this->createNguoiNhanRecords($thongBao->id, $nguoiNhanIds);
            }

            DB::commit();
            return $thongBao;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo thông báo tự động: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách ID người nhận dựa vào đối tượng
     * 
     * @param ThongBao $thongBao
     * @return array
     */
    public function getNguoiNhanIds(ThongBao $thongBao): array
    {
        $nguoiNhanIds = [];

        switch ($thongBao->doi_tuong) {
            case 'all':
                $nguoiNhanIds = User::where('trang_thai', 'hoat_dong')
                    ->pluck('id')
                    ->toArray();
                break;

            case 'sinh_vien':
                $nguoiNhanIds = User::whereHas('sinhVien')
                    ->where('trang_thai', 'hoat_dong')
                    ->pluck('id')
                    ->toArray();
                break;

            case 'giang_vien':
                $nguoiNhanIds = User::whereHas('giangVien')
                    ->where('trang_thai', 'hoat_dong')
                    ->pluck('id')
                    ->toArray();
                break;

            case 'dao_tao':
                $nguoiNhanIds = User::whereHas('vaiTro', function ($query) {
                    $query->whereIn('ma_vai_tro', ['truong_phong_dt', 'nhan_vien_dt']);
                })
                    ->where('trang_thai', 'hoat_dong')
                    ->pluck('id')
                    ->toArray();
                break;

            case 'admin':
                $nguoiNhanIds = User::whereHas('vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'admin');
                })
                    ->where('trang_thai', 'hoat_dong')
                    ->pluck('id')
                    ->toArray();
                break;

            case 'lop_hanh_chinh':
                if ($thongBao->doi_tuong_cu_the_id) {
                    $nguoiNhanIds = SinhVien::where('lop_hanh_chinh_id', $thongBao->doi_tuong_cu_the_id)
                        ->whereHas('user', function ($q) {
                            $q->where('trang_thai', 'hoat_dong');
                        })
                        ->pluck('user_id')
                        ->toArray();
                }
                break;

            case 'lop_hoc_phan':
                if ($thongBao->doi_tuong_cu_the_id) {
                    $nguoiNhanIds = DB::table('lop_hoc_phan_sinh_vien')
                        ->where('lop_hoc_phan_id', $thongBao->doi_tuong_cu_the_id)
                        ->join('sinh_vien', 'lop_hoc_phan_sinh_vien.sinh_vien_id', '=', 'sinh_vien.id')
                        ->join('users', 'sinh_vien.user_id', '=', 'users.id')
                        ->where('users.trang_thai', 'hoat_dong')
                        ->pluck('users.id')
                        ->toArray();
                }
                break;
        }

        return array_unique($nguoiNhanIds);
    }

    /**
     * Tạo bản ghi người nhận thông báo (batch insert)
     * 
     * @param int $thongBaoId
     * @param array $nguoiNhanIds
     * @return int Số bản ghi đã tạo
     */
    public function createNguoiNhanRecords(int $thongBaoId, array $nguoiNhanIds): int
    {
        if (empty($nguoiNhanIds)) {
            return 0;
        }

        $now = now();
        $data = [];

        foreach ($nguoiNhanIds as $userId) {
            $data[] = [
                'thong_bao_id' => $thongBaoId,
                'nguoi_nhan_id' => $userId,
                'da_doc' => false,
                'da_gui_email' => false,
                'da_gui_sms' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Batch insert để tối ưu performance
        // Chia nhỏ thành chunks để tránh query quá lớn
        $chunks = array_chunk($data, 500);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            NguoiNhanThongBao::insert($chunk);
            $totalInserted += count($chunk);
        }

        Log::info("Đã tạo {$totalInserted} bản ghi người nhận cho thông báo #{$thongBaoId}");

        return $totalInserted;
    }

    /**
     * Đánh dấu đã đọc thông báo
     * 
     * @param int $thongBaoId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $thongBaoId, int $userId): bool
    {
        $nguoiNhan = NguoiNhanThongBao::where('thong_bao_id', $thongBaoId)
            ->where('nguoi_nhan_id', $userId)
            ->first();

        if ($nguoiNhan && !$nguoiNhan->da_doc) {
            $nguoiNhan->danhDauDaDoc();
            return true;
        }

        return false;
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     * 
     * @param int $userId
     * @return int Số thông báo đã đánh dấu
     */
    public function markAllAsRead(int $userId): int
    {
        return NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->whereHas('thongBao', function ($query) {
                $query->where('trang_thai', 'cong_khai')
                    ->where(function ($q) {
                        $q->whereNull('hien_thi_tu_ngay')
                            ->orWhere('hien_thi_tu_ngay', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('ngay_het_han')
                            ->orWhere('ngay_het_han', '>=', now());
                    });
            })
            ->count();
    }

    /**
     * Gửi thông báo học phí sắp hết hạn
     * 
     * @param int $sinhVienId
     * @param int $hocPhiId
     * @param int $soNgayConLai
     * @return ThongBao|null
     */
    public function sendTuitionDeadlineNotification(int $sinhVienId, int $hocPhiId, int $soNgayConLai): ?ThongBao
    {
        $sinhVien = SinhVien::find($sinhVienId);
        if (!$sinhVien) {
            return null;
        }

        $tieuDe = "Nhắc nhở: Học phí sắp đến hạn ({$soNgayConLai} ngày)";
        $noiDung = "Kính chào {$sinhVien->ho_ten},\n\n"
            . "Học phí của bạn sẽ đến hạn trong {$soNgayConLai} ngày nữa. "
            . "Vui lòng hoàn thành việc đóng học phí để tránh bị ảnh hưởng đến quá trình học tập.\n\n"
            . "Trân trọng!";

        return $this->createAutoNotification(
            'hoc_phi',
            $tieuDe,
            $noiDung,
            [$sinhVien->user_id],
            [
                'muc_do_quan_trong' => $soNgayConLai <= 1 ? 'rat_quan_trong' : 'quan_trong',
                'lien_ket_id' => $hocPhiId,
                'lien_ket_loai' => 'hoc_phi',
                'gui_email' => true,
            ]
        );
    }

    /**
     * Gửi thông báo điểm mới
     * 
     * @param int $sinhVienId
     * @param string $tenMonHoc
     * @param float $diem
     * @return ThongBao|null
     */
    public function sendGradeNotification(int $sinhVienId, string $tenMonHoc, float $diem): ?ThongBao
    {
        $sinhVien = SinhVien::find($sinhVienId);
        if (!$sinhVien) {
            return null;
        }

        $tieuDe = "Điểm môn học mới: {$tenMonHoc}";
        $noiDung = "Kính chào {$sinhVien->ho_ten},\n\n"
            . "Điểm của bạn cho môn {$tenMonHoc} đã được cập nhật.\n"
            . "Điểm: {$diem}/10\n\n"
            . "Vui lòng truy cập hệ thống để xem chi tiết.\n\n"
            . "Trân trọng!";

        return $this->createAutoNotification(
            'diem',
            $tieuDe,
            $noiDung,
            [$sinhVien->user_id],
            [
                'muc_do_quan_trong' => 'quan_trong',
                'gui_email' => true,
            ]
        );
    }

    /**
     * Gửi thông báo đăng ký môn
     * 
     * @param int $sinhVienId
     * @param string $tenMonHoc
     * @param bool $thanhCong
     * @param string|null $lyDo
     * @return ThongBao|null
     */
    public function sendCourseRegistrationNotification(
        int $sinhVienId,
        string $tenMonHoc,
        bool $thanhCong,
        ?string $lyDo = null
    ): ?ThongBao {
        $sinhVien = SinhVien::find($sinhVienId);
        if (!$sinhVien) {
            return null;
        }

        if ($thanhCong) {
            $tieuDe = "Đăng ký môn học thành công";
            $noiDung = "Kính chào {$sinhVien->ho_ten},\n\n"
                . "Bạn đã đăng ký thành công môn: {$tenMonHoc}\n\n"
                . "Vui lòng kiểm tra lịch học và chuẩn bị tham gia lớp.\n\n"
                . "Trân trọng!";
            $mucDo = 'binh_thuong';
        } else {
            $tieuDe = "Đăng ký môn học không thành công";
            $noiDung = "Kính chào {$sinhVien->ho_ten},\n\n"
                . "Đăng ký môn {$tenMonHoc} của bạn không thành công.\n"
                . ($lyDo ? "Lý do: {$lyDo}\n\n" : "\n")
                . "Vui lòng liên hệ phòng đào tạo để được hỗ trợ.\n\n"
                . "Trân trọng!";
            $mucDo = 'quan_trong';
        }

        return $this->createAutoNotification(
            'dang_ky_mon',
            $tieuDe,
            $noiDung,
            [$sinhVien->user_id],
            [
                'muc_do_quan_trong' => $mucDo,
                'gui_email' => false,
            ]
        );
    }

    /**
     * Gửi Laravel Notification đến user
     * Sử dụng database + broadcast channels
     * 
     * @param ThongBao $thongBao
     * @param array $nguoiNhanIds
     * @return void
     */
    public function sendLaravelNotification(ThongBao $thongBao, array $nguoiNhanIds): void
    {
        try {
            // Lấy danh sách users
            $users = User::whereIn('id', $nguoiNhanIds)->get();
            
            if ($users->isEmpty()) {
                return;
            }

            // Gửi notification qua Laravel Notification
            Notification::send($users, new ThongBaoMoi($thongBao));

            Log::info('Laravel Notification đã được gửi', [
                'thong_bao_id' => $thongBao->id,
                'so_nguoi_nhan' => count($nguoiNhanIds),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi Laravel Notification: ' . $e->getMessage(), [
                'thong_bao_id' => $thongBao->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

