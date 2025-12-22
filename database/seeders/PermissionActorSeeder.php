<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PermissionActorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tự động gán actor cho permissions và quyền cho roles
     */
    public function run(): void
    {
        $this->command->info('=== BẮT ĐẦU GÁN ACTOR CHO PERMISSIONS ===');
        
        // Bước 1: Gán actor cho các quyền dựa trên sidebar
        Artisan::call('permissions:assign-by-sidebar');
        $this->command->info(Artisan::output());
        
        // Bước 2: Gán quyền mặc định cho các vai trò
        $this->command->info('=== BẮT ĐẦU GÁN QUYỀN CHO VAI TRÒ ===');
        Artisan::call('roles:assign-default-permissions');
        $this->command->info(Artisan::output());
        
        $this->command->info('✓ Hoàn thành! Phân quyền đã được thiết lập theo sidebar.');
    }
}
