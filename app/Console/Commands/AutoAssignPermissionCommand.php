<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChucNang;
use App\Models\Quyen;
use App\Models\NhomQuyen;

/**
 * Command tự động gắn quyền với chức năng dựa trên quy tắc đặt tên
 * 
 * Quy tắc: route "dao-tao.khoa.store" → quyền "khoa.them"
 * 
 * Sử dụng: php artisan permission:auto-assign
 */
class AutoAssignPermissionCommand extends Command
{
    protected $signature = 'permission:auto-assign {--create-missing : Tạo quyền mới nếu chưa có}';
    protected $description = 'Tự động gắn quyền với chức năng dựa trên quy tắc đặt tên';

    /**
     * Mapping action → mã quyền suffix
     */
    protected array $actionPermissionMap = [
        'index' => 'xem',
        'show' => 'xem',
        'create' => 'them',
        'store' => 'them',
        'edit' => 'sua',
        'update' => 'sua',
        'destroy' => 'xoa',
    ];

    public function handle()
    {
        $createMissing = $this->option('create-missing');
        $chucNangs = ChucNang::whereNull('quyen_id')->where('yeu_cau_quyen', true)->get();

        $assigned = 0;
        $created = 0;
        $skipped = 0;

        foreach ($chucNangs as $chucNang) {
            $maQuyen = $this->generatePermissionCode($chucNang->route_name);

            if (!$maQuyen) {
                $skipped++;
                continue;
            }

            // Tìm quyền có sẵn
            $quyen = Quyen::where('ma_quyen', $maQuyen)->first();

            // Nếu chưa có và được phép tạo mới
            if (!$quyen && $createMissing) {
                $quyen = $this->createPermission($maQuyen, $chucNang);
                if ($quyen) {
                    $created++;
                    $this->line("  + Tạo quyền mới: {$maQuyen}");
                }
            }

            // Gắn quyền với chức năng
            if ($quyen) {
                $chucNang->update(['quyen_id' => $quyen->id]);
                $assigned++;
                $this->line("  ✓ {$chucNang->route_name} → {$maQuyen}");
            } else {
                $skipped++;
                $this->warn("  ✗ Không tìm thấy quyền: {$maQuyen} cho {$chucNang->route_name}");
            }
        }

        $this->newLine();
        $this->info("Kết quả: Gắn {$assigned}, Tạo mới {$created}, Bỏ qua {$skipped}");
    }

    /**
     * Tạo mã quyền từ route name
     * VD: dao-tao.khoa.store → khoa.them
     */
    protected function generatePermissionCode(string $routeName): ?string
    {
        $parts = explode('.', $routeName);

        if (count($parts) < 2) {
            return null;
        }

        $action = end($parts);
        $resource = $parts[count($parts) - 2];

        // Chuyển action sang mã quyền
        $permissionAction = $this->actionPermissionMap[$action] ?? null;
        if (!$permissionAction) {
            return null;
        }

        // Chuẩn hóa resource: sinh-vien → sinh_vien
        $resource = str_replace('-', '_', $resource);

        return "{$resource}.{$permissionAction}";
    }

    /**
     * Tạo quyền mới
     */
    protected function createPermission(string $maQuyen, ChucNang $chucNang): ?Quyen
    {
        // Tìm hoặc tạo nhóm quyền
        $nhomQuyen = NhomQuyen::firstOrCreate(
            ['ma_nhom' => 'auto_' . explode('.', $maQuyen)[0]],
            [
                'ten_nhom' => $chucNang->nhom ?? 'Khác',
                'mo_ta' => 'Nhóm quyền tự động tạo',
            ]
        );

        // Tạo quyền
        $quyen = Quyen::create([
            'ma_quyen' => $maQuyen,
            'ten_quyen' => $chucNang->ten_chuc_nang,
            'mo_ta' => "Quyền cho route: {$chucNang->route_name}",
            'nhom_quyen_id' => $nhomQuyen->id,
        ]);

        // Gắn actor cho quyền
        if ($chucNang->actor) {
            $quyen->syncActors([$chucNang->actor]);
        }

        return $quyen;
    }
}
