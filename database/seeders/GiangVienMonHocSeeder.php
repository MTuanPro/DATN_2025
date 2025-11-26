<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiangVienMonHocSeeder extends Seeder
{
    public function run(): void
    {
        // Map giảng viên với môn học dựa trên chuyên môn

        // Lấy tất cả giảng viên và môn học
        $giangViens = DB::table('giang_vien')->get();
        $monHocs = DB::table('mon_hoc')->get();

        if ($giangViens->isEmpty() || $monHocs->isEmpty()) {
            echo "❌ Cần có dữ liệu giảng viên và môn học trước\n";
            return;
        }

        $mappings = [];

        // Mapping dựa trên chuyên môn và tên môn học
        foreach ($giangViens as $gv) {
            $chuyen_mon = strtolower($gv->chuyen_mon);

            foreach ($monHocs as $mh) {
                $ten_mon = strtolower($mh->ten_mon);
                $ma_mon = strtolower($mh->ma_mon);                // Logic phân công môn học dựa trên chuyên môn
                $should_assign = false;

                // Lập trình Web, Database
                if ((str_contains($chuyen_mon, 'web') || str_contains($chuyen_mon, 'cơ sở dữ liệu')) &&
                    (str_contains($ten_mon, 'web') || str_contains($ten_mon, 'lập trình') ||
                        str_contains($ten_mon, 'cơ sở dữ liệu') || str_contains($ten_mon, 'database'))
                ) {
                    $should_assign = true;
                }

                // Java, Spring
                if ((str_contains($chuyen_mon, 'java') || str_contains($chuyen_mon, 'spring')) &&
                    (str_contains($ten_mon, 'java') || str_contains($ten_mon, 'lập trình hướng đối tượng') ||
                        str_contains($ten_mon, 'oop'))
                ) {
                    $should_assign = true;
                }

                // Phát triển phần mềm, DevOps
                if ((str_contains($chuyen_mon, 'phần mềm') || str_contains($chuyen_mon, 'devops')) &&
                    (str_contains($ten_mon, 'phần mềm') || str_contains($ten_mon, 'công nghệ phần mềm') ||
                        str_contains($ten_mon, 'kiểm thử'))
                ) {
                    $should_assign = true;
                }

                // AI, Machine Learning
                if ((str_contains($chuyen_mon, 'trí tuệ') || str_contains($chuyen_mon, 'machine learning') ||
                        str_contains($chuyen_mon, 'ai')) &&
                    (str_contains($ten_mon, 'trí tuệ nhân tạo') || str_contains($ten_mon, 'machine learning') ||
                        str_contains($ten_mon, 'ai') || str_contains($ten_mon, 'học máy'))
                ) {
                    $should_assign = true;
                }

                // Data Science, Big Data
                if ((str_contains($chuyen_mon, 'dữ liệu') || str_contains($chuyen_mon, 'big data')) &&
                    (str_contains($ten_mon, 'dữ liệu') || str_contains($ten_mon, 'data') ||
                        str_contains($ten_mon, 'khai phá') || str_contains($ten_mon, 'phân tích'))
                ) {
                    $should_assign = true;
                }

                // Thuật toán, Cấu trúc dữ liệu
                if ((str_contains($chuyen_mon, 'thuật toán') || str_contains($chuyen_mon, 'cấu trúc')) &&
                    (str_contains($ten_mon, 'thuật toán') || str_contains($ten_mon, 'cấu trúc dữ liệu') ||
                        str_contains($ten_mon, 'ctdl'))
                ) {
                    $should_assign = true;
                }

                // Deep Learning, Neural Networks
                if ((str_contains($chuyen_mon, 'học sâu') || str_contains($chuyen_mon, 'neural') ||
                        str_contains($chuyen_mon, 'deep learning')) &&
                    (str_contains($ten_mon, 'học sâu') || str_contains($ten_mon, 'deep learning') ||
                        str_contains($ten_mon, 'neural') || str_contains($ten_mon, 'mạng nơ-ron'))
                ) {
                    $should_assign = true;
                }

                // An toàn thông tin, Mật mã
                if ((str_contains($chuyen_mon, 'mật mã') || str_contains($chuyen_mon, 'bảo mật') ||
                        str_contains($chuyen_mon, 'an toàn') || str_contains($chuyen_mon, 'an ninh')) &&
                    (str_contains($ten_mon, 'an toàn') || str_contains($ten_mon, 'bảo mật') ||
                        str_contains($ten_mon, 'mật mã') || str_contains($ten_mon, 'security'))
                ) {
                    $should_assign = true;
                }

                // Hacking, Penetration Testing
                if ((str_contains($chuyen_mon, 'hacking') || str_contains($chuyen_mon, 'penetration')) &&
                    (str_contains($ten_mon, 'an ninh mạng') || str_contains($ten_mon, 'network security') ||
                        str_contains($ten_mon, 'kiểm thử bảo mật'))
                ) {
                    $should_assign = true;
                }

                // Quản trị, Marketing
                if ((str_contains($chuyen_mon, 'quản trị') || str_contains($chuyen_mon, 'marketing')) &&
                    (str_contains($ten_mon, 'quản trị') || str_contains($ten_mon, 'marketing') ||
                        str_contains($ten_mon, 'kinh doanh'))
                ) {
                    $should_assign = true;
                }

                // Nhân sự
                if (
                    str_contains($chuyen_mon, 'nhân sự') &&
                    (str_contains($ten_mon, 'nhân sự') || str_contains($ten_mon, 'quản trị nguồn nhân lực') ||
                        str_contains($ten_mon, 'hành vi tổ chức'))
                ) {
                    $should_assign = true;
                }

                // Kinh doanh quốc tế, Logistics
                if ((str_contains($chuyen_mon, 'quốc tế') || str_contains($chuyen_mon, 'logistics')) &&
                    (str_contains($ten_mon, 'kinh doanh quốc tế') || str_contains($ten_mon, 'xuất nhập khẩu') ||
                        str_contains($ten_mon, 'logistics') || str_contains($ten_mon, 'chuỗi cung ứng'))
                ) {
                    $should_assign = true;
                }

                // Quản trị dự án
                if (
                    str_contains($chuyen_mon, 'dự án') &&
                    (str_contains($ten_mon, 'quản lý dự án') || str_contains($ten_mon, 'project management'))
                ) {
                    $should_assign = true;
                }

                // Tài chính, Đầu tư
                if ((str_contains($chuyen_mon, 'tài chính') || str_contains($chuyen_mon, 'đầu tư')) &&
                    (str_contains($ten_mon, 'tài chính') || str_contains($ten_mon, 'đầu tư') ||
                        str_contains($ten_mon, 'finance'))
                ) {
                    $should_assign = true;
                }

                // Ngân hàng, Tín dụng
                if ((str_contains($chuyen_mon, 'ngân hàng') || str_contains($chuyen_mon, 'tín dụng')) &&
                    (str_contains($ten_mon, 'ngân hàng') || str_contains($ten_mon, 'tiền tệ') ||
                        str_contains($ten_mon, 'banking') || str_contains($ten_mon, 'tín dụng'))
                ) {
                    $should_assign = true;
                }

                // Chứng khoán
                if (
                    str_contains($chuyen_mon, 'chứng khoán') &&
                    (str_contains($ten_mon, 'chứng khoán') || str_contains($ten_mon, 'thị trường tài chính'))
                ) {
                    $should_assign = true;
                }

                // Kế toán
                if (
                    str_contains($chuyen_mon, 'kế toán') &&
                    (str_contains($ten_mon, 'kế toán') || str_contains($ten_mon, 'accounting') ||
                        str_contains($ten_mon, 'kiểm toán'))
                ) {
                    $should_assign = true;
                }

                // Thuế
                if (
                    str_contains($chuyen_mon, 'thuế') &&
                    (str_contains($ten_mon, 'thuế') || str_contains($ten_mon, 'tax'))
                ) {
                    $should_assign = true;
                }

                // Tiếng Anh
                if ((str_contains($chuyen_mon, 'anh') || str_contains($chuyen_mon, 'tesol') ||
                        str_contains($chuyen_mon, 'ielts')) &&
                    (str_contains($ten_mon, 'tiếng anh') || str_contains($ten_mon, 'english') ||
                        str_contains($ma_mon, 'eng'))
                ) {
                    $should_assign = true;
                }

                // Tiếng Nhật
                if ((str_contains($chuyen_mon, 'nhật') || str_contains($chuyen_mon, 'jlpt')) &&
                    (str_contains($ten_mon, 'tiếng nhật') || str_contains($ten_mon, 'japanese') ||
                        str_contains($ten_mon, 'nhật ngữ') || str_contains($ma_mon, 'jpn'))
                ) {
                    $should_assign = true;
                }

                // Tiếng Trung
                if ((str_contains($chuyen_mon, 'trung') || str_contains($chuyen_mon, 'hán') ||
                        str_contains($chuyen_mon, 'hsk')) &&
                    (str_contains($ten_mon, 'tiếng trung') || str_contains($ten_mon, 'chinese') ||
                        str_contains($ten_mon, 'hán ngữ') || str_contains($ma_mon, 'chi'))
                ) {
                    $should_assign = true;
                }

                if ($should_assign) {
                    $mappings[] = [
                        'giang_vien_id' => $gv->id,
                        'mon_hoc_id' => $mh->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($mappings)) {
            // Xóa dữ liệu cũ trước
            DB::table('giang_vien_mon_hoc')->truncate();

            // Insert dữ liệu mới
            DB::table('giang_vien_mon_hoc')->insert($mappings);

            echo "✅ Đã phân công " . count($mappings) . " môn học cho giảng viên\n";

            // Thống kê
            $stats = DB::table('giang_vien_mon_hoc')
                ->join('giang_vien', 'giang_vien_mon_hoc.giang_vien_id', '=', 'giang_vien.id')
                ->select('giang_vien.ho_ten', DB::raw('COUNT(*) as so_mon'))
                ->groupBy('giang_vien.id', 'giang_vien.ho_ten')
                ->get();

            echo "   📊 Thống kê môn học theo giảng viên:\n";
            foreach ($stats as $stat) {
                echo "      - {$stat->ho_ten}: {$stat->so_mon} môn\n";
            }
        } else {
            echo "❌ Không tìm thấy môn học phù hợp với chuyên môn giảng viên\n";
            echo "   💡 Đảm bảo đã chạy MonHocSeeder trước\n";
        }
    }
}
