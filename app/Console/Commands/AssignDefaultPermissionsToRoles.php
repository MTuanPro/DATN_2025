<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VaiTro;
use App\Models\Quyen;
use Illuminate\Support\Facades\DB;

class AssignDefaultPermissionsToRoles extends Command
{
    protected $signature = 'roles:assign-default-permissions';
    protected $description = 'Gán quyền mặc định cho các vai trò dựa trên actor';

    public function handle()
    {
        $this->info('Bắt đầu gán quyền mặc định cho các vai trò...');

        $vaiTros = VaiTro::all();

        foreach ($vaiTros as $vaiTro) {
            if (!$vaiTro->actor) {
                $this->warn("Vai trò '{$vaiTro->ten_vai_tro}' không có actor, bỏ qua.");
                continue;
            }

            $this->info("\nĐang xử lý: {$vaiTro->ten_vai_tro} (actor: {$vaiTro->actor})");

            // Lấy tất cả quyền phù hợp với actor của vai trò
            $quyens = Quyen::with('actors')->get()->filter(function($quyen) use ($vaiTro) {
                $quyenActors = $quyen->actors->pluck('actor')->toArray();
                
                // Kiểm tra actor khớp
                if (empty($quyenActors) || !in_array($vaiTro->actor, $quyenActors)) {
                    return false;
                }
                
                // Nếu là NHÂN VIÊN ĐÀO TẠO - chỉ cho quyền XEM
                if ($vaiTro->ma_vai_tro === 'nhan_vien_dt') {
                    $tenQuyen = strtolower($quyen->ten_quyen);
                    return str_contains($tenQuyen, 'xem');
                }
                
                return true;
            });

            if ($quyens->isEmpty()) {
                $this->warn("  Không tìm thấy quyền nào phù hợp với actor '{$vaiTro->actor}'");
                continue;
            }

            $quyenIds = $quyens->pluck('id')->toArray();
            
            // Sync quyền
            $vaiTro->quyens()->sync($quyenIds);
            
            $this->info("  ✓ Đã gán {$quyens->count()} quyền");
            
            // Hiển thị danh sách quyền
            foreach ($quyens->take(5) as $quyen) {
                $this->line("    - {$quyen->ten_quyen}");
            }
            if ($quyens->count() > 5) {
                $this->line("    ... và " . ($quyens->count() - 5) . " quyền khác");
            }
        }

        $this->info("\n✓ Hoàn thành!");
        return 0;
    }
}
