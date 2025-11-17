# Tích hợp Hệ thống Ca Học vào S-MIS

## Tổng quan

Đã tạo hệ thống quản lý ca học và tích hợp vào các bảng lịch học/lịch thi.

## ✅ Đã hoàn thành

### 1. Migration & Database

-   ✅ Tạo bảng `ca_hoc` với các trường:

    -   `id`: Primary key
    -   `ten_ca`: Tên ca học (VD: Ca 1, Ca 2...)
    -   `thu_tu`: Thứ tự ca học (1-6)
    -   `gio_bat_dau`: Giờ bắt đầu
    -   `gio_ket_thuc`: Giờ kết thúc
    -   `trang_thai`: Trạng thái hoạt động
    -   `ghi_chu`: Ghi chú

-   ✅ Thêm cột `ca_hoc_id` vào các bảng:
    -   `lich_hoc_co_dinh`
    -   `lich_hoc_chi_tiet`
    -   `lich_thi`

### 2. Model & Relationship

-   ✅ Tạo Model `CaHoc` với các phương thức:

    -   `getCaHocHoatDong()`: Lấy danh sách ca học đang hoạt động
    -   `isValidTimeRange()`: Kiểm tra thời gian hợp lệ
    -   `getFormattedTimeRange()`: Format thời gian hiển thị

-   ✅ Cập nhật Model `LichHocCoDinh`:

    -   Thêm `ca_hoc_id` vào fillable
    -   Thêm relationship `caHoc()`

-   ✅ Cập nhật Model `LichHocChiTiet`:

    -   Thêm `ca_hoc_id` vào fillable
    -   Thêm relationship `caHoc()`

-   ✅ Cập nhật Model `LichThi`:
    -   Thêm `ca_hoc_id` vào fillable
    -   Thêm relationship `caHoc()`

### 3. Seeder & Dữ liệu mẫu

-   ✅ Tạo `CaHocSeeder` với 6 ca học mặc định:
    -   Ca 1: 07:00 - 08:50 (Tiết 1,2)
    -   Ca 2: 09:00 - 10:50 (Tiết 3,4)
    -   Ca 3: 11:00 - 12:50 (Tiết 5,6)
    -   Ca 4: 13:00 - 14:50 (Tiết 7,8)
    -   Ca 5: 15:00 - 16:50 (Tiết 9,10)
    -   Ca 6: 17:00 - 18:50 (Tiết 11,12)

### 4. Controller & Routes

-   ✅ Tạo `CaHocController` với đầy đủ CRUD:

    -   `index()`: Danh sách ca học
    -   `create()`: Form thêm ca học
    -   `store()`: Lưu ca học mới
    -   `edit()`: Form chỉnh sửa
    -   `update()`: Cập nhật ca học
    -   `destroy()`: Xóa ca học
    -   `toggleStatus()`: Bật/tắt trạng thái

-   ✅ Thêm routes vào `web.php`:
    ```php
    Route::resource('ca-hoc', \App\Http\Controllers\DaoTao\CaHocController::class);
    Route::post('ca-hoc/{caHoc}/toggle-status', [...]);
    ```

### 5. Views

-   ✅ Tạo view `daotao/ca-hoc/index.blade.php`: Danh sách ca học
-   ✅ Tạo view `daotao/ca-hoc/create.blade.php`: Form thêm mới
-   ✅ Tạo view `daotao/ca-hoc/edit.blade.php`: Form chỉnh sửa
-   ✅ Thêm menu "Quản lý Ca học" vào sidebar đào tạo

## 📝 Cần cập nhật tiếp

### 1. Views - Lịch học cố định (`daotao/lich-hoc-co-dinh/`)

**File cần sửa**: `create.blade.php`, `edit.blade.php`

Thêm dropdown chọn ca học:

```html
<div class="mb-3">
    <label for="ca_hoc_id" class="form-label">Ca học (tùy chọn)</label>
    <select class="form-control" id="ca_hoc_id" name="ca_hoc_id">
        <option value="">-- Chọn ca học hoặc nhập thủ công --</option>
        @foreach(\App\Models\CaHoc::getCaHocHoatDong() as $ca)
            <option value="{{ $ca->id }}"
                    data-gio-bd="{{ date('H:i', strtotime($ca->gio_bat_dau)) }}"
                    data-gio-kt="{{ date('H:i', strtotime($ca->gio_ket_thuc)) }}"
                    {{ old('ca_hoc_id') == $ca->id ? 'selected' : '' }}>
                {{ $ca->ten_ca }} ({{ $ca->getFormattedTimeRange() }})
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">
        Chọn ca học để tự động điền giờ, hoặc bỏ qua để nhập thủ công
    </small>
</div>

<script>
// Tự động điền giờ khi chọn ca học
document.getElementById('ca_hoc_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('gio_bat_dau').value = selectedOption.dataset.gioBd;
        document.getElementById('gio_ket_thuc').value = selectedOption.dataset.gioKt;
    }
});
</script>
```

### 2. Views - Lịch học chi tiết (`daotao/lich-hoc-chi-tiet/`)

