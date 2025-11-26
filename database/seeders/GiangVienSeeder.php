<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GiangVienSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID các khoa
        $khoaCNTT = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Công nghệ thông tin%')->value('id');
        $khoaKT = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Kinh tế%')->value('id');
        $khoaNN = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Ngoại ngữ%')->value('id');

        if (!$khoaCNTT || !$khoaKT || !$khoaNN) {
            echo "❌ Không tìm thấy đủ các khoa\n";
            return;
        }

        // Lấy ID trình độ
        $trinhDoTS = DB::table('dm_trinh_do')->where('ten_trinh_do', 'Tiến sĩ')->value('id');
        $trinhDoThS = DB::table('dm_trinh_do')->where('ten_trinh_do', 'Thạc sĩ')->value('id');

        if (!$trinhDoTS || !$trinhDoThS) {
            echo "❌ Không tìm thấy trình độ Tiến sĩ hoặc Thạc sĩ\n";
            return;
        }

        // Lấy ID vai trò giảng viên
        $vaiTroGiangVien = DB::table('vai_tro')->where('ten_vai_tro', 'Giảng viên')->value('id');

        if (!$vaiTroGiangVien) {
            echo "❌ Không tìm thấy vai trò Giảng viên\n";
            return;
        }

        // Lấy danh sách môn học để map
        $allMonHocs = DB::table('mon_hoc')->select('id', 'ma_mon', 'ten_mon')->get();

        // Helper function để tìm môn học theo tên
        $findMonHoc = function ($keywords) use ($allMonHocs) {
            $ids = [];
            foreach ($keywords as $keyword) {
                foreach ($allMonHocs as $mh) {
                    if (stripos($mh->ten_mon, $keyword) !== false || stripos($mh->ma_mon, $keyword) !== false) {
                        $ids[] = $mh->id;
                    }
                }
            }
            return array_unique($ids);
        };

        // Danh sách giảng viên với môn học mapping
        $giangViens = [
            // Khoa Công nghệ thông tin (10 người)
            [
                'ma_giang_vien' => 'GV001',
                'ho_ten' => 'Nguyễn Văn An',
                'email' => 'nva.gv@university.edu.vn',
                'so_dien_thoai' => '0901234561',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Lập trình Web', 'Cơ sở dữ liệu', 'HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel'],
                'ngay_vao_truong' => '2015-09-01',
            ],
            [
                'ma_giang_vien' => 'GV002',
                'ho_ten' => 'Trần Thị Bích',
                'email' => 'ttb.gv@university.edu.vn',
                'so_dien_thoai' => '0901234562',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Java', 'Lập trình hướng đối tượng'],
                'ngay_vao_truong' => '2017-03-15',
            ],
            [
                'ma_giang_vien' => 'GV003',
                'ho_ten' => 'Lê Minh Cường',
                'email' => 'lmc.gv@university.edu.vn',
                'so_dien_thoai' => '0901234563',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Phát triển ứng dụng', 'Công nghệ phần mềm'],
                'ngay_vao_truong' => '2018-08-20',
            ],
            [
                'ma_giang_vien' => 'GV004',
                'ho_ten' => 'Phạm Văn Dũng',
                'email' => 'pvd.gv@university.edu.vn',
                'so_dien_thoai' => '0901234564',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Trí tuệ nhân tạo', 'Machine Learning', 'Học máy'],
                'ngay_vao_truong' => '2014-01-10',
            ],
            [
                'ma_giang_vien' => 'GV005',
                'ho_ten' => 'Hoàng Thị Em',
                'email' => 'hte.gv@university.edu.vn',
                'so_dien_thoai' => '0901234565',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Khoa học dữ liệu', 'Big Data', 'Phân tích dữ liệu'],
                'ngay_vao_truong' => '2016-06-05',
            ],
            [
                'ma_giang_vien' => 'GV006',
                'ho_ten' => 'Đỗ Văn Phong',
                'email' => 'dvp.gv@university.edu.vn',
                'so_dien_thoai' => '0901234566',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Thuật toán', 'Cấu trúc dữ liệu', 'Toán rời rạc'],
                'ngay_vao_truong' => '2019-02-14',
            ],
            [
                'ma_giang_vien' => 'GV007',
                'ho_ten' => 'Vũ Thị Giang',
                'email' => 'vtg.gv@university.edu.vn',
                'so_dien_thoai' => '0901234567',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Học sâu', 'Neural Network', 'Deep Learning'],
                'ngay_vao_truong' => '2020-09-01',
            ],
            [
                'ma_giang_vien' => 'GV008',
                'ho_ten' => 'Bùi Văn Hải',
                'email' => 'bvh.gv@university.edu.vn',
                'so_dien_thoai' => '0901234568',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Mật mã', 'An toàn', 'Bảo mật'],
                'ngay_vao_truong' => '2015-04-20',
            ],
            [
                'ma_giang_vien' => 'GV009',
                'ho_ten' => 'Ngô Thị Hoa',
                'email' => 'nth.gv@university.edu.vn',
                'so_dien_thoai' => '0901234569',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['An ninh mạng', 'Bảo mật hệ thống'],
                'ngay_vao_truong' => '2018-11-12',
            ],
            [
                'ma_giang_vien' => 'GV010',
                'ho_ten' => 'Đinh Văn Khải',
                'email' => 'dvk.gv@university.edu.vn',
                'so_dien_thoai' => '0901234570',
                'khoa_id' => $khoaCNTT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Kiểm thử', 'Bảo mật'],
                'ngay_vao_truong' => '2021-01-15',
            ],

            // Khoa Kinh tế (11 người)
            [
                'ma_giang_vien' => 'GV011',
                'ho_ten' => 'Lý Văn Long',
                'email' => 'lvl.gv@university.edu.vn',
                'so_dien_thoai' => '0901234571',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Quản trị', 'Marketing', 'Chiến lược'],
                'ngay_vao_truong' => '2013-09-01',
            ],
            [
                'ma_giang_vien' => 'GV012',
                'ho_ten' => 'Phan Thị Mai',
                'email' => 'ptm.gv@university.edu.vn',
                'so_dien_thoai' => '0901234572',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Nhân sự', 'Hành vi tổ chức', 'Quản trị'],
                'ngay_vao_truong' => '2016-03-10',
            ],
            [
                'ma_giang_vien' => 'GV013',
                'ho_ten' => 'Võ Văn Nam',
                'email' => 'vvn.gv@university.edu.vn',
                'so_dien_thoai' => '0901234573',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Kinh doanh quốc tế', 'Logistics'],
                'ngay_vao_truong' => '2017-07-05',
            ],
            [
                'ma_giang_vien' => 'GV014',
                'ho_ten' => 'Đặng Thị Oanh',
                'email' => 'dto.gv@university.edu.vn',
                'so_dien_thoai' => '0901234574',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Quản trị dự án', 'Quản trị'],
                'ngay_vao_truong' => '2019-01-20',
            ],
            [
                'ma_giang_vien' => 'GV015',
                'ho_ten' => 'Trịnh Văn Phúc',
                'email' => 'tvp.gv@university.edu.vn',
                'so_dien_thoai' => '0901234575',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Tài chính', 'Đầu tư', 'Ngân hàng'],
                'ngay_vao_truong' => '2014-05-15',
            ],
            [
                'ma_giang_vien' => 'GV016',
                'ho_ten' => 'Cao Thị Quỳnh',
                'email' => 'ctq.gv@university.edu.vn',
                'so_dien_thoai' => '0901234576',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Ngân hàng'],
                'ngay_vao_truong' => '2018-02-28',
            ],
            [
                'ma_giang_vien' => 'GV017',
                'ho_ten' => 'Hồ Văn Rộng',
                'email' => 'hvr.gv@university.edu.vn',
                'so_dien_thoai' => '0901234577',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Chứng khoán', 'Tài chính'],
                'ngay_vao_truong' => '2020-06-10',
            ],
            [
                'ma_giang_vien' => 'GV018',
                'ho_ten' => 'Lương Văn Sơn',
                'email' => 'lvs.gv@university.edu.vn',
                'so_dien_thoai' => '0901234578',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Kế toán', 'Kiểm toán'],
                'ngay_vao_truong' => '2015-09-01',
            ],
            [
                'ma_giang_vien' => 'GV019',
                'ho_ten' => 'Dương Thị Tâm',
                'email' => 'dtt.gv@university.edu.vn',
                'so_dien_thoai' => '0901234579',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Kế toán'],
                'ngay_vao_truong' => '2017-11-05',
            ],
            [
                'ma_giang_vien' => 'GV020',
                'ho_ten' => 'Ông Văn Út',
                'email' => 'ovu.gv@university.edu.vn',
                'so_dien_thoai' => '0901234580',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Thuế', 'Kế toán'],
                'ngay_vao_truong' => '2019-04-22',
            ],
            [
                'ma_giang_vien' => 'GV021',
                'ho_ten' => 'Mai Thị Vân',
                'email' => 'mtv.gv@university.edu.vn',
                'so_dien_thoai' => '0901234581',
                'khoa_id' => $khoaKT,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Kế toán', 'Ngân hàng'],
                'ngay_vao_truong' => '2021-08-30',
            ],

            // Khoa Ngoại ngữ (9 người)
            [
                'ma_giang_vien' => 'GV022',
                'ho_ten' => 'Nguyễn Thị Xuân',
                'email' => 'ntx.gv@university.edu.vn',
                'so_dien_thoai' => '0901234582',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Tiếng Anh', 'Anh'],
                'ngay_vao_truong' => '2014-09-01',
            ],
            [
                'ma_giang_vien' => 'GV023',
                'ho_ten' => 'Trần Văn Yên',
                'email' => 'tvy.gv@university.edu.vn',
                'so_dien_thoai' => '0901234583',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Văn học', 'Dịch thuật', 'Anh'],
                'ngay_vao_truong' => '2016-02-15',
            ],
            [
                'ma_giang_vien' => 'GV024',
                'ho_ten' => 'Lê Thị Ánh',
                'email' => 'lta.gv@university.edu.vn',
                'so_dien_thoai' => '0901234584',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Tiếng Anh', 'Giao tiếp'],
                'ngay_vao_truong' => '2018-05-20',
            ],
            [
                'ma_giang_vien' => 'GV025',
                'ho_ten' => 'Phạm Văn Bình',
                'email' => 'pvb.gv@university.edu.vn',
                'so_dien_thoai' => '0901234585',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Tiếng Nhật', 'Nhật'],
                'ngay_vao_truong' => '2015-03-10',
            ],
            [
                'ma_giang_vien' => 'GV026',
                'ho_ten' => 'Hoàng Thị Cẩm',
                'email' => 'htc.gv@university.edu.vn',
                'so_dien_thoai' => '0901234586',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Tiếng Nhật', 'Giao tiếp'],
                'ngay_vao_truong' => '2017-09-05',
            ],
            [
                'ma_giang_vien' => 'GV027',
                'ho_ten' => 'Đỗ Văn Đạt',
                'email' => 'dvd.gv@university.edu.vn',
                'so_dien_thoai' => '0901234587',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Dịch thuật', 'Nhật', 'Kinh doanh'],
                'ngay_vao_truong' => '2019-06-15',
            ],
            [
                'ma_giang_vien' => 'GV028',
                'ho_ten' => 'Vũ Văn Gia',
                'email' => 'vvg.gv@university.edu.vn',
                'so_dien_thoai' => '0901234588',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoTS,
                'mon_hoc_keywords' => ['Tiếng Trung', 'Văn học', 'Hán'],
                'ngay_vao_truong' => '2016-01-20',
            ],
            [
                'ma_giang_vien' => 'GV029',
                'ho_ten' => 'Bùi Thị Hạnh',
                'email' => 'bth.gv@university.edu.vn',
                'so_dien_thoai' => '0901234589',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Tiếng Trung', 'Giao tiếp'],
                'ngay_vao_truong' => '2018-08-25',
            ],
            [
                'ma_giang_vien' => 'GV030',
                'ho_ten' => 'Ngô Văn Khánh',
                'email' => 'nvk.gv@university.edu.vn',
                'so_dien_thoai' => '0901234590',
                'khoa_id' => $khoaNN,
                'trinh_do_id' => $trinhDoThS,
                'mon_hoc_keywords' => ['Thương mại', 'Trung', 'Dịch thuật'],
                'ngay_vao_truong' => '2020-10-01',
            ],
        ];

        $count = 0;
        $totalMonHocAssigned = 0;

        foreach ($giangViens as $gvData) {
            // Tạo user trước
            $userId = DB::table('users')->insertGetId([
                'name' => $gvData['ho_ten'],
                'email' => $gvData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán vai trò giảng viên cho user
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $userId,
                'vai_tro_id' => $vaiTroGiangVien,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo giảng viên (không có chuyen_mon nữa)
            $giangVienId = DB::table('giang_vien')->insertGetId([
                'user_id' => $userId,
                'ma_giang_vien' => $gvData['ma_giang_vien'],
                'ho_ten' => $gvData['ho_ten'],
                'email' => $gvData['email'],
                'so_dien_thoai' => $gvData['so_dien_thoai'],
                'khoa_id' => $gvData['khoa_id'],
                'trinh_do_id' => $gvData['trinh_do_id'],
                'ngay_vao_truong' => $gvData['ngay_vao_truong'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán môn học cho giảng viên
            $monHocIds = $findMonHoc($gvData['mon_hoc_keywords']);
            if (!empty($monHocIds)) {
                foreach ($monHocIds as $monHocId) {
                    DB::table('giang_vien_mon_hoc')->insert([
                        'giang_vien_id' => $giangVienId,
                        'mon_hoc_id' => $monHocId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $totalMonHocAssigned += count($monHocIds);
            }

            $count++;
        }

        echo "✅ Đã tạo {$count} giảng viên\n";
        echo "   👨‍🏫 Khoa CNTT: 10 giảng viên\n";
        echo "   👨‍🏫 Khoa Kinh tế: 11 giảng viên\n";
        echo "   👨‍🏫 Khoa Ngoại ngữ: 9 giảng viên\n";
        echo "   📚 Đã gán {$totalMonHocAssigned} môn học cho các giảng viên\n";
        echo "   🔑 Mật khẩu mặc định: password123\n";
    }
}
