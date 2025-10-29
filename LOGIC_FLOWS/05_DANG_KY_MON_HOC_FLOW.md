# LOGIC FLOW: ĐĂNG KÝ MÔN HỌC

**Phase:** Phase 5 - Đăng ký môn học  
**Actor chính:** Sinh viên, Đào tạo  
**Độ ưu tiên:** ⭐⭐⭐⭐⭐ CỰC CAO - Chức năng cốt lõi

---

## 📊 TỔNG QUAN QUY TRÌNH

```
[Đào tạo mở đăng ký]
        ↓
[Sinh viên xem môn có thể đăng ký]
        ↓
[Sinh viên đăng ký tạm (dang_ky_mon_hoc_tam)]
        ↓
[Hệ thống validate]
        ↓
[Đào tạo chạy thuật toán xếp lớp]
        ↓
[Sinh viên được xếp vào lớp (lop_hoc_phan_sinh_vien)]
        ↓
[Sinh viên xem TKB]
```

---

## 1. MỞ ĐĂNG KÝ MÔN HỌC (Đào tạo)

### 📊 Flowchart:

```
[Đào tạo vào quản lý học kỳ]
        ↓
[Chọn học kỳ cần mở đăng ký]
        ↓
[Thiết lập ngày bắt đầu/kết thúc đăng ký]
        ↓
[Cập nhật hoc_ky.ngay_bat_dau_dang_ky, ngay_ket_thuc_dang_ky]
        ↓
[Cập nhật hoc_ky.la_hoc_ky_hien_tai = true]
        ↓
[Gửi thông báo cho sinh viên]
        ↓
[Sinh viên có thể bắt đầu đăng ký]
```

### 🔧 Chi tiết xử lý:

```php
public function moMoDangKy(Request $request, $hocKyId) {
    // 1. Validate
    $request->validate([
        'ngay_bat_dau_dang_ky' => 'required|date|after:today',
        'ngay_ket_thuc_dang_ky' => 'required|date|after:ngay_bat_dau_dang_ky',
    ]);

    // 2. Update học kỳ
    DB::transaction(function() use ($hocKyId, $request) {
        // Đóng tất cả học kỳ khác
        HocKy::where('id', '!=', $hocKyId)
            ->update(['la_hoc_ky_hien_tai' => false]);

        // Mở học kỳ hiện tại
        HocKy::where('id', $hocKyId)->update([
            'ngay_bat_dau_dang_ky' => $request->ngay_bat_dau_dang_ky,
            'ngay_ket_thuc_dang_ky' => $request->ngay_ket_thuc_dang_ky,
            'la_hoc_ky_hien_tai' => true,
        ]);
    });

    // 3. Gửi thông báo
    $this->guiThongBaoMoDangKy($hocKyId);

    return redirect()->back()
        ->with('success', 'Đã mở đăng ký môn học');
}
```

### 📋 Các bảng liên quan:

-   **hoc_ky** (id, ngay_bat_dau_dang_ky, ngay_ket_thuc_dang_ky, la_hoc_ky_hien_tai)
-   **thong_bao** (để gửi thông báo)

---

## 2. XEM MÔN CÓ THỂ ĐĂNG KÝ (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên vào trang đăng ký môn học]
        ↓
[Kiểm tra thời gian đăng ký]
    ↓                       ↓
[Ngoài thời gian]    [Trong thời gian]
    ↓                       ↓
[Hiển thị thông báo]  [Lấy chuyên ngành sinh viên]
                            ↓
                    [Lấy CTĐT theo chuyên ngành]
                            ↓
                    [Lọc môn theo kỳ hiện tại]
                            ↓
                    [Kiểm tra từng môn:]
                    - Đã đăng ký chưa?
                    - Đã học qua chưa?
                    - Đủ môn tiên quyết chưa?
                            ↓
                    [Hiển thị danh sách môn hợp lệ]
