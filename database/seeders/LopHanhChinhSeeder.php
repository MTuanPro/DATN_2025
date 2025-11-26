<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LopHanhChinhSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID khóa học (K22, K23, K24, K25)
        $khoaHocK22 = DB::table('khoa_hoc')->where('ten_khoa_hoc', 'K22')->value('id');
        $khoaHocK23 = DB::table('khoa_hoc')->where('ten_khoa_hoc', 'K23')->value('id');
        $khoaHocK24 = DB::table('khoa_hoc')->where('ten_khoa_hoc', 'K24')->value('id');
        $khoaHocK25 = DB::table('khoa_hoc')->where('ten_khoa_hoc', 'K25')->value('id');

        if (!$khoaHocK22 || !$khoaHocK23 || !$khoaHocK24 || !$khoaHocK25) {
            echo "❌ Không tìm thấy khóa học K22, K23, K24, K25\n";
            return;
        }

        // Lấy ID ngành
        $nganhs = [
            // CNTT
            'CNTT' => DB::table('nganh')->where('ma_nganh', '7480201')->value('id'),      // Công nghệ thông tin
            'KHMT' => DB::table('nganh')->where('ma_nganh', '7480202')->value('id'),      // Khoa học máy tính
            'ATTT' => DB::table('nganh')->where('ma_nganh', '7480299')->value('id'),      // An toàn thông tin

            // Kinh tế
            'QTKD' => DB::table('nganh')->where('ma_nganh', '7340101')->value('id'),      // Quản trị kinh doanh
            'TCNH' => DB::table('nganh')->where('ma_nganh', '7340201')->value('id'),      // Tài chính - Ngân hàng
            'KT' => DB::table('nganh')->where('ma_nganh', '7340301')->value('id'),        // Kế toán

            // Ngoại ngữ
            'NNA' => DB::table('nganh')->where('ma_nganh', '7220201')->value('id'),       // Ngôn ngữ Anh
            'NNJ' => DB::table('nganh')->where('ma_nganh', '7220203')->value('id'),       // Ngôn ngữ Nhật
            'NNC' => DB::table('nganh')->where('ma_nganh', '7220204')->value('id'),       // Ngôn ngữ Trung Quốc
        ];

        $lopHanhChinh = [];

        // ===== KHÓA K25 (2025-2029) - Sinh viên năm 1 =====
        $lopHanhChinh = array_merge($lopHanhChinh, [
            // CNTT
            ['ma_lop' => 'CNTT25A', 'ten_lop' => 'Công nghệ thông tin K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 45],
            ['ma_lop' => 'CNTT25B', 'ten_lop' => 'Công nghệ thông tin K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 42],

            ['ma_lop' => 'KHMT25A', 'ten_lop' => 'Khoa học máy tính K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 38],
            ['ma_lop' => 'KHMT25B', 'ten_lop' => 'Khoa học máy tính K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 35],

            ['ma_lop' => 'ATTT25A', 'ten_lop' => 'An toàn thông tin K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 40],
            ['ma_lop' => 'ATTT25B', 'ten_lop' => 'An toàn thông tin K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 37],

            // Kinh tế
            ['ma_lop' => 'QTKD25A', 'ten_lop' => 'Quản trị kinh doanh K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 48],
            ['ma_lop' => 'QTKD25B', 'ten_lop' => 'Quản trị kinh doanh K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 46],

            ['ma_lop' => 'TCNH25A', 'ten_lop' => 'Tài chính - Ngân hàng K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 41],
            ['ma_lop' => 'TCNH25B', 'ten_lop' => 'Tài chính - Ngân hàng K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 39],

            ['ma_lop' => 'KT25A', 'ten_lop' => 'Kế toán K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['KT'], 'si_so' => 44],
            ['ma_lop' => 'KT25B', 'ten_lop' => 'Kế toán K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['KT'], 'si_so' => 43],

            // Ngoại ngữ
            ['ma_lop' => 'NNA25A', 'ten_lop' => 'Ngôn ngữ Anh K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNA'], 'si_so' => 36],
            ['ma_lop' => 'NNA25B', 'ten_lop' => 'Ngôn ngữ Anh K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNA'], 'si_so' => 34],

            ['ma_lop' => 'NNJ25A', 'ten_lop' => 'Ngôn ngữ Nhật K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 32],
            ['ma_lop' => 'NNJ25B', 'ten_lop' => 'Ngôn ngữ Nhật K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 30],

            ['ma_lop' => 'NNC25A', 'ten_lop' => 'Ngôn ngữ Trung Quốc K25A', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNC'], 'si_so' => 28],
            ['ma_lop' => 'NNC25B', 'ten_lop' => 'Ngôn ngữ Trung Quốc K25B', 'khoa_hoc_id' => $khoaHocK25, 'nganh_id' => $nganhs['NNC'], 'si_so' => 26],
        ]);

        // ===== KHÓA K24 (2024-2028) - Sinh viên năm 1 =====
        $lopHanhChinh = array_merge($lopHanhChinh, [
            // CNTT
            ['ma_lop' => 'CNTT24A', 'ten_lop' => 'Công nghệ thông tin K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 45],
            ['ma_lop' => 'CNTT24B', 'ten_lop' => 'Công nghệ thông tin K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 42],

            ['ma_lop' => 'KHMT24A', 'ten_lop' => 'Khoa học máy tính K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 38],
            ['ma_lop' => 'KHMT24B', 'ten_lop' => 'Khoa học máy tính K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 35],

            ['ma_lop' => 'ATTT24A', 'ten_lop' => 'An toàn thông tin K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 40],
            ['ma_lop' => 'ATTT24B', 'ten_lop' => 'An toàn thông tin K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 37],

            // Kinh tế
            ['ma_lop' => 'QTKD24A', 'ten_lop' => 'Quản trị kinh doanh K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 48],
            ['ma_lop' => 'QTKD24B', 'ten_lop' => 'Quản trị kinh doanh K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 46],

            ['ma_lop' => 'TCNH24A', 'ten_lop' => 'Tài chính - Ngân hàng K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 41],
            ['ma_lop' => 'TCNH24B', 'ten_lop' => 'Tài chính - Ngân hàng K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 39],

            ['ma_lop' => 'KT24A', 'ten_lop' => 'Kế toán K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['KT'], 'si_so' => 44],
            ['ma_lop' => 'KT24B', 'ten_lop' => 'Kế toán K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['KT'], 'si_so' => 43],

            // Ngoại ngữ
            ['ma_lop' => 'NNA24A', 'ten_lop' => 'Ngôn ngữ Anh K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNA'], 'si_so' => 36],
            ['ma_lop' => 'NNA24B', 'ten_lop' => 'Ngôn ngữ Anh K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNA'], 'si_so' => 34],

            ['ma_lop' => 'NNJ24A', 'ten_lop' => 'Ngôn ngữ Nhật K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 32],
            ['ma_lop' => 'NNJ24B', 'ten_lop' => 'Ngôn ngữ Nhật K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 30],

            ['ma_lop' => 'NNC24A', 'ten_lop' => 'Ngôn ngữ Trung Quốc K24A', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNC'], 'si_so' => 28],
            ['ma_lop' => 'NNC24B', 'ten_lop' => 'Ngôn ngữ Trung Quốc K24B', 'khoa_hoc_id' => $khoaHocK24, 'nganh_id' => $nganhs['NNC'], 'si_so' => 26],
        ]);

        // ===== KHÓA K23 (2023-2027) - Sinh viên năm 2 =====
        $lopHanhChinh = array_merge($lopHanhChinh, [
            // CNTT
            ['ma_lop' => 'CNTT23A', 'ten_lop' => 'Công nghệ thông tin K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 45],
            ['ma_lop' => 'CNTT23B', 'ten_lop' => 'Công nghệ thông tin K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 42],

            ['ma_lop' => 'KHMT23A', 'ten_lop' => 'Khoa học máy tính K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 38],
            ['ma_lop' => 'KHMT23B', 'ten_lop' => 'Khoa học máy tính K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 35],

            ['ma_lop' => 'ATTT23A', 'ten_lop' => 'An toàn thông tin K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 40],
            ['ma_lop' => 'ATTT23B', 'ten_lop' => 'An toàn thông tin K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 37],

            // Kinh tế
            ['ma_lop' => 'QTKD23A', 'ten_lop' => 'Quản trị kinh doanh K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 48],
            ['ma_lop' => 'QTKD23B', 'ten_lop' => 'Quản trị kinh doanh K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 46],

            ['ma_lop' => 'TCNH23A', 'ten_lop' => 'Tài chính - Ngân hàng K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 41],
            ['ma_lop' => 'TCNH23B', 'ten_lop' => 'Tài chính - Ngân hàng K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 39],

            ['ma_lop' => 'KT23A', 'ten_lop' => 'Kế toán K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['KT'], 'si_so' => 44],
            ['ma_lop' => 'KT23B', 'ten_lop' => 'Kế toán K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['KT'], 'si_so' => 43],

            // Ngoại ngữ
            ['ma_lop' => 'NNA23A', 'ten_lop' => 'Ngôn ngữ Anh K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNA'], 'si_so' => 36],
            ['ma_lop' => 'NNA23B', 'ten_lop' => 'Ngôn ngữ Anh K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNA'], 'si_so' => 34],

            ['ma_lop' => 'NNJ23A', 'ten_lop' => 'Ngôn ngữ Nhật K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 32],
            ['ma_lop' => 'NNJ23B', 'ten_lop' => 'Ngôn ngữ Nhật K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 30],

            ['ma_lop' => 'NNC23A', 'ten_lop' => 'Ngôn ngữ Trung Quốc K23A', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNC'], 'si_so' => 28],
            ['ma_lop' => 'NNC23B', 'ten_lop' => 'Ngôn ngữ Trung Quốc K23B', 'khoa_hoc_id' => $khoaHocK23, 'nganh_id' => $nganhs['NNC'], 'si_so' => 26],
        ]);

        // ===== KHÓA K22 (2022-2026) - Sinh viên năm 3 =====
        $lopHanhChinh = array_merge($lopHanhChinh, [
            // CNTT
            ['ma_lop' => 'CNTT22A', 'ten_lop' => 'Công nghệ thông tin K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 43],
            ['ma_lop' => 'CNTT22B', 'ten_lop' => 'Công nghệ thông tin K22B', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['CNTT'], 'si_so' => 40],

            ['ma_lop' => 'KHMT22A', 'ten_lop' => 'Khoa học máy tính K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['KHMT'], 'si_so' => 36],

            ['ma_lop' => 'ATTT22A', 'ten_lop' => 'An toàn thông tin K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['ATTT'], 'si_so' => 38],

            // Kinh tế
            ['ma_lop' => 'QTKD22A', 'ten_lop' => 'Quản trị kinh doanh K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 46],
            ['ma_lop' => 'QTKD22B', 'ten_lop' => 'Quản trị kinh doanh K22B', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['QTKD'], 'si_so' => 44],

            ['ma_lop' => 'TCNH22A', 'ten_lop' => 'Tài chính - Ngân hàng K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['TCNH'], 'si_so' => 39],

            ['ma_lop' => 'KT22A', 'ten_lop' => 'Kế toán K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['KT'], 'si_so' => 42],

            // Ngoại ngữ
            ['ma_lop' => 'NNA22A', 'ten_lop' => 'Ngôn ngữ Anh K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['NNA'], 'si_so' => 34],

            ['ma_lop' => 'NNJ22A', 'ten_lop' => 'Ngôn ngữ Nhật K22A', 'khoa_hoc_id' => $khoaHocK22, 'nganh_id' => $nganhs['NNJ'], 'si_so' => 30],
        ]);



        // Insert data
        $count = 0;
        foreach ($lopHanhChinh as $lop) {
            DB::table('lop_hanh_chinh')->updateOrInsert(
                ['ma_lop' => $lop['ma_lop']],
                array_merge($lop, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            $count++;
        }

        echo "✅ Đã tạo {$count} lớp hành chính\n";
        echo "   📚 Khóa K25: 18 lớp (sinh viên năm 1)\n";
        echo "   📚 Khóa K24: 18 lớp (sinh viên năm 2)\n";
        echo "   📚 Khóa K23: 18 lớp (sinh viên năm 3)\n";
        echo "   📚 Khóa K22: 10 lớp (sinh viên năm 4)\n";
    }
}
