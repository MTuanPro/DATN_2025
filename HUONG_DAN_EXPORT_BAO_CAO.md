# Hướng dẫn sử dụng Export Báo cáo

## Tổng quan

Hệ thống hỗ trợ xuất báo cáo dưới 2 định dạng:
- **Excel** (.xlsx) - Dùng PhpOffice\PhpSpreadsheet
- **PDF** (.pdf) - Dùng Barryvdh\DomPDF

Cả Đào tạo và Giảng viên đều có chức năng export với filter đầy đủ.

---

## Đào tạo - Export Báo cáo

### Routes
```
GET /dao-tao/bao-cao/export-excel
GET /dao-tao/bao-cao/export-pdf
```

### Các loại báo cáo hỗ trợ
1. **sinh-vien** - Báo cáo sinh viên
2. **ket-qua** - Báo cáo kết quả học tập
3. **diem-danh** - Báo cáo điểm danh
4. **hoc-phi** - Báo cáo học phí
5. **dang-ky** - Báo cáo đăng ký môn học
6. **xep-lop** - Báo cáo xếp lớp
7. **tai-giang-vien** - Báo cáo tải giảng viên
8. **phong-hoc** - Báo cáo sử dụng phòng học
9. **canh-bao** - Báo cáo cảnh báo học vụ

### Filters hỗ trợ

#### Filter theo Khoa/Ngành (sinh-vien)
- `khoa_id` - ID khoa
- `nganh_id` - ID ngành
- `khoa_hoc_id` - ID khóa học
- `lop` - Tên lớp hành chính

#### Filter theo Thời gian (diem-danh, ket-qua, hoc-phi)
- `hoc_ky_id` - ID học kỳ
- `tu_ngay` - Từ ngày (YYYY-MM-DD)
- `den_ngay` - Đến ngày (YYYY-MM-DD)

### Cách sử dụng trong View

#### 1. Thêm script vào layout
```blade
@push('scripts')
    <script src="{{ asset('assets/js/export-report.js') }}"></script>
@endpush
```

#### 2. Thêm id và data-report-type vào form
```blade
<form method="GET" action="..." id="filterForm" data-report-type="sinh-vien">
    <!-- Filters here -->
</form>
```

#### 3. Sử dụng component export-buttons
```blade
<x-export-buttons report-type="sinh-vien" />
```

Hoặc viết tay:
```blade
<div class="btn-group">
    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-download me-1"></i> Xuất báo cáo
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="#" onclick="exportReport(event, 'excel')">
                <i class="bi bi-file-earmark-excel text-success me-2"></i>
                Xuất Excel
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="exportReport(event, 'pdf')">
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                Xuất PDF
            </a>
        </li>
    </ul>
</div>
```

---

## Giảng viên - Export Báo cáo

### Routes
```
GET /giang-vien/bao-cao/export-excel
GET /giang-vien/bao-cao/export-pdf
```

### Các loại báo cáo
1. **tien-do** - Tiến độ giảng dạy
2. **diem-danh** - Báo cáo điểm danh
3. **phan-tich-diem** - Phân tích điểm

### Filters hỗ trợ
- `hoc_ky_id` - ID học kỳ
- `lop_hoc_phan_id` - ID lớp học phần

### Ví dụ sử dụng
```blade
@extends('layouts.layout-giangvien')

@push('scripts')
    <script src="{{ asset('assets/js/export-report.js') }}"></script>
@endpush

@section('content')
    <form id="filterForm" data-report-type="tien-do">
        <!-- Filters -->
        <select name="hoc_ky_id">...</select>
        <select name="lop_hoc_phan_id">...</select>
        
        <button type="submit">Lọc</button>
        <x-export-buttons />
    </form>
@endsection
```

---

## Export Functions (JavaScript)

### exportReport(event, type)
Xuất báo cáo với filters từ form

**Parameters:**
- `event` - Event object (để preventDefault)
- `type` - 'excel' hoặc 'pdf'

**Example:**
```html
<a onclick="exportReport(event, 'excel')">Xuất Excel</a>
```

### exportReportWithFilters(event, type, customFilters)
Xuất báo cáo với filters tùy chỉnh

**Parameters:**
- `event` - Event object
- `type` - 'excel' hoặc 'pdf'
- `customFilters` - Object chứa filters bổ sung

**Example:**
```javascript
exportReportWithFilters(event, 'pdf', {
    khoa_id: 5,
    tu_ngay: '2024-01-01',
    den_ngay: '2024-12-31'
});
```

---

## Controller Methods

### DaoTao\BaoCaoController

#### exportExcel(Request $request)
Xuất file Excel với PhpSpreadsheet