```

### 🔧 Chi tiết xử lý:

```php
public function danhSachMonDangKy() {
    $sinhVien = Auth::user()->sinhVien;

    // 1. Kiểm tra thời gian đăng ký
    $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

    if (!$hocKy ||
        now() < $hocKy->ngay_bat_dau_dang_ky ||
        now() > $hocKy->ngay_ket_thuc_dang_ky) {
        return view('sinhvien.dang-ky.ngoai-thoi-gian');
    }

    // 2. Lấy CTĐT theo chuyên ngành
    $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
        ->with('monHoc')
        ->get();

    // 3. Lọc môn theo kỳ hiện tại sinh viên
    $monHocGoiY = $chuongTrinhKhung
        ->where('hoc_ky_goi_y', $sinhVien->ky_hien_tai)
        ->pluck('monHoc');

    // 4. Kiểm tra từng môn
    $monHopLe = [];

    foreach ($monHocGoiY as $monHoc) {
        $kiemTra = $this->kiemTraDieuKienDangKy($sinhVien->id, $monHoc->id, $hocKy->id);

        if ($kiemTra['hop_le']) {
            $monHopLe[] = [
                'mon_hoc' => $monHoc,
                'lop_hoc_phan' => $this->layLopHocPhan($monHoc->id, $hocKy->id),
            ];
        } else {
            // Lưu lý do không thể đăng ký để hiển thị
            $monHoc->ly_do_khong_the_dang_ky = $kiemTra['ly_do'];
        }
    }

    return view('sinhvien.dang-ky.index', compact('monHopLe', 'hocKy'));
}
```

### 🔧 Hàm kiểm tra điều kiện:

```php
private function kiemTraDieuKienDangKy($sinhVienId, $monHocId, $hocKyId) {
    // 1. Kiểm tra đã đăng ký tạm chưa
    $daDangKyTam = DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
        ->where('mon_hoc_id', $monHocId)
        ->where('hoc_ky_id', $hocKyId)
        ->exists();

    if ($daDangKyTam) {
        return ['hop_le' => false, 'ly_do' => 'Đã đăng ký môn này'];
    }

    // 2. Kiểm tra đã học và qua môn chưa
    $daQuaMon = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVienId, $monHocId) {
            $q->where('sinh_vien_id', $sinhVienId)
              ->whereHas('lopHocPhan', function($q2) use($monHocId) {
                  $q2->where('mon_hoc_id', $monHocId);
              });
        })
        ->where('qua_mon', true)
        ->exists();

    if ($daQuaMon) {
        return ['hop_le' => false, 'ly_do' => 'Đã qua môn này'];
    }

    // 3. Kiểm tra môn tiên quyết
    $monTienQuyet = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
        ->where('loai_tien_quyet', 'bat_buoc')
        ->where('dieu_kien_qua_mon', true)
        ->get();

    foreach ($monTienQuyet as $tq) {
        $daQuaTienQuyet = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVienId, $tq) {
                $q->where('sinh_vien_id', $sinhVienId)
                  ->whereHas('lopHocPhan', function($q2) use($tq) {
                      $q2->where('mon_hoc_id', $tq->mon_tien_quyet_id);
                  });
            })
            ->where('qua_mon', true)
            ->exists();

        if (!$daQuaTienQuyet) {
            $tenMonTienQuyet = MonHoc::find($tq->mon_tien_quyet_id)->ten_mon;
            return [
                'hop_le' => false,
                'ly_do' => "Chưa qua môn tiên quyết: {$tenMonTienQuyet}"
            ];
        }
    }

    // 4. Kiểm tra tổng tín chỉ đã đăng ký trong kỳ
    $tongTinChiDaDangKy = DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
        ->where('hoc_ky_id', $hocKyId)
        ->join('mon_hoc', 'dang_ky_mon_hoc_tam.mon_hoc_id', '=', 'mon_hoc.id')
        ->sum('mon_hoc.so_tin_chi');

    $monHoc = MonHoc::find($monHocId);

    if ($tongTinChiDaDangKy + $monHoc->so_tin_chi > 24) {
        return [
            'hop_le' => false,
            'ly_do' => "Vượt quá 24 tín chỉ/kỳ (hiện tại: {$tongTinChiDaDangKy} tín chỉ)"
        ];
    }

    // 5. Kiểm tra có lớp học phần không
    $coLopHocPhan = LopHocPhan::where('mon_hoc_id', $monHocId)
        ->where('hoc_ky_id', $hocKyId)
        ->where('trang_thai_lop', 'mo_dang_ky')
        ->exists();

    if (!$coLopHocPhan) {
        return ['hop_le' => false, 'ly_do' => 'Chưa mở lớp học phần'];
    }

    return ['hop_le' => true];
}
```

### 📋 Các bảng liên quan:

-   **hoc_ky** (la_hoc_ky_hien_tai, ngay_bat_dau_dang_ky, ngay_ket_thuc_dang_ky)
-   **sinh_vien** (chuyen_nganh_id, ky_hien_tai)
-   **chuong_trinh_khung** (chuyen_nganh_id, mon_hoc_id, hoc_ky_goi_y)
-   **mon_hoc_tien_quyet** (kiểm tra môn tiên quyết)
-   **ket_qua_hoc_tap** (kiểm tra đã qua môn)
-   **dang_ky_mon_hoc_tam** (kiểm tra đã đăng ký)
-   **lop_hoc_phan** (danh sách lớp có thể đăng ký)

---

## 3. ĐĂNG KÝ MÔN HỌC (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên chọn môn học và lớp học phần]
        ↓
[Validate toàn bộ điều kiện]
    ↓                           ↓
[Không hợp lệ]            [Hợp lệ]
    ↓                           ↓
[Return error]      [Insert vào dang_ky_mon_hoc_tam]
                                ↓
                    [Tính độ ưu tiên (uu_tien)]
                                ↓
                    [Gửi thông báo xác nhận]
                                ↓
                    [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function dangKyMonHoc(Request $request) {
    $sinhVien = Auth::user()->sinhVien;
    $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

    // 1. Validate time
    if (now() < $hocKy->ngay_bat_dau_dang_ky ||
        now() > $hocKy->ngay_ket_thuc_dang_ky) {
        return response()->json([
            'success' => false,
            'message' => 'Ngoài thời gian đăng ký'
        ], 400);
    }

    // 2. Validate toàn bộ điều kiện
    $kiemTra = $this->kiemTraDieuKienDangKy(
        $sinhVien->id,
        $request->mon_hoc_id,
        $hocKy->id
    );

    if (!$kiemTra['hop_le']) {
        return response()->json([
            'success' => false,
            'message' => $kiemTra['ly_do']
        ], 400);
    }

    // 3. Tính độ ưu tiên
    $uuTien = $this->tinhDoUuTien($sinhVien, $request->mon_hoc_id);

    // 4. Insert đăng ký tạm
    DB::transaction(function() use ($sinhVien, $request, $hocKy, $uuTien) {
        DangKyMonHocTam::create([
            'sinh_vien_id' => $sinhVien->id,
            'mon_hoc_id' => $request->mon_hoc_id,
            'hoc_ky_id' => $hocKy->id,
            'ngay_dang_ky' => now(),
            'uu_tien' => $uuTien,
            'trang_thai' => 'cho_xep_lop',
        ]);

        // 5. Gửi thông báo
        $this->guiThongBaoDangKy($sinhVien, $request->mon_hoc_id);
    });

    return response()->json([
        'success' => true,
        'message' => 'Đăng ký môn học thành công. Chờ hệ thống xếp lớp.'
    ]);
}
```

