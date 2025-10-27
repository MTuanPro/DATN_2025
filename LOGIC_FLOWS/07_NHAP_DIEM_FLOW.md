# LOGIC FLOW: NHẬP ĐIỂM & TÍNH ĐIỂM

**Phase:** Phase 7 - Nhập điểm & Đánh giá  
**Actor chính:** Giảng viên, Đào tạo, Sinh viên  
**Độ ưu tiên:** ⭐⭐⭐⭐ CAO

---

## 📊 TỔNG QUAN QUY TRÌNH

```
[GV nhập điểm thành phần theo cấu hình]
        ↓
[Hệ thống tính điểm tổng kết]
        ↓
[GV khóa điểm]
        ↓
[Đào tạo duyệt điểm]
        ↓
[Công bố điểm cho sinh viên]
```

---

## 1. XEM CẤU HÌNH ĐẦU ĐIỂM

### 📊 Flowchart:

```
[GV vào trang nhập điểm]
        ↓
[Chọn lớp học phần]
        ↓
[Xem cấu hình đầu điểm]
- Tên đầu điểm
- Tỷ lệ %
- Số cột điểm
        ↓
[Hiển thị danh sách sinh viên]
```

### 🔧 Chi tiết xử lý:

```php
public function xemCauHinhDiem($lopHocPhanId) {
    $giangVien = Auth::user()->giangVien;

    // 1. Kiểm tra quyền truy cập
    $duocPhanCong = LopHocPhanGiangVien::where('lop_hoc_phan_id', $lopHocPhanId)
        ->where('giang_vien_id', $giangVien->id)
        ->exists();

    if (!$duocPhanCong) {
        abort(403, 'Bạn không có quyền truy cập lớp này');
    }

    // 2. Lấy cấu hình đầu điểm
    $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
        ->orderBy('ten_dau_diem')
        ->get();

    // 3. Lấy danh sách sinh viên
    $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
        ->where('trang_thai', 'dang_hoc')
        ->with('sinhVien')
        ->get();

    // 4. Kiểm tra trạng thái khóa điểm
    $lopHocPhan = LopHocPhan::find($lopHocPhanId);
    $daKhoaDiem = $lopHocPhan->trang_thai_lop === 'da_khoa_diem';

    return view('giangvien.nhap-diem.index', compact(
        'cauHinhs',
        'sinhViens',
        'lopHocPhan',
        'daKhoaDiem'
    ));
}
```

### 📋 Các bảng liên quan:

-   **cau_hinh_dau_diem** (danh sách đầu điểm)
-   **lop_hoc_phan_sinh_vien** (danh sách sinh viên)
-   **lop_hoc_phan** (trạng thái khóa điểm)

---

## 2. NHẬP ĐIỂM THÀNH PHẦN

### 📊 Flowchart:

```
[GV nhập điểm cho sinh viên]
        ↓
[Validate]
- Điểm 0-10
- Không được rỗng
        ↓
[Kiểm tra lớp đã khóa điểm?]
    ↓                   ↓
[Đã khóa]          [Chưa khóa]
    ↓                   ↓
[Return error]  [Insert/Update nhap_diem]
                        ↓
                [Tự động tính điểm tổng]
                        ↓
                [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function nhapDiem(Request $request) {
    // 1. Validate
    $validated = $request->validate([
        'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
        'cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
        'cot_diem' => 'required|integer|min:1',
        'diem_so' => 'required|numeric|min:0|max:10',
        'ghi_chu' => 'nullable|string',
    ]);

    // 2. Kiểm tra quyền
    $lhpsv = LopHocPhanSinhVien::find($validated['lop_hoc_phan_sinh_vien_id']);
    $giangVien = Auth::user()->giangVien;

    $duocPhanCong = LopHocPhanGiangVien::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
        ->where('giang_vien_id', $giangVien->id)
        ->exists();

    if (!$duocPhanCong) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền nhập điểm lớp này'
        ], 403);
    }

    // 3. Kiểm tra lớp đã khóa điểm chưa
    $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);

    if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem') {
        return response()->json([
            'success' => false,
            'message' => 'Lớp đã khóa điểm, không thể sửa'
        ], 400);
    }

    // 4. Kiểm tra cột điểm hợp lệ
    $cauHinh = CauHinhDauDiem::find($validated['cau_hinh_id']);

    if ($validated['cot_diem'] > $cauHinh->so_cot) {
        return response()->json([
            'success' => false,
            'message' => "Cột điểm không hợp lệ (max: {$cauHinh->so_cot})"
        ], 400);
    }

    // 5. Insert hoặc Update
    DB::transaction(function() use ($validated) {
        NhapDiem::updateOrCreate(
            [
                'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                'cau_hinh_id' => $validated['cau_hinh_id'],
                'cot_diem' => $validated['cot_diem'],
            ],
            [
                'diem_so' => $validated['diem_so'],
                'ghi_chu' => $validated['ghi_chu'],
            ]
        );

        // 6. Tự động tính điểm tổng
        $this->tinhDiemTong($validated['lop_hoc_phan_sinh_vien_id']);
    });

    return response()->json([
        'success' => true,
        'message' => 'Đã nhập điểm thành công'
    ]);
}
```

