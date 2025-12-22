<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VaiTro;
use App\Models\Quyen;
use Illuminate\Support\Facades\DB;

class CleanInvalidRolePermissions extends Command
{
    protected $signature = 'permissions:clean-invalid';
    protected $description = 'Xóa các quyền không hợp lệ khỏi vai trò (quyền không khớp actor)';

    public function handle()
    {
        $this->info('Bắt đầu làm sạch quyền không hợp lệ...');

        $vaiTros = VaiTro::with('quyens.actors')->get();
        $totalRemoved = 0;

        foreach ($vaiTros as $vaiTro) {
            if (!$vaiTro->actor) {
                $this->warn("Vai trò '{$vaiTro->ten_vai_tro}' không có actor, bỏ qua.");
                continue;
            }

            $this->info("Đang xử lý vai trò: {$vaiTro->ten_vai_tro} (actor: {$vaiTro->actor})");

            foreach ($vaiTro->quyens as $quyen) {
                $quyenActors = $quyen->actors->pluck('actor')->toArray();
                
                // Kiểm tra nếu quyền KHÔNG có actor hoặc actor không khớp với vai trò
                if (empty($quyenActors) || !in_array($vaiTro->actor, $quyenActors)) {
                    $this->line("  - Xóa quyền: {$quyen->ten_quyen}");
                    $vaiTro->quyens()->detach($quyen->id);
                    $totalRemoved++;
                }
            }
        }

        $this->info("✓ Hoàn thành! Đã xóa {$totalRemoved} quyền không hợp lệ.");
        return 0;
    }
}