### 🔧 Tính độ ưu tiên:

```php
private function tinhDoUuTien($sinhVien, $monHocId) {
    $uuTien = 0;

    // 1. Sinh viên năm cuối (kỳ >= 7) được ưu tiên +100
    if ($sinhVien->ky_hien_tai >= 7) {
        $uuTien += 100;
    }

    // 2. Sinh viên học lại (đã học nhưng chưa qua) +50
    $daHocChuaQua = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVien, $monHocId) {
            $q->where('sinh_vien_id', $sinhVien->id)
              ->whereHas('lopHocPhan', function($q2) use($monHocId) {
                  $q2->where('mon_hoc_id', $monHocId);
              });
        })
        ->where('qua_mon', false)
        ->exists();

    if ($daHocChuaQua) {
        $uuTien += 50;
    }

    // 3. Đăng ký sớm +10
    $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
    $soNgayTuBatDau = now()->diffInDays($hocKy->ngay_bat_dau_dang_ky);

    if ($soNgayTuBatDau <= 2) {
        $uuTien += 10;
    }

    return $uuTien;
}
```

### 📋 Các bảng liên quan:

-   **dang_ky_mon_hoc_tam** (sinh_vien_id, mon_hoc_id, hoc_ky_id, uu_tien, trang_thai)
-   **thong_bao** (gửi thông báo)

---

## 4. HỦY ĐĂNG KÝ (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên chọn hủy môn đã đăng ký]
        ↓
[Kiểm tra trong thời gian cho phép?]
    ↓                       ↓
  [Không]               [Có]
    ↓                       ↓
