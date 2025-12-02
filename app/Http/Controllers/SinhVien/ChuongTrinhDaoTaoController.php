<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\DaoTao\SinhVien;
use App\Models\KetQuaHocTap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChuongTrinhDaoTaoController extends Controller
{
    /**
     * Hiển thị chương trình đào tạo của sinh viên
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy thông tin sinh viên
        $sinhVien = SinhVien::with([
            'chuyenNganh.nganh.khoa',
            'nganh.khoa',
            'khoaHoc'
        ])->where('user_id', $user->id)->firstOrFail();

        // Lấy chuyên ngành của sinh viên (từ bảng sinh_vien)
        $chuyenNganh = $sinhVien->chuyenNganh;
        
        // Nếu chưa có chuyên ngành, lấy từ ngành
        if (!$chuyenNganh) {
            // Fallback: lấy ngành từ sinh viên
            $nganh = $sinhVien->nganh;
            if ($nganh) {
                // Tìm chuyên ngành mặc định của ngành này
                $chuyenNganh = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $nganh->id)->first();
            }
        }

        // Kiểm tra chuyên ngành
        if (!$chuyenNganh) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Bạn chưa được phân chuyên ngành. Vui lòng liên hệ phòng Đào tạo.');
        }

        // Lấy chương trình khung theo chuyên ngành
        $chuongTrinhKhung = ChuongTrinhKhung::with(['monHoc.khoa', 'monHoc.monTienQuyet'])
            ->where('chuyen_nganh_id', $chuyenNganh->id)
            ->orderBy('hoc_ky_goi_y')
            ->orderBy('thu_tu_hoc')
            ->get();

        // Lấy kết quả học tập của sinh viên (qua lop_hoc_phan_sinh_vien)
        $ketQuaHocTap = KetQuaHocTap::with([
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.sinhVien'
        ])
            ->whereHas('lopHocPhanSinhVien', function ($query) use ($sinhVien) {
                $query->where('sinh_vien_id', $sinhVien->id);
            })
            ->get()
            ->keyBy(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
            })
            ->filter(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id !== null;
            });

        // Group theo học kỳ
        $chuongTrinhTheoHocKy = $chuongTrinhKhung->groupBy('hoc_ky_goi_y');

        // Tính toán thống kê
        $thongKe = [
            'tong_tin_chi_ctdt' => $chuongTrinhKhung->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            }),
            'tin_chi_bat_buoc' => $chuongTrinhKhung->where('bat_buoc', true)->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            }),
            'tin_chi_tu_chon' => $chuongTrinhKhung->where('bat_buoc', false)->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            }),
            'tin_chi_da_hoc' => $ketQuaHocTap->sum(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi ?? 0;
            }),
            'tin_chi_dat' => $ketQuaHocTap->filter(function ($item) {
                return ($item->diem_he_10 ?? 0) >= 4.0; // Điểm >= 4.0 là đạt
            })->sum(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi ?? 0;
            }),
            'so_mon_da_hoc' => $ketQuaHocTap->count(),
            'so_mon_dat' => $ketQuaHocTap->filter(function ($item) {
                return ($item->diem_he_10 ?? 0) >= 4.0;
            })->count(),
            'so_mon_ctdt' => $chuongTrinhKhung->count(),
        ];

        // Tính tiến độ học tập
        $tienDo = $thongKe['tong_tin_chi_ctdt'] > 0 
            ? round(($thongKe['tin_chi_dat'] / $thongKe['tong_tin_chi_ctdt']) * 100, 2) 
            : 0;

        return view('sinhvien.chuong-trinh-dao-tao.index', compact(
            'sinhVien',
            'chuyenNganh',
            'chuongTrinhTheoHocKy',
            'ketQuaHocTap',
            'thongKe',
            'tienDo'
        ));
    }

    /**
     * Hiển thị điều kiện tốt nghiệp
     */
    public function dieuKienTotNghiep()
    {
        $user = Auth::user();
        
        // Lấy thông tin sinh viên
        $sinhVien = SinhVien::with([
            'chuyenNganh.nganh.khoa',
            'nganh.khoa',
            'khoaHoc'
        ])->where('user_id', $user->id)->firstOrFail();

        // Lấy chuyên ngành của sinh viên (từ bảng sinh_vien)
        $chuyenNganh = $sinhVien->chuyenNganh;
        
        // Nếu chưa có chuyên ngành, lấy từ ngành
        if (!$chuyenNganh) {
            $nganh = $sinhVien->nganh;
            if ($nganh) {
                $chuyenNganh = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $nganh->id)->first();
            }
        }

        // Kiểm tra chuyên ngành
        if (!$chuyenNganh) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Bạn chưa được phân chuyên ngành. Vui lòng liên hệ phòng Đào tạo.');
        }

        // Lấy chương trình khung theo chuyên ngành
        $chuongTrinhKhung = ChuongTrinhKhung::with(['monHoc'])
            ->where('chuyen_nganh_id', $chuyenNganh->id)
            ->get();

        // Lấy kết quả học tập của sinh viên (qua lop_hoc_phan_sinh_vien)
        $ketQuaHocTap = KetQuaHocTap::with([
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.lopHocPhan.hocKy',
            'lopHocPhanSinhVien.sinhVien'
        ])
            ->whereHas('lopHocPhanSinhVien', function ($query) use ($sinhVien) {
                $query->where('sinh_vien_id', $sinhVien->id);
            })
            ->get();

        // Tính toán các điều kiện tốt nghiệp
        $dieuKienTotNghiep = $this->tinhDieuKienTotNghiep($sinhVien, $chuongTrinhKhung, $ketQuaHocTap);

        return view('sinhvien.chuong-trinh-dao-tao.dieu-kien-tot-nghiep', compact(
            'sinhVien',
            'chuyenNganh',
            'dieuKienTotNghiep'
        ));
    }

    /**
     * Tính toán điều kiện tốt nghiệp
     */
    private function tinhDieuKienTotNghiep($sinhVien, $chuongTrinhKhung, $ketQuaHocTap)
    {
        // Tính tổng tín chỉ yêu cầu
        $tongTinChiYeuCau = $chuongTrinhKhung->sum(function ($item) {
            return $item->monHoc->so_tin_chi ?? 0;
        });

        // Tính tín chỉ bắt buộc
        $tinChiBatBuocYeuCau = $chuongTrinhKhung->where('bat_buoc', true)->sum(function ($item) {
            return $item->monHoc->so_tin_chi ?? 0;
        });

        // Tính tín chỉ tự chọn yêu cầu
        $tinChiTuChonYeuCau = $chuongTrinhKhung->where('bat_buoc', false)->sum(function ($item) {
            return $item->monHoc->so_tin_chi ?? 0;
        });

        // Tính tín chỉ đã tích lũy
        $tinChiDaTichLuy = $ketQuaHocTap->filter(function ($item) {
            return ($item->diem_he_10 ?? 0) >= 4.0;
        })->sum(function ($item) {
            return $item->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi ?? 0;
        });

        // Tính điểm trung bình tích lũy
        $tongDiemHe10 = 0;
        $tongTinChi = 0;
        foreach ($ketQuaHocTap as $kqht) {
            $monHoc = $kqht->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
            if ($monHoc && ($kqht->diem_he_10 ?? 0) > 0) {
                $tongDiemHe10 += $kqht->diem_he_10 * ($monHoc->so_tin_chi ?? 0);
                $tongTinChi += $monHoc->so_tin_chi ?? 0;
            }
        }
        $diemTrungBinhTichLuy = $tongTinChi > 0 ? round($tongDiemHe10 / $tongTinChi, 2) : 0;

        // Tính tín chỉ bắt buộc đã đạt
        $monHocBatBuocIds = $chuongTrinhKhung->where('bat_buoc', true)->pluck('mon_hoc_id')->toArray();
        $tinChiBatBuocDaDat = $ketQuaHocTap->filter(function ($item) use ($monHocBatBuocIds) {
            $monHocId = $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
            return $monHocId && in_array($monHocId, $monHocBatBuocIds) && ($item->diem_he_10 ?? 0) >= 4.0;
        })->sum(function ($item) {
            return $item->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi ?? 0;
        });

        // Danh sách môn học chưa đạt hoặc chưa học (môn bắt buộc)
        $monChuaDat = [];
        $monHocBatBuoc = $chuongTrinhKhung->where('bat_buoc', true);
        foreach ($monHocBatBuoc as $item) {
            $ketQua = $ketQuaHocTap->first(function ($kq) use ($item) {
                $monHocId = $kq->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
                return $monHocId == $item->mon_hoc_id;
            });
            if (!$ketQua || ($ketQua->diem_he_10 ?? 0) < 4.0) {
                $monChuaDat[] = [
                    'ma_mon' => $item->monHoc->ma_mon,
                    'ten_mon' => $item->monHoc->ten_mon,
                    'so_tin_chi' => $item->monHoc->so_tin_chi,
                    'hoc_ky_goi_y' => $item->hoc_ky_goi_y,
                    'trang_thai' => $ketQua ? 'Chưa đạt' : 'Chưa học',
                    'diem' => $ketQua ? $ketQua->diem_he_10 : null,
                ];
            }
        }

        // Kiểm tra các điều kiện
        $dieuKien = [
            // Điều kiện 1: Tín chỉ tích lũy
            'tin_chi' => [
                'ten' => 'Tín chỉ tích lũy',
                'yeu_cau' => $tongTinChiYeuCau,
                'da_dat' => $tinChiDaTichLuy,
                'con_thieu' => max(0, $tongTinChiYeuCau - $tinChiDaTichLuy),
                'dat' => $tinChiDaTichLuy >= $tongTinChiYeuCau,
                'phan_tram' => $tongTinChiYeuCau > 0 ? round(($tinChiDaTichLuy / $tongTinChiYeuCau) * 100, 2) : 0,
            ],
            // Điều kiện 2: Môn bắt buộc
            'mon_bat_buoc' => [
                'ten' => 'Môn học bắt buộc',
                'yeu_cau' => $tinChiBatBuocYeuCau,
                'da_dat' => $tinChiBatBuocDaDat,
                'con_thieu' => max(0, $tinChiBatBuocYeuCau - $tinChiBatBuocDaDat),
                'dat' => $tinChiBatBuocDaDat >= $tinChiBatBuocYeuCau,
                'phan_tram' => $tinChiBatBuocYeuCau > 0 ? round(($tinChiBatBuocDaDat / $tinChiBatBuocYeuCau) * 100, 2) : 0,
            ],
            // Điều kiện 3: Điểm trung bình
            'diem_trung_binh' => [
                'ten' => 'Điểm trung bình tích lũy',
                'yeu_cau' => 5.0, // Điểm yêu cầu tốt nghiệp (có thể thay đổi)
                'da_dat' => $diemTrungBinhTichLuy,
                'con_thieu' => max(0, 5.0 - $diemTrungBinhTichLuy),
                'dat' => $diemTrungBinhTichLuy >= 5.0,
                'phan_tram' => ($diemTrungBinhTichLuy / 10) * 100,
            ],
            // Điều kiện 4: Không có môn nợ
            'khong_no_mon' => [
                'ten' => 'Không còn môn nợ (bắt buộc)',
                'so_mon_no' => count($monChuaDat),
                'dat' => count($monChuaDat) == 0,
                'danh_sach' => $monChuaDat,
            ],
        ];

        // Tổng hợp kết quả
        $tongQuat = [
            'du_dieu_kien' => $dieuKien['tin_chi']['dat'] && 
                              $dieuKien['mon_bat_buoc']['dat'] && 
                              $dieuKien['diem_trung_binh']['dat'] && 
                              $dieuKien['khong_no_mon']['dat'],
            'so_dieu_kien_dat' => ($dieuKien['tin_chi']['dat'] ? 1 : 0) +
                                  ($dieuKien['mon_bat_buoc']['dat'] ? 1 : 0) +
                                  ($dieuKien['diem_trung_binh']['dat'] ? 1 : 0) +
                                  ($dieuKien['khong_no_mon']['dat'] ? 1 : 0),
            'tong_dieu_kien' => 4,
        ];

        return [
            'dieu_kien' => $dieuKien,
            'tong_quat' => $tongQuat,
        ];
    }

    /**
     * Hiển thị chi tiết môn học trong CTĐT
     */
    public function chiTietMonHoc($id)
    {
        $user = Auth::user();
        $sinhVien = SinhVien::with(['chuyenNganh', 'nganh'])
            ->where('user_id', $user->id)->firstOrFail();

        // Lấy thông tin môn học trong chương trình khung
        $chuongTrinhKhung = ChuongTrinhKhung::with([
            'monHoc.khoa',
            'monHoc.monTienQuyet',
            'chuyenNganh'
        ])->findOrFail($id);

        // Lấy chuyên ngành của sinh viên
        $chuyenNganhId = $sinhVien->chuyen_nganh_id;
        
        // Nếu chưa có chuyên ngành, thử lấy từ ngành
        if (!$chuyenNganhId) {
            $nganh = $sinhVien->nganh;
            if ($nganh) {
                $chuyenNganh = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $nganh->id)->first();
                $chuyenNganhId = $chuyenNganh ? $chuyenNganh->id : null;
            }
        }

        // Kiểm tra xem môn học có trong CTĐT của sinh viên không
        if (!$chuyenNganhId || $chuongTrinhKhung->chuyen_nganh_id !== $chuyenNganhId) {
            abort(403, 'Môn học này không thuộc chương trình đào tạo của bạn');
        }

        // Lấy kết quả học tập của sinh viên cho môn này (nếu có)
        $ketQuaHocTap = KetQuaHocTap::with([
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.lopHocPhan.hocKy',
            'lopHocPhanSinhVien.sinhVien'
        ])
            ->whereHas('lopHocPhanSinhVien', function ($query) use ($sinhVien, $chuongTrinhKhung) {
                $query->where('sinh_vien_id', $sinhVien->id)
                    ->whereHas('lopHocPhan', function ($q) use ($chuongTrinhKhung) {
                        $q->where('mon_hoc_id', $chuongTrinhKhung->mon_hoc_id);
                    });
            })
            ->first();

        return view('sinhvien.chuong-trinh-dao-tao.chi-tiet', compact(
            'chuongTrinhKhung',
            'ketQuaHocTap',
            'sinhVien'
        ));
    }
}

