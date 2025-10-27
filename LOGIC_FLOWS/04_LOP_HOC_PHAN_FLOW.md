# LOGIC FLOW: QUẢN LÝ LỚP HỌC PHẦN & PHÂN CÔNG

**Phase:** Phase 4 - Lớp học phần & Phân công  
**Actor chính:** Đào tạo  
**Độ ưu tiên:** ⭐⭐⭐⭐ CAO

---

## 📊 TỔNG QUAN QUY TRÌNH

```
[Đào tạo tạo lớp học phần]
        ↓
[Phân công giảng viên]
        ↓
[Cấu hình đầu điểm]
        ↓
[Tạo lịch học cố định]
        ↓
[Sinh lịch học chi tiết]
        ↓
[Mở cho sinh viên đăng ký]
```

---

## 1. TẠO LỚP HỌC PHẦN

### 📊 Flowchart:

```
[Đào tạo chọn Môn học & Học kỳ]
        ↓
[Nhập thông tin lớp]
- Mã lớp HP
- Tên lớp HP
- Nhóm lớp
- Sức chứa (10-100)
- Số lượng tối thiểu (5-30)
- Hình thức (offline/online/hybrid)
- Ngày bắt đầu/kết thúc
        ↓
[Validate]
    ↓               ↓
[Invalid]       [Valid]
    ↓               ↓
[Return]    [Insert lop_hoc_phan]
 Error              ↓
            [Tạo cấu hình đầu điểm mặc định]
                    ↓
            [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function taLopHocPhan(Request $request) {
    // 1. Validate
    $validated = $request->validate([
        'mon_hoc_id' => 'required|exists:mon_hoc,id',
        'hoc_ky_id' => 'required|exists:hoc_ky,id',
        'ma_lop_hp' => 'required|unique:lop_hoc_phan,ma_lop_hp',
        'ten_lop_hp' => 'required',
        'nhom_lop' => 'required|integer|min:1',
        'suc_chua' => 'required|integer|min:10|max:100',
        'so_luong_toi_thieu' => 'required|integer|min:5|max:30',
        'hinh_thuc' => 'required|in:offline,online,hybrid',
        'link_online' => 'required_if:hinh_thuc,online,hybrid|url',
        'ngay_bat_dau' => 'required|date',
        'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
    ]);

    // 2. Kiểm tra sức chứa >= số lượng tối thiểu
    if ($validated['suc_chua'] < $validated['so_luong_toi_thieu']) {
        return back()->withErrors([
            'suc_chua' => 'Sức chứa phải >= số lượng tối thiểu'
        ]);
    }

    // 3. Kiểm tra ngày trong khoảng học kỳ
    $hocKy = HocKy::find($validated['hoc_ky_id']);

    if ($validated['ngay_bat_dau'] < $hocKy->ngay_bat_dau ||
        $validated['ngay_ket_thuc'] > $hocKy->ngay_ket_thuc) {
        return back()->withErrors([
            'ngay_bat_dau' => 'Ngày bắt đầu/kết thúc phải nằm trong học kỳ'
        ]);
    }

    // 4. Kiểm tra unique nhóm lớp
    $exists = LopHocPhan::where('mon_hoc_id', $validated['mon_hoc_id'])
        ->where('hoc_ky_id', $validated['hoc_ky_id'])
        ->where('nhom_lop', $validated['nhom_lop'])
        ->exists();

    if ($exists) {
        return back()->withErrors([
            'nhom_lop' => 'Nhóm lớp này đã tồn tại cho môn học trong học kỳ'
        ]);
    }

    // 5. Tạo lớp học phần
    DB::transaction(function() use ($validated) {
        $lopHocPhan = LopHocPhan::create([
            'ma_lop_hp' => $validated['ma_lop_hp'],
            'ten_lop_hp' => $validated['ten_lop_hp'],
            'mon_hoc_id' => $validated['mon_hoc_id'],
            'hoc_ky_id' => $validated['hoc_ky_id'],
            'nhom_lop' => $validated['nhom_lop'],
            'suc_chua' => $validated['suc_chua'],
            'so_luong_dang_ky' => 0,
            'so_luong_toi_thieu' => $validated['so_luong_toi_thieu'],
            'hinh_thuc' => $validated['hinh_thuc'],
            'link_online' => $validated['link_online'] ?? null,
            'ngay_bat_dau' => $validated['ngay_bat_dau'],
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'],
            'trang_thai_lop' => 'mo_dang_ky',
        ]);

        // 6. Tạo cấu hình đầu điểm mặc định
        $this->taoCauHinhDauDiemMacDinh($lopHocPhan->id);

        // 7. Ghi log
        NhatKyHoatDong::create([
            'user_id' => Auth::id(),
            'hanh_dong' => 'CREATE',
            'bang_du_lieu' => 'lop_hoc_phan',
            'ban_ghi_id' => $lopHocPhan->id,
            'ip_address' => request()->ip(),
        ]);
    });

    return redirect()->route('daotao.lop-hoc-phan.index')
        ->with('success', 'Đã tạo lớp học phần thành công');
}
```