### 📋 Các bảng liên quan:

-   **nhap_diem** (lưu điểm thành phần)
-   **cau_hinh_dau_diem** (kiểm tra số cột)
-   **lop_hoc_phan** (kiểm tra trạng thái khóa)

---

## 3. TÍNH ĐIỂM TỔNG TỰ ĐỘNG

### 📊 Flowchart:

```
[Lấy tất cả cấu hình đầu điểm]
        ↓
[Với mỗi đầu điểm:]
        ↓
[Lấy tất cả điểm đã nhập theo cột]
        ↓
[Tính trung bình các cột]
        ↓
[Nhân với tỷ lệ %]
        ↓
[Cộng tổng tất cả đầu điểm]
        ↓
[Tính điểm hệ 4 và điểm chữ]
        ↓
[Update ket_qua_hoc_tap]
```

### 🔧 Chi tiết xử lý:

```php
private function tinhDiemTong($lopHocPhanSinhVienId) {
    $lhpsv = LopHocPhanSinhVien::find($lopHocPhanSinhVienId);

    // 1. Lấy cấu hình đầu điểm
    $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)->get();

    $tongDiem = 0;
    $daCoTatCaDiem = true;

    // 2. Tính điểm từng đầu
    foreach ($cauHinhs as $cauHinh) {
        // Lấy điểm đã nhập
        $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
            ->where('cau_hinh_id', $cauHinh->id)
            ->get();

        // Kiểm tra đủ cột chưa
        if ($diems->count() < $cauHinh->so_cot) {
            $daCoTatCaDiem = false;
            continue;
        }

        // Tính trung bình các cột
        $diemTrungBinh = $diems->avg('diem_so');

        // Nhân với tỷ lệ %
        $tongDiem += $diemTrungBinh * ($cauHinh->ty_le / 100);
    }

    // 3. Nếu chưa đủ điểm thì không tính
    if (!$daCoTatCaDiem) {
        return;
    }

    // 4. Làm tròn 2 chữ số
    $diemHe10 = round($tongDiem, 2);

    // 5. Tính điểm hệ 4
    $diemHe4 = $this->chuyenDoiHe4($diemHe10);

    // 6. Tính điểm chữ
    $diemChu = $this->chuyenDoiDiemChu($diemHe10);

    // 7. Qua môn?
    $quaMon = $diemHe10 >= 4.0;

    // 8. Update kết quả học tập
    KetQuaHocTap::updateOrCreate(
        ['lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId],
        [
            'diem_he_10' => $diemHe10,
            'diem_he_4' => $diemHe4,
            'diem_chu' => $diemChu,
            'qua_mon' => $quaMon,
        ]
    );
}
```

### 🔧 Chuyển đổi hệ điểm:

```php
private function chuyenDoiHe4($diemHe10) {
    if ($diemHe10 >= 9.0) return 4.0;
    if ($diemHe10 >= 8.5) return 3.7;
    if ($diemHe10 >= 8.0) return 3.5;
    if ($diemHe10 >= 7.0) return 3.0;
    if ($diemHe10 >= 6.5) return 2.5;
    if ($diemHe10 >= 5.5) return 2.0;
    if ($diemHe10 >= 5.0) return 1.5;
    if ($diemHe10 >= 4.0) return 1.0;
    return 0.0;
}

private function chuyenDoiDiemChu($diemHe10) {
    if ($diemHe10 >= 9.0) return 'A+';
    if ($diemHe10 >= 8.5) return 'A';
    if ($diemHe10 >= 8.0) return 'B+';
    if ($diemHe10 >= 7.0) return 'B';
    if ($diemHe10 >= 6.5) return 'C+';
    if ($diemHe10 >= 5.5) return 'C';
    if ($diemHe10 >= 5.0) return 'D+';
    if ($diemHe10 >= 4.0) return 'D';
    return 'F';
}
```

### 📋 Các bảng liên quan:

-   **cau_hinh_dau_diem** (lấy tỷ lệ %)
-   **nhap_diem** (lấy điểm thành phần)
-   **ket_qua_hoc_tap** (lưu điểm tổng)

---

## 4. KHÓA ĐIỂM (Giảng viên)

### 📊 Flowchart:

```
[GV chọn khóa điểm]
        ↓
[Kiểm tra tất cả SV đã có điểm?]
    ↓                       ↓
[Chưa đủ]              [Đủ]
    ↓                       ↓
[Cảnh báo]          [Cập nhật trang_thai_lop = 'da_khoa_diem']
[Cho phép tiếp tục?]        ↓
    ↓                 [Gửi thông báo cho Đào tạo]
[Khóa]                      ↓
                      [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function khoaDiem($lopHocPhanId) {
    $giangVien = Auth::user()->giangVien;

    // 1. Kiểm tra quyền (phải là GV chính)
    $gvChinh = LopHocPhanGiangVien::where('lop_hoc_phan_id', $lopHocPhanId)
        ->where('giang_vien_id', $giangVien->id)
        ->where('vai_tro', 'giang_vien_chinh')
        ->exists();

    if (!$gvChinh) {
        return response()->json([
            'success' => false,
            'message' => 'Chỉ giảng viên chính mới có quyền khóa điểm'
        ], 403);
    }

    // 2. Kiểm tra tất cả sinh viên đã có điểm chưa
    $tongSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
        ->where('trang_thai', 'dang_hoc')
        ->count();

    $sinhVienCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId)
              ->where('trang_thai', 'dang_hoc');
        })
        ->whereNotNull('diem_he_10')
        ->count();

    if ($sinhVienCoDiem < $tongSinhVien) {
        $soThieu = $tongSinhVien - $sinhVienCoDiem;

        return response()->json([
            'success' => false,
            'message' => "Còn {$soThieu} sinh viên chưa có điểm. Bạn có chắc muốn khóa?",
            'can_confirm' => true,
        ], 400);
    }

    // 3. Khóa điểm
    DB::transaction(function() use ($lopHocPhanId, $giangVien) {
        LopHocPhan::where('id', $lopHocPhanId)->update([
            'trang_thai_lop' => 'da_khoa_diem',
            'ngay_khoa_diem' => now(),
            'nguoi_khoa_diem_id' => $giangVien->id,
        ]);

        // Gửi thông báo cho đào tạo
        $this->guiThongBaoKhoaDiem($lopHocPhanId);
    });

    return response()->json([
        'success' => true,
        'message' => 'Đã khóa điểm thành công. Chờ Đào tạo duyệt.'
    ]);
}
```

---

## 5. DUYỆT ĐIỂM (Đào tạo)

### 📊 Flowchart:

```
[Đào tạo xem danh sách lớp đã khóa điểm]
        ↓
[Kiểm tra bảng điểm]
        ↓
[Phê duyệt/Trả về]
    ↓               ↓
[Trả về]        [Phê duyệt]
    ↓               ↓
[Mở khóa]    [Công bố điểm]
[Gửi thông báo GV]  ↓
                [Gửi thông báo SV]
```

### 🔧 Chi tiết xử lý:

```php
public function duyetDiem(Request $request, $lopHocPhanId) {
    // 1. Validate
    $validated = $request->validate([
        'hanh_dong' => 'required|in:phe_duyet,tra_ve',
        'ly_do_tra_ve' => 'required_if:hanh_dong,tra_ve',
    ]);

    // 2. Kiểm tra trạng thái
    $lopHocPhan = LopHocPhan::find($lopHocPhanId);

    if ($lopHocPhan->trang_thai_lop !== 'da_khoa_diem') {
        return response()->json([
            'success' => false,
            'message' => 'Lớp chưa khóa điểm'
        ], 400);
    }

    DB::transaction(function() use ($lopHocPhanId, $validated) {
        if ($validated['hanh_dong'] === 'phe_duyet') {
            // 3a. Phê duyệt
            LopHocPhan::where('id', $lopHocPhanId)->update([
                'trang_thai_lop' => 'da_duyet_diem',
                'ngay_duyet_diem' => now(),
                'nguoi_duyet_diem_id' => Auth::id(),
            ]);

            // Công bố điểm cho sinh viên
            $this->congBoDiem($lopHocPhanId);

        } else {
            // 3b. Trả về
            LopHocPhan::where('id', $lopHocPhanId)->update([
                'trang_thai_lop' => 'mo_dang_ky', // Mở khóa để GV sửa
                'ly_do_tra_ve' => $validated['ly_do_tra_ve'],
            ]);

            // Gửi thông báo cho GV
            $this->guiThongBaoTraVeDiem($lopHocPhanId, $validated['ly_do_tra_ve']);
        }
    });

    return response()->json([
        'success' => true,
        'message' => $validated['hanh_dong'] === 'phe_duyet'
            ? 'Đã phê duyệt và công bố điểm'
            : 'Đã trả về cho giảng viên'
    ]);
}
```

---

## 6. XEM ĐIỂM (Sinh viên)

### 📊 Flowchart:

