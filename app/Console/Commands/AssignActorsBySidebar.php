<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quyen;
use Illuminate\Support\Facades\DB;

class AssignActorsBySidebar extends Command
{
    protected $signature = 'permissions:assign-by-sidebar';
    protected $description = 'Gán actor cho quyền dựa trên cấu trúc sidebar thực tế';

    public function handle()
    {
        $this->info('Bắt đầu gán actor dựa trên sidebar...');
        
        DB::table('quyen_actor')->truncate();
        
        $quyens = Quyen::all();
        $totalAssigned = 0;

        foreach ($quyens as $quyen) {
            $actors = $this->getActorsBySidebarLogic($quyen);
            
            if (empty($actors)) {
                $this->warn("Bỏ qua: {$quyen->ten_quyen}");
                continue;
            }

            $this->info("{$quyen->ten_quyen} => [" . implode(', ', $actors) . "]");

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

        $this->info("✓ Đã gán {$totalAssigned} mappings.");
        return 0;
    }

    private function getActorsBySidebarLogic($quyen)
    {
        $ten = strtolower($quyen->ten_quyen);

        // ==================== ADMIN ====================
        // Sidebar Admin CHỈ CÓ: User, Vai trò, Nhóm quyền, Quyền, Thông báo, Báo cáo
        // KHÔNG CÓ: Danh mục, Sinh viên, Giảng viên, Điểm, Học phí, etc.
        
        // 1. Quản lý User
        if (str_contains($ten, 'user') || str_contains($ten, 'tài khoản')) {
            return ['admin'];
        }
        
        // 2. Quản lý Vai trò
        if (str_contains($ten, 'vai trò')) {
            return ['admin'];
        }
        
        // 3. Quản lý Nhóm quyền
        if (str_contains($ten, 'nhóm quyền')) {
            return ['admin'];
        }
        
        // 4. Quản lý Quyền (không phải phân quyền)
        if (str_contains($ten, 'quyền') && !str_contains($ten, 'phân quyền') && 
            !str_contains($ten, 'vai trò')) {
            return ['admin'];
        }

        // ==================== PHÒNG ĐÀO TẠO ====================
        // Có tất cả menu: Danh mục, Niên khóa, Người dùng, Lớp học phần, 
        // Điểm, Học phí, Thời khóa biểu, Kỳ thi, Thông báo
        
        // 1. DANH MỤC (Khoa, Ngành, Môn học, Phòng, Ca học, Trình độ, etc)
        if (str_contains($ten, 'khoa') || 
            str_contains($ten, 'ngành') || 
            str_contains($ten, 'môn học') ||
            str_contains($ten, 'phòng') ||
            str_contains($ten, 'ca học') ||
            str_contains($ten, 'trình độ') ||
            str_contains($ten, 'chương trình') ||
            str_contains($ten, 'chuyên ngành') ||
            str_contains($ten, 'trạng thái')) {
            return ['dao_tao'];
        }

        // 2. NIÊN KHÓA & HỌC KỲ
        if (str_contains($ten, 'khóa học') || 
            str_contains($ten, 'học kỳ') ||
            str_contains($ten, 'niên khóa')) {
            return ['dao_tao'];
        }

        // 3. QUẢN LÝ SINH VIÊN - ĐT quản lý tất cả, SV chỉ xem thông tin cá nhân
        if (str_contains($ten, 'sinh viên')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            return ['dao_tao']; // CRUD
        }

        // 4. QUẢN LÝ GIẢNG VIÊN - ĐT quản lý tất cả, GV chỉ xem thông tin cá nhân
        if (str_contains($ten, 'giảng viên')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien'];
            }
            return ['dao_tao']; // CRUD
        }

        // 5. LỚP HỌC PHẦN - ĐT quản lý, GV xem lớp dạy, SV xem lớp học
        if (str_contains($ten, 'lớp học phần')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao']; // CRUD
        }

        // 6. ĐIỂM - ĐT quản lý, GV nhập/sửa, SV xem
        if (str_contains($ten, 'điểm') && !str_contains($ten, 'điểm danh')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            if (str_contains($ten, 'nhập') || str_contains($ten, 'sửa')) {
                return ['dao_tao', 'giang_vien'];
            }
            if (str_contains($ten, 'khóa')) {
                return ['dao_tao'];
            }
            return ['dao_tao', 'giang_vien']; // Mặc định
        }

        // 7. HỌC PHÍ - ĐT quản lý, SV xem & thanh toán
        if (str_contains($ten, 'học phí')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            return ['dao_tao']; // Cấu hình, Thu
        }

        // 8. ĐĂNG KÝ HỌC - ĐT quản lý, SV đăng ký
        if (str_contains($ten, 'đăng ký')) {
            return ['dao_tao', 'sinh_vien'];
        }

        // 9. THỜI KHÓA BIỂU - ĐT quản lý, GV xem lịch dạy, SV xem lịch học
        if (str_contains($ten, 'thời khóa biểu') || 
            str_contains($ten, 'lịch học') ||
            str_contains($ten, 'lịch dạy')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 10. ĐIỂM DANH - GV điểm danh, ĐT xem báo cáo
        if (str_contains($ten, 'điểm danh')) {
            return ['dao_tao', 'giang_vien'];
        }

        // 11. KỲ THI & LỊCH THI - ĐT quản lý, GV xem lịch coi thi, SV xem lịch thi
        if (str_contains($ten, 'kỳ thi') || 
            str_contains($ten, 'lịch thi') ||
            str_contains($ten, 'phòng thi')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 12. ĐỀ THI & CÂU HỎI - GV tạo đề
        if (str_contains($ten, 'đề thi') || 
            str_contains($ten, 'câu hỏi') ||
            str_contains($ten, 'ngân hàng đề')) {
            return ['giang_vien'];
        }

        // 13. THÔNG BÁO - Admin và ĐT gửi, tất cả xem
        if (str_contains($ten, 'thông báo')) {
            if (str_contains($ten, 'xem')) {
                return ['admin', 'dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['admin', 'dao_tao'];
        }

        // 14. BÁO CÁO - Admin và ĐT
        if (str_contains($ten, 'báo cáo') || str_contains($ten, 'thống kê')) {
            return ['admin', 'dao_tao'];
        }

        // Mặc định - Phòng đào tạo
        return ['dao_tao'];
    }
}