[Return error]    [Kiểm tra trạng thái]
                        ↓
                  [cho_xep_lop?]
                ↓               ↓
              [Có]            [Không]
                ↓               ↓
        [Xóa khỏi       [Return error]
    dang_ky_mon_hoc_tam]
                ↓
        [Gửi thông báo]
                ↓
        [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function huyDangKy($dangKyId) {
    $sinhVien = Auth::user()->sinhVien;

    // 1. Lấy đăng ký
    $dangKy = DangKyMonHocTam::where('id', $dangKyId)
        ->where('sinh_vien_id', $sinhVien->id)
        ->firstOrFail();

    // 2. Kiểm tra thời gian
    $hocKy = HocKy::find($dangKy->hoc_ky_id);

    if (now() > $hocKy->ngay_ket_thuc_dang_ky) {
        return response()->json([
            'success' => false,
            'message' => 'Đã hết thời gian hủy đăng ký'
        ], 400);
    }

    // 3. Kiểm tra trạng thái
    if ($dangKy->trang_thai !== 'cho_xep_lop') {
        return response()->json([
            'success' => false,
            'message' => 'Không thể hủy môn đã được xếp lớp'
        ], 400);
    }

    // 4. Xóa đăng ký
    DB::transaction(function() use ($dangKy) {
        $dangKy->delete();

        // Gửi thông báo
        $this->guiThongBaoHuyDangKy($dangKy);
    });

    return response()->json([
        'success' => true,
        'message' => 'Đã hủy đăng ký môn học'
    ]);
}
```

---

## 5. THUẬT TOÁN XẾP LỚP TỰ ĐỘNG (Đào tạo)

### 📊 Flowchart:

```
[Đào tạo chạy thuật toán xếp lớp]
        ↓
[Lấy tất cả đăng ký tạm có trang_thai = 'cho_xep_lop']
        ↓
[Sắp xếp theo độ ưu tiên (uu_tien DESC)]
        ↓
[Với mỗi đăng ký:]
        ↓
[Lấy danh sách lớp học phần của môn]
        ↓
[Lọc lớp chưa đầy (so_luong_dang_ky < suc_chua)]
        ↓
[Kiểm tra trùng lịch với các lớp đã xếp]
        ↓
[Có lớp phù hợp?]
    ↓               ↓
  [Không]         [Có]
    ↓               ↓
[Cập nhật      [Insert vào lop_hoc_phan_sinh_vien]
 trang_thai          ↓
 = that_bai]   [Update so_luong_dang_ky++]
                    ↓
              [Cập nhật dang_ky_mon_hoc_tam.trang_thai = da_xep_lop]
                    ↓
              [Tính toán học phí (hoc_phi_hoc_ky)]
        ↓
[Ghi log vào lich_su_xep_lop]
        ↓
[Gửi thông báo kết quả cho sinh viên]
        ↓
[Return thống kê]
```

### 🔧 Chi tiết xử lý:

```php
public function xepLopTuDong($hocKyId) {
    $startTime = now();

    // 1. Lấy tất cả đăng ký chờ xếp lớp
    $dangKyList = DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
        ->where('trang_thai', 'cho_xep_lop')
        ->orderBy('uu_tien', 'desc')
        ->orderBy('ngay_dang_ky', 'asc')
        ->get();

    $soCanXep = $dangKyList->count();
    $soThanhCong = 0;
    $soThatBai = 0;

    DB::transaction(function() use ($dangKyList, &$soThanhCong, &$soThatBai, $hocKyId) {
        foreach ($dangKyList as $dangKy) {
            $ketQua = $this->xepLopChoSinhVien($dangKy, $hocKyId);

            if ($ketQua['thanh_cong']) {
                $soThanhCong++;
            } else {
                $soThatBai++;
            }
        }
    });

    $endTime = now();
    $thoiGianXuLy = $endTime->diffInSeconds($startTime);

    // 2. Ghi log
    LichSuXepLop::create([
        'hoc_ky_id' => $hocKyId,
        'ngay_chay_xep_lop' => $startTime,
        'so_sinh_vien_can_xep' => $soCanXep,
        'so_sinh_vien_xep_thanh_cong' => $soThanhCong,
        'so_sinh_vien_xep_that_bai' => $soThatBai,
        'thoi_gian_xu_ly' => $thoiGianXuLy,
        'nguoi_chay_id' => Auth::id(),
    ]);

    // 3. Gửi thông báo
    $this->guiThongBaoKetQuaXepLop($hocKyId);

    return [
        'success' => true,
        'tong_so' => $soCanXep,
        'thanh_cong' => $soThanhCong,
        'that_bai' => $soThatBai,
        'thoi_gian' => $thoiGianXuLy,
    ];
}
```

### 🔧 Xếp lớp cho từng sinh viên:

```php
private function xepLopChoSinhVien($dangKy, $hocKyId) {
    // 1. Lấy danh sách lớp học phần
    $lopHocPhans = LopHocPhan::where('mon_hoc_id', $dangKy->mon_hoc_id)
        ->where('hoc_ky_id', $hocKyId)
        ->where('trang_thai_lop', 'mo_dang_ky')
        ->whereColumn('so_luong_dang_ky', '<', 'suc_chua')
        ->get();

    if ($lopHocPhans->isEmpty()) {
        $this->capNhatThatBai($dangKy, 'Tất cả lớp đã đầy');
        return ['thanh_cong' => false];
    }

    // 2. Lấy lịch học của sinh viên (các lớp đã xếp)
    $lichHocSinhVien = $this->layLichHocSinhVien($dangKy->sinh_vien_id, $hocKyId);

    // 3. Tìm lớp không trùng lịch
    foreach ($lopHocPhans as $lop) {
        $lichHocLop = LichHocCoDinh::where('lop_hoc_phan_id', $lop->id)->get();

        if (!$this->kiemTraTrungLich($lichHocSinhVien, $lichHocLop)) {
            // Xếp vào lớp này
            $this->xepVaoLop($dangKy, $lop);
            return ['thanh_cong' => true];
        }
    }

    // 4. Không tìm được lớp phù hợp
    $this->capNhatThatBai($dangKy, 'Tất cả lớp đều trùng lịch');
    return ['thanh_cong' => false];
}
```

### 🔧 Kiểm tra trùng lịch:

```php
private function kiemTraTrungLich($lichHocSinhVien, $lichHocLop) {
    foreach ($lichHocSinhVien as $lich1) {
        foreach ($lichHocLop as $lich2) {
            // Cùng thứ
            if ($lich1->thu_trong_tuan == $lich2->thu_trong_tuan) {
                // Trùng tiết
                if ($this->trungTiet($lich1->tiet_bat_dau, $lich1->tiet_ket_thuc,
                                     $lich2->tiet_bat_dau, $lich2->tiet_ket_thuc)) {
                    return true; // Trùng lịch
                }
            }
        }
    }
    return false; // Không trùng
}

private function trungTiet($start1, $end1, $start2, $end2) {
    return !($end1 < $start2 || $end2 < $start1);
}
```

### 🔧 Xếp sinh viên vào lớp:

```php
private function xepVaoLop($dangKy, $lopHocPhan) {
    // 1. Insert vào lop_hoc_phan_sinh_vien
    LopHocPhanSinhVien::create([
        'lop_hoc_phan_id' => $lopHocPhan->id,
        'sinh_vien_id' => $dangKy->sinh_vien_id,
        'dang_ky_tam_id' => $dangKy->id,
        'ngay_dang_ky' => $dangKy->ngay_dang_ky,
        'ngay_xep_lop' => now(),
        'phuong_thuc_xep' => 'tu_dong',
        'trang_thai' => 'da_xep_lop',
    ]);

    // 2. Update số lượng đăng ký
    $lopHocPhan->increment('so_luong_dang_ky');

    // 3. Update trạng thái đăng ký tạm
    $dangKy->update(['trang_thai' => 'da_xep_lop']);

    // 4. Tính học phí
    $this->tinhHocPhi($dangKy->sinh_vien_id, $dangKy->hoc_ky_id);
}
```

### 🔧 Cập nhật thất bại:

```php
private function capNhatThatBai($dangKy, $lyDo) {
    $dangKy->update([
        'trang_thai' => 'that_bai',
        'ly_do_that_bai' => $lyDo,
    ]);
}
```

### 📋 Các bảng liên quan:

-   **dang_ky_mon_hoc_tam** (danh sách đăng ký tạm)
-   **lop_hoc_phan** (danh sách lớp học phần)
-   **lop_hoc_phan_sinh_vien** (kết quả xếp lớp)
-   **lich_hoc_co_dinh** (kiểm tra trùng lịch)
-   **lich_su_xep_lop** (log thuật toán)

---

## 6. XEM KẾT QUẢ XẾP LỚP (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên vào xem kết quả đăng ký]
        ↓
[Lấy danh sách dang_ky_mon_hoc_tam của sinh viên]
        ↓
[Hiển thị theo trạng thái:]
- cho_xep_lop (Đang chờ xếp lớp)
- da_xep_lop (Đã xếp lớp → hiển thị lớp HP)
- that_bai (Thất bại → hiển thị lý do)
        ↓
[Sinh viên xem thời khóa biểu]
```

### 🔧 Chi tiết xử lý:

```php
public function ketQuaDangKy() {
    $sinhVien = Auth::user()->sinhVien;
    $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

    // Lấy kết quả đăng ký
    $dangKyList = DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
        ->where('hoc_ky_id', $hocKy->id)
        ->with('monHoc')
        ->get();

    // Lấy lớp đã xếp
    $lopDaXep = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
        ->whereHas('lopHocPhan', function($q) use($hocKy) {
            $q->where('hoc_ky_id', $hocKy->id);
        })
        ->with(['lopHocPhan.monHoc', 'lopHocPhan.lichHocCoDinh'])
        ->get();

    return view('sinhvien.dang-ky.ket-qua', compact('dangKyList', 'lopDaXep'));
}
```

---

## 7. XEM THỜI KHÓA BIỂU (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên vào xem TKB]
        ↓
[Lấy tất cả lớp học phần đã xếp]
        ↓
[Lấy lịch học cố định của từng lớp]
        ↓
[Sắp xếp theo thứ và tiết]
        ↓
[Hiển thị dạng bảng hoặc calendar]
```

### 🔧 Chi tiết xử lý:

```php
public function thoiKhoaBieu() {
    $sinhVien = Auth::user()->sinhVien;
    $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

    // Lấy tất cả lớp học phần
    $lopHocPhans = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
        ->whereHas('lopHocPhan', function($q) use($hocKy) {
            $q->where('hoc_ky_id', $hocKy->id);
        })
        ->with([
            'lopHocPhan.monHoc',
            'lopHocPhan.lichHocCoDinh.phongHoc',
            'lopHocPhan.lichHocCoDinh.giangVien',
        ])
        ->get();

    // Tạo cấu trúc TKB [thứ][tiết] = [...]
    $tkb = $this->taoThoiKhoaBieu($lopHocPhans);

    return view('sinhvien.tkb.index', compact('tkb'));
}

private function taoThoiKhoaBieu($lopHocPhans) {
    $tkb = [];

    foreach ($lopHocPhans as $lhpsv) {
        $lichHocs = $lhpsv->lopHocPhan->lichHocCoDinh;

        foreach ($lichHocs as $lich) {
            $tkb[$lich->thu_trong_tuan][] = [
                'tiet_bat_dau' => $lich->tiet_bat_dau,
                'tiet_ket_thuc' => $lich->tiet_ket_thuc,
                'gio_bat_dau' => $lich->gio_bat_dau,
                'gio_ket_thuc' => $lich->gio_ket_thuc,
                'mon_hoc' => $lhpsv->lopHocPhan->monHoc->ten_mon,
                'phong' => $lich->phongHoc->ten_phong,
                'giang_vien' => $lich->giangVien->ho_ten,
                'hinh_thuc' => $lich->hinh_thuc,
                'link_online' => $lich->link_online,
            ];
        }
    }

    return $tkb;
}
```

---

## 📊 THỐNG KÊ & BÁO CÁO

### Thống kê đăng ký (Đào tạo):

```php
public function thongKeDangKy($hocKyId) {
    return [
        'tong_dang_ky' => DangKyMonHocTam::where('hoc_ky_id', $hocKyId)->count(),
        'cho_xep_lop' => DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai', 'cho_xep_lop')->count(),
        'da_xep_lop' => DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai', 'da_xep_lop')->count(),
        'that_bai' => DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai', 'that_bai')->count(),
    ];
}
```

---

## 🔒 VALIDATION & SECURITY

### Các điều kiện bắt buộc:

1. ✅ Trong thời gian đăng ký
2. ✅ Chưa đăng ký môn này trong kỳ
3. ✅ Chưa qua môn này trước đó
4. ✅ Đã qua tất cả môn tiên quyết bắt buộc
5. ✅ Tổng tín chỉ <= 24 tín chỉ/kỳ
6. ✅ Có lớp học phần mở đăng ký
7. ✅ Không trùng lịch khi xếp lớp

### Rate Limiting:

```php
// Giới hạn số lần đăng ký liên tục
Route::middleware(['throttle:10,1'])->group(function() {
    Route::post('/dang-ky-mon-hoc', [DangKyController::class, 'store']);
});
```

---

**Ngày tạo:** 27/10/2025  
**Phase:** Phase 5 - Đăng ký môn học  
**Trạng thái:** ⏳ Chưa triển khai (theo lộ trình)