```
[Sinh viên vào xem điểm]
        ↓
[Chọn học kỳ]
        ↓
[Lấy tất cả môn đã học trong kỳ]
        ↓
[Với mỗi môn:]
        ↓
[Lấy điểm thành phần (nếu đã công bố)]
        ↓
[Lấy điểm tổng kết]
        ↓
[Tính GPA học kỳ]
        ↓
[Tính GPA tích lũy]
        ↓
[Hiển thị bảng điểm]
```

### 🔧 Chi tiết xử lý:

```php
public function xemDiem($hocKyId = null) {
    $sinhVien = Auth::user()->sinhVien;

    if (!$hocKyId) {
        $hocKyId = HocKy::where('la_hoc_ky_hien_tai', true)->first()->id;
    }

    // 1. Lấy tất cả lớp HP đã học trong kỳ
    $lopHocPhans = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
        ->whereHas('lopHocPhan', function($q) use($hocKyId) {
            $q->where('hoc_ky_id', $hocKyId)
              ->where('trang_thai_lop', 'da_duyet_diem'); // Chỉ hiển thị đã duyệt
        })
        ->with([
            'lopHocPhan.monHoc',
            'ketQuaHocTap',
        ])
        ->get();

    // 2. Tính GPA học kỳ
    $gpaHocKy = $this->tinhGPAHocKy($sinhVien->id, $hocKyId);

    // 3. Tính GPA tích lũy
    $gpaTichLuy = $this->tinhGPATichLuy($sinhVien->id);

    // 4. Tổng tín chỉ đạt
    $tongTinChiDat = $this->tinhTongTinChiDat($sinhVien->id);

    return view('sinhvien.diem.index', compact(
        'lopHocPhans',
        'gpaHocKy',
        'gpaTichLuy',
        'tongTinChiDat'
    ));
}
```

### 🔧 Tính GPA:

```php
private function tinhGPAHocKy($sinhVienId, $hocKyId) {
    $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVienId, $hocKyId) {
            $q->where('sinh_vien_id', $sinhVienId)
              ->whereHas('lopHocPhan', function($q2) use($hocKyId) {
                  $q2->where('hoc_ky_id', $hocKyId);
              });
        })
        ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
        ->get();

    if ($ketQuas->isEmpty()) {
        return 0;
    }

    $tongDiem = 0;
    $tongTinChi = 0;

    foreach ($ketQuas as $kq) {
        $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
        $tongDiem += $kq->diem_he_4 * $tinChi;
        $tongTinChi += $tinChi;
    }

    return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
}

private function tinhGPATichLuy($sinhVienId) {
    $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId);
        })
        ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
        ->get();

    if ($ketQuas->isEmpty()) {
        return 0;
    }

    $tongDiem = 0;
    $tongTinChi = 0;

    foreach ($ketQuas as $kq) {
        $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
        $tongDiem += $kq->diem_he_4 * $tinChi;
        $tongTinChi += $tinChi;
    }

    return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
}

private function tinhTongTinChiDat($sinhVienId) {
    return KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId);
        })
        ->where('qua_mon', true)
        ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
        ->get()
        ->sum(function($kq) {
            return $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
        });
}
```

---

## 7. XUẤT BẢNG ĐIỂM (PDF)

### 🔧 Chi tiết xử lý:

```php
public function xuatBangDiem($hocKyId) {
    $sinhVien = Auth::user()->sinhVien;

    // Lấy dữ liệu
    $data = $this->layDuLieuBangDiem($sinhVien->id, $hocKyId);

    // Generate PDF
    $pdf = PDF::loadView('sinhvien.diem.pdf', $data);

    return $pdf->download('bang-diem-hoc-ky-' . $hocKyId . '.pdf');
}
```

---

## 📊 THỐNG KÊ & BÁO CÁO

### Thống kê tiến độ nhập điểm (Đào tạo):

```php
public function tienDoNhapDiem($hocKyId) {
    $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKyId)->get();

    $thongKe = [];

    foreach ($lopHocPhans as $lop) {
        $tongSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
            ->where('trang_thai', 'dang_hoc')
            ->count();

        $svCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use($lop) {
                $q->where('lop_hoc_phan_id', $lop->id);
            })
            ->whereNotNull('diem_he_10')
            ->count();

        $thongKe[] = [
            'lop' => $lop->ma_lop_hp,
            'mon_hoc' => $lop->monHoc->ten_mon,
            'tong_sv' => $tongSV,
            'sv_co_diem' => $svCoDiem,
            'ty_le' => $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0,
            'trang_thai' => $lop->trang_thai_lop,
        ];
    }

    return $thongKe;
}
```

---

**Ngày tạo:** 27/10/2025  
**Phase:** Phase 7 - Nhập điểm & Đánh giá  
**Trạng thái:** ⏳ Chưa triển khai (theo lộ trình)
