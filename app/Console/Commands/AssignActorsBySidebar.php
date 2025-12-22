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
        $ma = strtolower($quyen->ma_quyen);

        // ==================== ADMIN ====================
        // Sidebar: User, Vai trò, Thông báo hệ thống, Báo cáo & Thống kê
        
        if (str_contains($ten, 'user') || str_contains($ma, 'user')) {
            return ['admin'];
        }
        
        if (str_contains($ten, 'vai trò') || str_contains($ma, 'vai_tro')) {
            return ['admin'];
        }

        // ==================== PHÒNG ĐÀO TẠO ====================
        // Sidebar: Danh mục & CTĐT, Niên khóa & Học kỳ, Sinh viên, Giảng viên,
        // Lớp học phần, Điểm, Học phí, TKB, Điểm danh, Kỳ thi, Thông báo
        
        // 1. DANH MỤC (Khoa, Ngành, Môn học, Phòng, Ca học, Trình độ, Chuyên ngành)
        if (str_contains($ten, 'khoa') || str_contains($ten, 'ngành') || 
            str_contains($ten, 'môn học') || str_contains($ten, 'phòng') ||
            str_contains($ten, 'ca học') || str_contains($ten, 'trình độ') ||
            str_contains($ten, 'chương trình') || str_contains($ten, 'chuyên ngành') ||
            str_contains($ten, 'trạng thái')) {
            return ['dao_tao'];
        }

        // 2. NIÊN KHÓA & HỌC KỲ
        if (str_contains($ten, 'khóa học') || str_contains($ten, 'học kỳ') ||
            str_contains($ten, 'niên khóa')) {
            return ['dao_tao'];
        }

        // 3. SINH VIÊN - ĐT quản lý, SV xem thông tin cá nhân
        if (str_contains($ten, 'sinh viên')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 4. GIẢNG VIÊN - ĐT quản lý, GV xem thông tin cá nhân
        if (str_contains($ten, 'giảng viên')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien'];
            }
            return ['dao_tao'];
        }

        // 5. LỚP HỌC PHẦN - ĐT quản lý, GV xem lớp dạy, SV xem lớp học
        if (str_contains($ten, 'lớp học phần') || str_contains($ten, 'lớp hp')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 6. ĐIỂM - ĐT quản lý/khóa, GV nhập/sửa/xem, SV xem
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
            return ['dao_tao', 'giang_vien'];
        }

        // 7. HỌC PHÍ - ĐT quản lý, SV xem & thanh toán
        if (str_contains($ten, 'học phí') || str_contains($ten, 'hoc phi')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 8. ĐĂNG KÝ HỌC - ĐT quản lý, SV đăng ký
        if (str_contains($ten, 'đăng ký')) {
            return ['dao_tao', 'sinh_vien'];
        }

        // 9. THỜI KHÓA BIỂU - ĐT quản lý, GV xem lịch dạy, SV xem lịch học
        if (str_contains($ten, 'thời khóa biểu') || str_contains($ten, 'tkb') ||
            str_contains($ten, 'lịch học') || str_contains($ten, 'lịch dạy')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 10. ĐIỂM DANH - GV điểm danh, ĐT xem báo cáo, SV xem
        if (str_contains($ten, 'điểm danh') || str_contains($ten, 'diem danh')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao', 'giang_vien'];
        }

        // 11. KỲ THI & LỊCH THI - ĐT quản lý, GV xem lịch coi thi, SV xem lịch thi
        if (str_contains($ten, 'kỳ thi') || str_contains($ten, 'lịch thi') ||
            str_contains($ten, 'phòng thi')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['dao_tao'];
        }

        // 12. ĐỀ THI & CÂU HỎI - GV tạo đề, ĐT xem
        if (str_contains($ten, 'đề thi') || str_contains($ten, 'câu hỏi') ||
            str_contains($ten, 'ngân hàng đề')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien'];
            }
            return ['giang_vien'];
        }

        // 13. BUỔI HỌC - GV quản lý, ĐT xem
        if (str_contains($ten, 'buổi học')) {
            if (str_contains($ten, 'xem')) {
                return ['dao_tao', 'giang_vien'];
            }
            return ['giang_vien'];
        }

        // 14. THÔNG BÁO - Admin và ĐT gửi, tất cả xem
        if (str_contains($ten, 'thông báo')) {
            if (str_contains($ten, 'xem')) {
                return ['admin', 'dao_tao', 'giang_vien', 'sinh_vien'];
            }
            return ['admin', 'dao_tao'];
        }

        // 15. BÁO CÁO - Admin và ĐT
        if (str_contains($ten, 'báo cáo') || str_contains($ten, 'thống kê')) {
            return ['admin', 'dao_tao'];
        }

        // Mặc định - Phòng đào tạo
        return ['dao_tao'];
    }
}
