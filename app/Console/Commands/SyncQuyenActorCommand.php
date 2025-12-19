<?php

namespace App\Console\Commands;

use App\Models\ChucNang;
use App\Models\Quyen;
use Illuminate\Console\Command;

class SyncQuyenActorCommand extends Command
{
    protected $signature = 'permission:sync-actors';
    protected $description = 'Tự động gắn actor cho quyền dựa trên chức năng';

    public function handle()
    {
        // Lấy tất cả chức năng đã gắn quyền
        $chucNangs = ChucNang::whereNotNull('quyen_id')->get();
        $actorQuyenMap = [];

        foreach ($chucNangs as $cn) {
            $actor = $cn->actor;
            $quyenId = $cn->quyen_id;

            if (!isset($actorQuyenMap[$quyenId])) {
                $actorQuyenMap[$quyenId] = [];
            }
            if (!in_array($actor, $actorQuyenMap[$quyenId])) {
                $actorQuyenMap[$quyenId][] = $actor;
            }
        }

        $count = 0;
        foreach ($actorQuyenMap as $quyenId => $actors) {
            $quyen = Quyen::find($quyenId);
            if ($quyen) {
                $quyen->syncActors($actors);
                $this->info("✓ {$quyen->ma_quyen} → [" . implode(', ', $actors) . "]");
                $count++;
            }
        }

        $this->newLine();
        $this->info("Đã gắn actor cho {$count} quyền");

        return 0;
    }
}