**Query Parameters:**
- `loai` (required) - Loại báo cáo
- `khoa_id` (optional) - Filter khoa
- `nganh_id` (optional) - Filter ngành
- `hoc_ky_id` (optional) - Filter học kỳ
- `tu_ngay` (optional) - Filter từ ngày
- `den_ngay` (optional) - Filter đến ngày

**Response:**
- File Excel download (.xlsx)
- Filename format: `bao_cao_{loai}_{timestamp}.xlsx`

#### exportPdf(Request $request)
Xuất file PDF với DomPDF

**Query Parameters:** Giống exportExcel

**Response:**
- File PDF download
- Filename format: `bao_cao_{loai}_{timestamp}.pdf`

---

## File Structure

```
app/Http/Controllers/
├── DaoTao/
│   └── BaoCaoController.php    (+ exportExcel, exportPdf)
└── GiangVien/
    └── BaoCaoController.php    (đã có exportExcel, exportPdf)

public/assets/js/
└── export-report.js            (Export functions)

resources/views/
├── components/
│   └── export-buttons.blade.php    (Component nút export)
├── daotao/bao-cao/
│   ├── sinh-vien.blade.php     (✅ Đã cập nhật)
│   ├── ket-qua.blade.php       (Cần cập nhật)
│   ├── diem-danh.blade.php     (Cần cập nhật)
│   ├── hoc-phi.blade.php       (Cần cập nhật)
│   ├── canh-bao.blade.php      (Cần cập nhật)
│   └── pdf.blade.php           (✅ Template PDF)
└── giangvien/bao-cao/
    ├── tien-do.blade.php       (Đã có)
    ├── diem-danh.blade.php     (Đã có)
    ├── phan-tich-diem.blade.php (Đã có)
    └── pdf.blade.php           (Đã có)

routes/
└── web.php
    ├── dao-tao.bao-cao.export-excel    ✅
    ├── dao-tao.bao-cao.export-pdf      ✅
    ├── giangvien.bao-cao.export-excel  ✅
    └── giangvien.bao-cao.export-pdf    ✅
```

---

## Cách cập nhật các view còn lại

### Bước 1: Thêm script
```blade
@push('scripts')
    <script src="{{ asset('assets/js/export-report.js') }}"></script>
@endpush
```

### Bước 2: Thêm attributes vào form
```blade
<form id="filterForm" data-report-type="ket-qua">
```

### Bước 3: Thay nút export cũ
```blade
<!-- Xóa -->
<button type="button" class="btn btn-success">
    <i class="bi bi-file-excel"></i> Xuất Excel
</button>

<!-- Thay bằng -->
<x-export-buttons report-type="ket-qua" />
```

---

## Testing

### Test Export Excel
```bash
# Đào tạo
curl "http://localhost:8000/dao-tao/bao-cao/export-excel?loai=sinh-vien&khoa_id=1"

# Giảng viên
curl "http://localhost:8000/giang-vien/bao-cao/export-excel?loai=tien-do&hoc_ky_id=2"
```

### Test Export PDF
```bash
# Đào tạo
curl "http://localhost:8000/dao-tao/bao-cao/export-pdf?loai=diem-danh&tu_ngay=2024-01-01&den_ngay=2024-12-31"

# Giảng viên  
curl "http://localhost:8000/giang-vien/bao-cao/export-pdf?loai=diem-danh&lop_hoc_phan_id=5"
```

---

## Troubleshooting

### Lỗi: "Form with id='filterForm' not found"
**Giải pháp:** Thêm `id="filterForm"` vào thẻ `<form>`

### Lỗi: "Unknown route context"
**Giải pháp:** Kiểm tra URL có chứa `/dao-tao/` hoặc `/giang-vien/`

### File Excel không tải về
**Giải pháp:** 
- Kiểm tra PhpSpreadsheet đã cài: `composer show phpoffice/phpspreadsheet`
- Kiểm tra log: `storage/logs/laravel.log`

### File PDF bị lỗi font tiếng Việt
**Giải pháp:** PDF template đã dùng font `DejaVu Sans` hỗ trợ Unicode

---

## Next Steps

✅ Đã hoàn thành:
- DaoTao\BaoCaoController: exportExcel(), exportPdf()
- GiangVien\BaoCaoController: exportExcel(), exportPdf() (từ trước)
- Routes: 4 routes export
- JavaScript: export-report.js
- Component: export-buttons.blade.php
- PDF Template: daotao/bao-cao/pdf.blade.php
- View example: sinh-vien.blade.php

📝 Cần làm tiếp:
- Cập nhật các view còn lại (ket-qua, diem-danh, hoc-phi, canh-bao...)
- Test với dữ liệu thực
- Optimize query performance cho báo cáo lớn
- Thêm progress indicator khi export
- Cache báo cáo đã tạo (nếu cần)