**File cần sửa**: `create.blade.php`, `edit.blade.php`

Thêm tương tự như lịch học cố định.

### 3. Views - Lịch thi (`daotao/lich-thi/`)

**File cần sửa**: `create.blade.php`, `edit.blade.php`

Thêm dropdown chọn ca thi:

```html
<div class="mb-3">
    <label for="ca_hoc_id" class="form-label">Ca thi</label>
    <select class="form-control" id="ca_hoc_id" name="ca_hoc_id">
        <option value="">-- Chọn ca thi hoặc nhập thủ công --</option>
        @foreach(\App\Models\CaHoc::getCaHocHoatDong() as $ca)
        <option
            value="{{ $ca->id }}"
            data-gio-bd="{{ date('H:i', strtotime($ca->gio_bat_dau)) }}"
            data-gio-kt="{{ date('H:i', strtotime($ca->gio_ket_thuc)) }}"
        >
            {{ $ca->ten_ca }} ({{ $ca->getFormattedTimeRange() }})
        </option>
        @endforeach
    </select>
</div>
```

### 4. Controllers cần cập nhật

**File cần sửa**:

-   `LichHocCoDinhController.php`
-   `LichHocChiTietController.php`
-   `LichThiController.php`

Thêm logic xử lý ca học trong method `store()` và `update()`:

```php
// Thêm vào validation rules
'ca_hoc_id' => 'nullable|exists:ca_hoc,id',

// Trong phần xử lý data
if ($request->filled('ca_hoc_id')) {
    $caHoc = \App\Models\CaHoc::find($request->ca_hoc_id);
    if ($caHoc) {
        $validated['ca_hoc_id'] = $caHoc->id;
        // Tự động lấy giờ từ ca học nếu không nhập thủ công
        if (!$request->filled('gio_bat_dau')) {
            $validated['gio_bat_dau'] = date('H:i', strtotime($caHoc->gio_bat_dau));
        }
        if (!$request->filled('gio_ket_thuc')) {
            $validated['gio_ket_thuc'] = date('H:i', strtotime($caHoc->gio_ket_thuc));
        }
    }
}
```

### 5. Views - Hiển thị thông tin ca học

Trong các view danh sách (index), thêm cột hiển thị ca học:

```php
@if($lichHoc->caHoc)
    <span class="badge bg-info">{{ $lichHoc->caHoc->ten_ca }}</span>
    <br>
    <small>{{ $lichHoc->caHoc->getFormattedTimeRange() }}</small>
@else
    {{ date('H:i', strtotime($lichHoc->gio_bat_dau)) }} -
    {{ date('H:i', strtotime($lichHoc->gio_ket_thuc)) }}
@endif
```

## 🎯 Lợi ích của hệ thống Ca học

1. **Chuẩn hóa thời gian**: Tất cả lịch học/lịch thi đều theo chuẩn ca học của trường
2. **Dễ quản lý**: Thay đổi thời gian ca học ở một nơi, tự động áp dụng toàn hệ thống
3. **Giảm lỗi nhập liệu**: Không cần nhập thủ công giờ bắt đầu/kết thúc
4. **Linh hoạt**: Vẫn cho phép nhập thủ công nếu cần (ca_hoc_id nullable)
5. **Mở rộng**: Dễ dàng thêm ca học mới hoặc điều chỉnh thời gian

## 📊 Cấu trúc Database

```
ca_hoc (Bảng chính)
├── id
├── ten_ca (Ca 1, Ca 2...)
├── thu_tu (1-6)
├── gio_bat_dau (07:00, 09:00...)
├── gio_ket_thuc (08:50, 10:50...)
├── trang_thai (active/inactive)
└── ghi_chu

lich_hoc_co_dinh
├── ...existing fields...
└── ca_hoc_id (nullable, FK → ca_hoc)

lich_hoc_chi_tiet
├── ...existing fields...
└── ca_hoc_id (nullable, FK → ca_hoc)

lich_thi
├── ...existing fields...
└── ca_hoc_id (nullable, FK → ca_hoc)
```

## 🔗 Routes

-   `GET /dao-tao/ca-hoc` - Danh sách ca học
-   `GET /dao-tao/ca-hoc/create` - Form thêm ca học
-   `POST /dao-tao/ca-hoc` - Lưu ca học mới
-   `GET /dao-tao/ca-hoc/{id}/edit` - Form sửa ca học
-   `PUT /dao-tao/ca-hoc/{id}` - Cập nhật ca học
-   `DELETE /dao-tao/ca-hoc/{id}` - Xóa ca học
-   `POST /dao-tao/ca-hoc/{id}/toggle-status` - Bật/tắt trạng thái

## 💡 Ghi chú

-   Trường `ca_hoc_id` được để **nullable** để đảm bảo tương thích ngược
-   Có thể vẫn nhập thủ công giờ nếu không chọn ca học
-   Khi chọn ca học, giờ tự động điền nhưng vẫn có thể chỉnh sửa
-   Seeder đã tạo sẵn 6 ca học tiêu chuẩn, có thể thêm/sửa qua giao diện quản lý
