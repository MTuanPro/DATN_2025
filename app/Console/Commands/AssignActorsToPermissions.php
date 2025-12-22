<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quyen;
use App\Models\NhomQuyen;
use Illuminate\Support\Facades\DB;

class AssignActorsToPermissions extends Command
{
    protected $signature = 'permissions:assign-actors';
    protected $description = 'Gán actor cho các quyền dựa trên logic nghiệp vụ';

    public function handle()
    {
        $this->info('Bắt đầu gán actor cho các quyền theo logic chi tiết...');

        $this->info('Xóa tất cả actors cũ...');
        DB::table('quyen_actor')->truncate();

        $quyens = Quyen::all();
        $totalAssigned = 0;

        foreach ($quyens as $quyen) {
            $actors = $this->determineActors($quyen);
            
            if (empty($actors)) {
                $this->warn("Bỏ qua quyền: {$quyen->ten_quyen}");
                continue;
            }

            $actorStr = implode(', ', $actors);
            $this->info("{$quyen->ten_quyen} => [{$actorStr}]");

            foreach ($actors as $actor) {
                DB::table('quyen_actor')->insert([
                    'quyen_id' => $quyen->id,
                    'actor' => $actor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalAssigned++;
            }
        }

        $this->info("✓ Hoàn thành! Đã gán {$totalAssigned} actor-permission mappings.");
        return 0;
    }

    /**
     * Xác định actors cho từng quyền dựa trên tên quyền
     */
    private function determineActors($quyen)
    {
        $tenQuyen = strtolower($quyen->ten_quyen);
        $maQuyen = strtolower($quyen->ma_quyen);

        // 1. QUYỀN QUẢN LÝ USER/VAI TRÒ - Admin và Phòng đào tạo
        if (str_contains($tenQuyen, 'user') || str_contains($tenQuyen, 'vai trò')) {
            return ['admin', 'dao_tao'];
        }

        // 2. QUYỀN QUẢN LÝ SINH VIÊN
        if (str_contains($tenQuyen, 'sinh viên')) {
            // Chỉ XEM - cho cả sinh viên
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            // THÊM/SỬA/XÓA - chỉ phòng đào tạo
            return ['dao_tao'];
        }

        // 3. QUYỀN QUẢN LÝ GIẢNG VIÊN
        if (str_contains($tenQuyen, 'giảng viên')) {
            // Chỉ XEM - cho cả giảng viên
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'giang_vien'];
            }
            // THÊM/SỬA/XÓA - chỉ phòng đào tạo
            return ['dao_tao'];
        }

        // 4. QUYỀN QUẢN LÝ DANH MỤC (Khoa, Ngành, Môn học)
        if (str_contains($tenQuyen, 'khoa') || str_contains($tenQuyen, 'ngành') || 
            str_contains($tenQuyen, 'môn học')) {
            // XEM - cho tất cả
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            // THÊM/SỬA/XÓA - chỉ phòng đào tạo
            return ['dao_tao'];
        }

        // 5. QUYỀN QUẢN LÝ LỚP HỌC PHẦN
        if (str_contains($tenQuyen, 'lớp học phần')) {
            // XEM - cho tất cả
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            // THÊM/SỬA/XÓA - chỉ phòng đào tạo
            return ['dao_tao'];
        }

        // 6. QUYỀN QUẢN LÝ ĐIỂM
        if (str_contains($tenQuyen, 'điểm')) {
            // XEM điểm - tất cả
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            // NHẬP/SỬA điểm - phòng đào tạo và giảng viên
            if (str_contains($tenQuyen, 'nhập') || str_contains($tenQuyen, 'sửa')) {
                return ['dao_tao', 'giang_vien'];
            }
            // KHÓA điểm - chỉ phòng đào tạo
            if (str_contains($tenQuyen, 'khóa')) {
                return ['dao_tao'];
            }
            // Mặc định - phòng đào tạo và giảng viên
            return ['dao_tao', 'giang_vien'];
        }

        // 7. QUYỀN QUẢN LÝ HỌC PHÍ
        if (str_contains($tenQuyen, 'học phí')) {
            // XEM học phí - phòng đào tạo và sinh viên
            if (str_contains($tenQuyen, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            // Cấu hình/Thu học phí - chỉ phòng đào tạo
            return ['dao_tao'];
        }

        // 8. QUYỀN ĐĂNG KÝ HỌC
        if (str_contains($tenQuyen, 'đăng ký')) {
            // Sinh viên đăng ký, phòng đào tạo quản lý
            return ['dao_tao', 'sinh_vien'];
        }

        // Mặc định - chỉ phòng đào tạo
        return ['dao_tao'];
    }
}
