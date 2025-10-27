<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $data = [
            ['ma_khoa' => 'CNTT', 'ten_khoa' => 'Công nghệ thông tin', 'mo_ta' => 'Quản lý CNTT'],
            ['ma_khoa' => 'KT', 'ten_khoa' => 'Kinh tế', 'mo_ta' => 'Ngành kinh tế'],
            ['ma_khoa' => 'NN', 'ten_khoa' => 'Ngoại ngữ', 'mo_ta' => 'Ngành ngôn ngữ']
        ];

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'ma_khoa' => strtoupper($faker->lexify('KH??')),
                'ten_khoa' => 'Khoa ' . ucfirst($faker->word()),
                'mo_ta' => $faker->sentence(5),
            ];
        }

        foreach ($data as $item) {
    DB::table('khoa')->updateOrInsert(
        ['ma_khoa' => $item['ma_khoa']],
        array_merge($item, [
            'updated_at' => now(),
            'created_at' => now(),
        ])
    );
}
    }
}
