<?php

namespace Database\Seeders;

<<<<<<< HEAD


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;


=======
>>>>>>> origin/main
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrinhDoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_trinh_do' => 'Cao đẳng'],
            ['ten_trinh_do' => 'Đại học'],
<<<<<<< HEAD

            ['ten_trinh_do' => 'Thạc sĩ'], // 👈 Thêm dòng này
            ['ten_trinh_do' => 'Tiến sĩ'], // 👈 Và dòng này nếu cần
        ];

        foreach ($data as $item) {
            DB::table('dm_trinh_do')->updateOrInsert(
                ['ten_trinh_do' => $item['ten_trinh_do']],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}

            ['ten_trinh_do' => 'Sau đại học'],
=======
            ['ten_trinh_do' => 'Thạc sĩ'], // 👈 Thêm dòng này
            ['ten_trinh_do' => 'Tiến sĩ'], // 👈 Và dòng này nếu cần
>>>>>>> origin/main
        ];

        foreach ($data as $item) {
            DB::table('dm_trinh_do')->updateOrInsert(
                ['ten_trinh_do' => $item['ten_trinh_do']],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
<<<<<<< HEAD


=======
>>>>>>> origin/main