### 🔧 Tạo cấu hình đầu điểm mặc định:

```php
private function taoCauHinhDauDiemMacDinh($lopHocPhanId) {
    $cauHinhs = [
        ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
        ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
        ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
    ];

    foreach ($cauHinhs as $ch) {
        CauHinhDauDiem::create([
            'lop_hoc_phan_id' => $lopHocPhanId,
            'ten_dau_diem' => $ch['ten_dau_diem'],
            'ty_le' => $ch['ty_le'],
            'so_cot' => $ch['so_cot'],
        ]);
    }
}
```

### 📋 Các bảng liên quan:

-   **lop_hoc_phan**
-   **cau_hinh_dau_diem**
-   **mon_hoc** (foreign key)
-   **hoc_ky** (foreign key)

---

## 2. PHÂN CÔNG GIẢNG VIÊN

### 📊 Flowchart:

```
[Đào tạo chọn lớp học phần]
        ↓
[Chọn giảng viên]
        ↓
[Chọn vai trò (GV chính/phụ/trợ giảng)]
        ↓
[Kiểm tra giảng viên có trùng lịch?]
    ↓                       ↓
[Trùng lịch]          [Không trùng]
    ↓                       ↓
[Cảnh báo]          [Insert lop_hoc_phan_giang_vien]
[Cho phép bỏ qua?]          ↓
    ↓                 [Gửi thông báo cho GV]
[Insert]                    ↓
                      [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function phanCongGiangVien(Request $request) {
    // 1. Validate
    $validated = $request->validate([
        'lop_hoc_phan_id' => 'required|exists:lop_hoc_phan,id',
        'giang_vien_id' => 'required|exists:giang_vien,id',
        'vai_tro' => 'required|in:giang_vien_chinh,giang_vien_phu,tro_giang',
    ]);

    // 2. Kiểm tra đã phân công chưa
    $exists = LopHocPhanGiangVien::where('lop_hoc_phan_id', $validated['lop_hoc_phan_id'])
        ->where('giang_vien_id', $validated['giang_vien_id'])
        ->exists();

    if ($exists) {
        return back()->withErrors([
            'giang_vien_id' => 'Giảng viên đã được phân công vào lớp này'
        ]);
    }

    // 3. Kiểm tra đã có GV chính chưa (nếu vai trò = giang_vien_chinh)
    if ($validated['vai_tro'] === 'giang_vien_chinh') {
        $daCoGVChinh = LopHocPhanGiangVien::where('lop_hoc_phan_id', $validated['lop_hoc_phan_id'])
            ->where('vai_tro', 'giang_vien_chinh')
            ->exists();

        if ($daCoGVChinh) {
            return back()->withErrors([
                'vai_tro' => 'Lớp đã có giảng viên chính'
            ]);
        }
    }

    // 4. Kiểm tra trùng lịch
    $trungLich = $this->kiemTraGiangVienTrungLich(
        $validated['giang_vien_id'],
        $validated['lop_hoc_phan_id']
    );

    if ($trungLich['co_trung'] && !$request->bo_qua_canh_bao) {
        return back()->withErrors([
            'giang_vien_id' => 'Giảng viên bị trùng lịch: ' . $trungLich['chi_tiet']
        ])->withInput();
    }

    // 5. Phân công
    DB::transaction(function() use ($validated) {
        LopHocPhanGiangVien::create([
            'lop_hoc_phan_id' => $validated['lop_hoc_phan_id'],
            'giang_vien_id' => $validated['giang_vien_id'],
            'vai_tro' => $validated['vai_tro'],
            'ngay_phan_cong' => now(),
            'nguoi_phan_cong_id' => Auth::id(),
        ]);

        // Gửi thông báo cho giảng viên
        $this->guiThongBaoPhanCong($validated['giang_vien_id'], $validated['lop_hoc_phan_id']);
    });

    return redirect()->back()
        ->with('success', 'Đã phân công giảng viên thành công');
}
```

### 🔧 Kiểm tra giảng viên trùng lịch:

```php
private function kiemTraGiangVienTrungLich($giangVienId, $lopHocPhanId) {
    // Lấy lịch học của lớp mới
    $lichMoi = LichHocCoDinh::where('lop_hoc_phan_id', $lopHocPhanId)->get();

    if ($lichMoi->isEmpty()) {
        return ['co_trung' => false];
    }

    // Lấy lớp học kỳ cùng học kỳ mà giảng viên đang dạy
    $lopHocPhan = LopHocPhan::find($lopHocPhanId);

    $cacLopDangDay = LopHocPhanGiangVien::where('giang_vien_id', $giangVienId)
        ->whereHas('lopHocPhan', function($q) use($lopHocPhan) {
            $q->where('hoc_ky_id', $lopHocPhan->hoc_ky_id);
        })
        ->with('lopHocPhan.lichHocCoDinh')
        ->get();

    // Kiểm tra trùng
    foreach ($cacLopDangDay as $lopDay) {
        foreach ($lopDay->lopHocPhan->lichHocCoDinh as $lich1) {
            foreach ($lichMoi as $lich2) {
                if ($lich1->thu_trong_tuan == $lich2->thu_trong_tuan) {
                    // Kiểm tra trùng tiết
                    if ($this->trungTiet($lich1->tiet_bat_dau, $lich1->tiet_ket_thuc,
                                         $lich2->tiet_bat_dau, $lich2->tiet_ket_thuc)) {
                        return [
                            'co_trung' => true,
                            'chi_tiet' => sprintf(
                                'Thứ %d, tiết %d-%d (Lớp %s)',
                                $lich1->thu_trong_tuan,
                                $lich1->tiet_bat_dau,
                                $lich1->tiet_ket_thuc,
                                $lopDay->lopHocPhan->ma_lop_hp
                            )
                        ];
                    }
                }
            }
        }
    }

    return ['co_trung' => false];
}
```

### 📋 Các bảng liên quan:

-   **lop_hoc_phan_giang_vien**
-   **giang_vien** (foreign key)
-   **lop_hoc_phan** (foreign key)
-   **lich_hoc_co_dinh** (kiểm tra trùng lịch)

---

## 3. CẤU HÌNH ĐẦU ĐIỂM

### 📊 Flowchart:

```
[Đào tạo/GV chọn lớp học phần]
        ↓
[Thêm/Sửa/Xóa cấu hình đầu điểm]
        ↓
[Validate tổng tỷ lệ = 100%]
    ↓               ↓
[!= 100%]       [= 100%]
    ↓               ↓
[Return]      [Lưu cấu hình]
 Error              ↓
            [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function capNhatCauHinhDauDiem(Request $request, $lopHocPhanId) {
    // 1. Validate
    $validated = $request->validate([
        'cau_hinhs' => 'required|array',
        'cau_hinhs.*.ten_dau_diem' => 'required|string',
        'cau_hinhs.*.ty_le' => 'required|numeric|min:0|max:100',
        'cau_hinhs.*.so_cot' => 'required|integer|min:1|max:10',
    ]);

    // 2. Kiểm tra tổng tỷ lệ = 100%
    $tongTyLe = array_sum(array_column($validated['cau_hinhs'], 'ty_le'));

    if ($tongTyLe != 100) {
        return back()->withErrors([
            'cau_hinhs' => "Tổng tỷ lệ phải = 100% (hiện tại: {$tongTyLe}%)"
        ]);
    }

    // 3. Cập nhật cấu hình
    DB::transaction(function() use ($lopHocPhanId, $validated) {
        // Xóa cấu hình cũ
        CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->delete();

        // Thêm cấu hình mới
        foreach ($validated['cau_hinhs'] as $ch) {
            CauHinhDauDiem::create([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ten_dau_diem' => $ch['ten_dau_diem'],
                'ty_le' => $ch['ty_le'],
                'so_cot' => $ch['so_cot'],
            ]);
        }
    });

    return redirect()->back()
        ->with('success', 'Đã cập nhật cấu hình đầu điểm');
}
```

### 🔧 Validate khi đã có điểm:

```php
public function kiemTraCoTheSuaCauHinh($lopHocPhanId) {
    // Kiểm tra đã có điểm nhập chưa
    $daCoNhapDiem = NhapDiem::whereHas('cauHinh', function($q) use($lopHocPhanId) {
        $q->where('lop_hoc_phan_id', $lopHocPhanId);
    })->exists();

    if ($daCoNhapDiem) {
        return [
            'co_the_sua' => false,
            'ly_do' => 'Không thể sửa cấu hình vì đã có điểm được nhập'
        ];
    }

    return ['co_the_sua' => true];
}
```

### 📋 Các bảng liên quan:

-   **cau_hinh_dau_diem**
-   **lop_hoc_phan** (foreign key)
-   **nhap_diem** (kiểm tra trước khi sửa)

---

## 4. TẠO LỊCH HỌC CỐ ĐỊNH

### 📊 Flowchart:

```
[Đào tạo chọn lớp học phần]
        ↓
[Thêm buổi học]
- Thứ trong tuần
- Tiết bắt đầu/kết thúc
- Giờ bắt đầu/kết thúc
- Phòng học
- Giảng viên
- Hình thức
        ↓
[Validate]
        ↓
[Kiểm tra trùng phòng?]
    ↓                   ↓
[Trùng]            [Không trùng]
    ↓                   ↓
[Return]        [Kiểm tra trùng lịch GV?]
 Error              ↓                   ↓
                [Trùng]            [Không trùng]
                    ↓                   ↓
                [Cảnh báo]      [Insert lich_hoc_co_dinh]
                [Cho phép               ↓
                 bỏ qua?]       [Sinh lich_hoc_chi_tiet]
                    ↓                   ↓
                [Insert]          [Return success]
```

### 🔧 Chi tiết xử lý:

```php
public function taoLichHocCoDinh(Request $request) {
    // 1. Validate
    $validated = $request->validate([
        'lop_hoc_phan_id' => 'required|exists:lop_hoc_phan,id',
        'thu_trong_tuan' => 'required|integer|min:2|max:8',
        'tiet_bat_dau' => 'required|integer|min:1|max:10',
        'tiet_ket_thuc' => 'required|integer|min:1|max:10|gt:tiet_bat_dau',
        'gio_bat_dau' => 'required|date_format:H:i',
        'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
        'phong_hoc_id' => 'required|exists:phong_hoc,id',
        'giang_vien_id' => 'required|exists:giang_vien,id',
        'hinh_thuc' => 'required|in:offline,online,hybrid',
        'link_online' => 'required_if:hinh_thuc,online,hybrid|url',
    ]);

    // 2. Kiểm tra giảng viên có được phân công không
    $giangVienDuocPhanCong = LopHocPhanGiangVien::where('lop_hoc_phan_id', $validated['lop_hoc_phan_id'])
        ->where('giang_vien_id', $validated['giang_vien_id'])
        ->exists();

    if (!$giangVienDuocPhanCong) {
        return back()->withErrors([
            'giang_vien_id' => 'Giảng viên chưa được phân công vào lớp này'
        ]);
    }

    // 3. Kiểm tra trùng phòng
    $trungPhong = LichHocCoDinh::where('thu_trong_tuan', $validated['thu_trong_tuan'])
        ->where('phong_hoc_id', $validated['phong_hoc_id'])
        ->where(function($q) use($validated) {
            $q->whereBetween('tiet_bat_dau', [$validated['tiet_bat_dau'], $validated['tiet_ket_thuc']-1])
              ->orWhereBetween('tiet_ket_thuc', [$validated['tiet_bat_dau']+1, $validated['tiet_ket_thuc']]);
        })
        ->exists();

    if ($trungPhong) {
        return back()->withErrors([
            'phong_hoc_id' => 'Phòng học bị trùng lịch'
        ]);
    }

    // 4. Kiểm tra trùng lịch giảng viên (cảnh báo)
    $trungLichGV = $this->kiemTraGiangVienTrungLichCoDinh(
        $validated['giang_vien_id'],
        $validated['thu_trong_tuan'],
        $validated['tiet_bat_dau'],
        $validated['tiet_ket_thuc']
    );

    if ($trungLichGV['co_trung'] && !$request->bo_qua_canh_bao) {
        return back()->withErrors([
            'giang_vien_id' => 'Giảng viên bị trùng lịch: ' . $trungLichGV['chi_tiet']
        ])->withInput();
    }

    // 5. Tạo lịch học cố định
    DB::transaction(function() use ($validated) {
        $lichHoc = LichHocCoDinh::create($validated);

        // 6. Sinh lịch học chi tiết
        $this->sinhLichHocChiTiet($lichHoc);
    });

    return redirect()->back()
        ->with('success', 'Đã tạo lịch học cố định');
}
```

### 📋 Các bảng liên quan:

-   **lich_hoc_co_dinh**
-   **lop_hoc_phan** (foreign key)
-   **phong_hoc** (foreign key, kiểm tra trùng)
-   **giang_vien** (foreign key, kiểm tra trùng lịch)

---

## 5. SINH LỊCH HỌC CHI TIẾT

### 📊 Flowchart:

```
[Lấy lich_hoc_co_dinh]
        ↓
[Lấy ngày bắt đầu/kết thúc lớp học phần]
        ↓
[Duyệt từng tuần trong khoảng thời gian]
        ↓
[Tìm ngày học theo thứ trong tuần]
        ↓
[Insert vào lich_hoc_chi_tiet]
        ↓
[Repeat cho tất cả tuần]
```

### 🔧 Chi tiết xử lý:

```php
private function sinhLichHocChiTiet($lichHocCoDinh) {
    $lopHocPhan = $lichHocCoDinh->lopHocPhan;

    // 1. Lấy ngày bắt đầu/kết thúc
    $ngayBatDau = Carbon::parse($lopHocPhan->ngay_bat_dau);
    $ngayKetThuc = Carbon::parse($lopHocPhan->ngay_ket_thuc);

    // 2. Tìm ngày đầu tiên có thứ phù hợp
    $thuTrongTuan = $lichHocCoDinh->thu_trong_tuan;

    $current = $ngayBatDau->copy();

    // Tìm thứ đầu tiên
    while ($current->dayOfWeek != $this->convertThuToCarbon($thuTrongTuan)) {
        $current->addDay();
    }

    // 3. Sinh lịch chi tiết cho từng tuần
    while ($current <= $ngayKetThuc) {
        LichHocChiTiet::create([
            'lich_hoc_co_dinh_id' => $lichHocCoDinh->id,
            'lop_hoc_phan_id' => $lopHocPhan->id,
            'ngay_hoc' => $current->format('Y-m-d'),
            'tiet_bat_dau' => $lichHocCoDinh->tiet_bat_dau,
            'tiet_ket_thuc' => $lichHocCoDinh->tiet_ket_thuc,
            'gio_bat_dau' => $lichHocCoDinh->gio_bat_dau,
            'gio_ket_thuc' => $lichHocCoDinh->gio_ket_thuc,
            'phong_hoc_id' => $lichHocCoDinh->phong_hoc_id,
            'giang_vien_id' => $lichHocCoDinh->giang_vien_id,
            'hinh_thuc' => $lichHocCoDinh->hinh_thuc,
            'link_online' => $lichHocCoDinh->link_online,
            'trang_thai' => 'chua_day',
        ]);

        // Sang tuần sau
        $current->addWeek();
    }
}

private function convertThuToCarbon($thu) {
    // Thứ 2=2, 3=3,...7=7, CN=8
    // Carbon: Monday=1, Sunday=0
    return $thu == 8 ? 0 : $thu - 1;
}
```

### 📋 Các bảng liên quan:

-   **lich_hoc_co_dinh** (nguồn)
-   **lich_hoc_chi_tiet** (đích)
-   **lop_hoc_phan** (lấy ngày bắt đầu/kết thúc)

---

## 6. THỐNG KÊ & BÁO CÁO

### Thống kê lớp học phần:

```php
public function thongKeLopHocPhan($hocKyId) {
    return [
        'tong_lop' => LopHocPhan::where('hoc_ky_id', $hocKyId)->count(),
        'dang_mo_dang_ky' => LopHocPhan::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai_lop', 'mo_dang_ky')->count(),
        'dang_hoc' => LopHocPhan::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai_lop', 'dang_hoc')->count(),
        'da_ket_thuc' => LopHocPhan::where('hoc_ky_id', $hocKyId)
            ->where('trang_thai_lop', 'ket_thuc')->count(),
        'ty_le_day' => LopHocPhan::where('hoc_ky_id', $hocKyId)
            ->selectRaw('AVG(so_luong_dang_ky / suc_chua * 100) as ty_le')
            ->value('ty_le'),
    ];
}
```

### Báo cáo tải giảng viên:

```php
public function taiGiangVien($hocKyId) {
    return GiangVien::withCount(['lopHocPhanGiangVien' => function($q) use($hocKyId) {
            $q->whereHas('lopHocPhan', function($q2) use($hocKyId) {
                $q2->where('hoc_ky_id', $hocKyId);
            });
        }])
        ->having('lop_hoc_phan_giang_vien_count', '>', 0)
        ->get()
        ->map(function($gv) {
            return [
                'giang_vien' => $gv->ho_ten,
                'so_lop' => $gv->lop_hoc_phan_giang_vien_count,
                'khoa' => $gv->khoa->ten_khoa,
            ];
        });
}
```

---

**Ngày tạo:** 27/10/2025  
**Phase:** Phase 4 - Lớp học phần & Phân công  
**Trạng thái:** ⏳ Chưa triển khai (theo lộ trình)
