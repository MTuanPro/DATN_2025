# CHỨC NĂNG ĐÀO TẠO (Trưởng phòng & Nhân viên)

**Phạm vi:** Toàn bộ nghiệp vụ đào tạo, kế hoạch lớp/môn, xếp lớp, lịch dạy/thi, học phí, cảnh báo, truyền thông liên quan đào tạo  
**Phân quyền nội bộ:** Trưởng phòng có quyền phê duyệt/chạy batch/cấu hình chính sách; Nhân viên thực hiện nghiệp vụ hàng ngày

---

## 1. QUẢN LÝ DANH MỤC & CẤU TRÚC CTĐT

### 1.1. Quản lý Khoa (khoa)

-   Xem danh sách khoa
-   Thêm khoa mới
-   Sửa thông tin khoa
-   Xóa khoa
-   Gán trưởng khoa (truong_khoa_id)

### 1.2. Quản lý Ngành (nganh)

-   Xem danh sách ngành
-   Thêm ngành mới
-   Sửa thông tin ngành
-   Xóa ngành
-   Gán ngành vào khoa (khoa_id)

### 1.3. Quản lý Chuyên ngành (chuyen_nganh)

-   Xem danh sách chuyên ngành
-   Thêm chuyên ngành mới
-   Sửa thông tin chuyên ngành
-   Xóa chuyên ngành
-   Gán chuyên ngành vào ngành (nganh_id)
-   Thiết lập tổng tín chỉ tối thiểu (tong_tin_chi_toi_thieu)

### 1.4. Quản lý Trình độ (dm_trinh_do)

-   Xem danh sách trình độ
-   Thêm trình độ mới
-   Sửa trình độ
-   Xóa trình độ

### 1.5. Quản lý Môn học (mon_hoc)

-   Xem danh sách môn học
-   Thêm môn học mới
-   Sửa thông tin môn học
-   Xóa môn học (soft delete)
-   Thiết lập số tín chỉ (so_tin_chi, so_tin_chi_ly_thuyet, so_tin_chi_thuc_hanh)
-   Phân loại môn học (loai_mon: dai_cuong, co_so_nganh, chuyen_nganh_bat_buoc, chuyen_nganh_tu_chon, thuc_tap, do_an_tot_nghiep)
-   Gán khoa quản lý (khoa_id)
-   Thiết lập hình thức dạy (hinh_thuc_day: offline, online, hybrid)
-   Thiết lập thời lượng học, số buổi học
-   Import môn học từ Excel
-   Xuất danh sách môn học

### 1.6. Quản lý Môn học tiên quyết (mon_hoc_tien_quyet)

-   Xem môn tiên quyết của môn học
-   Thêm môn tiên quyết
-   Sửa thông tin môn tiên quyết (loai_tien_quyet, dieu_kien_qua_mon)
-   Xóa môn tiên quyết

### 1.7. Quản lý Chương trình khung (chuong_trinh_khung)

-   Xem chương trình khung theo chuyên ngành
-   Thêm môn học vào chương trình khung
-   Sửa thông tin môn trong chương trình (hoc_ky_goi_y, thu_tu_hoc, bat_buoc)
-   Xóa môn khỏi chương trình khung
-   Sắp xếp thứ tự môn học
-   Thiết lập môn bắt buộc/tự chọn
-   Import chương trình khung
-   Xuất chương trình khung

### 1.8. Quản lý Trạng thái học tập (trang_thai_hoc_tap)

-   Xem danh sách trạng thái (Đang học, Bảo lưu, Thôi học, Tốt nghiệp)
-   Thêm trạng thái mới
-   Sửa trạng thái
-   Xóa trạng thái

### 1.9. Quản lý Phòng học (phong_hoc)

-   Xem danh sách phòng học
-   Thêm phòng học mới
-   Sửa thông tin phòng học (sức chứa, vị trí, loại phòng)
-   Xóa phòng học
-   Thay đổi trạng thái phòng học (Hoạt động, Bảo trì, Không sử dụng)

---

## 2. QUẢN LÝ NIÊN KHÓA & HỌC KỲ

### 2.1. Quản lý Khóa học (khoa_hoc)

-   Xem danh sách khóa học (K17, K18, ...)
-   Thêm khóa học mới
-   Sửa thông tin khóa học (năm bắt đầu/kết thúc, số năm đào tạo)
-   Xóa khóa học

### 2.2. Quản lý Học kỳ (hoc_ky)

-   Xem danh sách học kỳ
-   Thêm học kỳ mới (HK1, HK2, HK3-Hè)
-   Sửa thông tin học kỳ
-   Xóa học kỳ
-   Thiết lập thời gian học kỳ (ngay_bat_dau, ngay_ket_thuc)
-   Thiết lập thời gian đăng ký (ngay_bat_dau_dang_ky, ngay_ket_thuc_dang_ky)
-   Đặt học kỳ hiện tại (la_hoc_ky_hien_tai)

---

## 3. QUẢN LÝ LỚP HÀNH CHÍNH & HỒ SƠ SINH VIÊN

### 3.1. Quản lý Lớp hành chính (lop_hanh_chinh)

-   Xem danh sách lớp hành chính
-   Thêm lớp hành chính mới
-   Sửa thông tin lớp hành chính
-   Xóa lớp hành chính (soft delete)
-   Gán giảng viên chủ nhiệm (giang_vien_chu_nhiem_id)
-   Gán khóa học, ngành (khoa_hoc_id, nganh_id)
-   Xem danh sách sinh viên trong lớp
-   Cập nhật sĩ số (si_so)
-   Chuyển sinh viên sang lớp khác
-   Thêm sinh viên vào lớp
-   Xóa sinh viên khỏi lớp

### 3.2. Quản lý Sinh viên (sinh_vien)

-   Xem danh sách sinh viên
-   Thêm sinh viên mới
-   Sửa thông tin sinh viên (hồ sơ cá nhân, địa chỉ, CCCD)
-   Xóa sinh viên (soft delete)
-   Gán/thay đổi lớp hành chính (lop_hanh_chinh_id)
-   Gán/thay đổi ngành, chuyên ngành (nganh_id, chuyen_nganh_id)
-   Cập nhật trạng thái học tập (trang_thai_hoc_tap_id)
-   Cập nhật kỳ hiện tại (ky_hien_tai)
-   Gán giảng viên chủ nhiệm (giang_vien_chu_nhiem_id)
-   Tạo/gán tài khoản user (user_id)
-   Import sinh viên từ Excel
-   Xuất danh sách sinh viên
-   Tìm kiếm, lọc sinh viên theo khoa/ngành/lớp/khóa

---

## 4. QUẢN LÝ GIẢNG VIÊN

### 4.1. Quản lý Giảng viên (giang_vien)

-   Xem danh sách giảng viên
-   Thêm giảng viên mới
-   Sửa thông tin giảng viên (hồ sơ, trình độ, chuyên môn)
-   Xóa giảng viên (soft delete)
-   Gán khoa (khoa_id)
-   Gán trình độ (trinh_do_id)
-   Thiết lập ngày vào trường (ngay_vao_truong)
-   Tạo/gán tài khoản user (user_id)
-   Import giảng viên từ Excel
-   Xuất danh sách giảng viên
-   Tìm kiếm, lọc giảng viên theo khoa/trình độ

---

## 5. QUẢN LÝ LỚP HỌC PHẦN & PHÂN CÔNG

### 5.1. Quản lý Lớp học phần (lop_hoc_phan)

-   Xem danh sách lớp học phần
-   Thêm lớp học phần mới (theo môn học, học kỳ, nhóm lớp)
-   Sửa thông tin lớp học phần
-   Xóa lớp học phần (soft delete)
-   Thiết lập sức chứa, số lượng tối thiểu (suc_chua, so_luong_toi_thieu)
-   Thiết lập hình thức (hinh_thuc: offline, online, hybrid)
-   Thiết lập thời gian (ngay_bat_dau, ngay_ket_thuc)
-   Thay đổi trạng thái lớp (trang_thai_lop: mo_dang_ky, dang_hoc, ket_thuc, huy)
-   Xem danh sách sinh viên trong lớp (lop_hoc_phan_sinh_vien)
-   Cập nhật số lượng đăng ký (so_luong_dang_ky)

### 5.2. Phân công Giảng viên (lop_hoc_phan_giang_vien)

-   Xem danh sách phân công giảng viên
-   Phân công giảng viên dạy lớp học phần
-   Sửa phân công (vai trò, phần công giảng dạy)
-   Xóa phân công giảng viên
-   Thiết lập vai trò (vai_tro: giang_vien_chinh, giang_vien_phu, tro_giang)
-   Ghi nhận người phân công (nguoi_phan_cong_id)

---

## 6. QUẢN LÝ LỊCH DẠY & LỊCH CHI TIẾT

### 6.1. Lịch học cố định - Thời khóa biểu (lich_hoc_co_dinh)

-   Xem thời khóa biểu
-   Thêm lịch học cố định theo tuần
-   Sửa lịch học cố định
-   Xóa lịch học cố định
-   Thiết lập thứ, tiết, giờ học (thu_trong_tuan, tiet_bat_dau, tiet_ket_thuc, gio_bat_dau, gio_ket_thuc)
-   Gán phòng học (phong_hoc_id)
-   Gán giảng viên (giang_vien_id)
-   Kiểm tra trùng lịch phòng (unique: thu_trong_tuan, tiet_bat_dau, phong_hoc_id)
-   Kiểm tra trùng lịch giảng viên
-   Tự động sinh lịch chi tiết từ lịch cố định
-   Xuất thời khóa biểu

### 6.2. Lịch học chi tiết (lich_hoc_chi_tiet)

-   Xem lịch học chi tiết từng buổi
-   Thêm buổi học (bù học, học bổ sung)
-   Sửa buổi học
-   Hủy buổi học (trang_thai: huy)
-   Thay đổi phòng học
-   Thay đổi giảng viên
-   Thay đổi thời gian
-   Cập nhật nội dung giảng dạy (noi_dung_giang_day)
-   Đính kèm tài liệu (tai_lieu_dinh_kem)
-   Cập nhật trạng thái (trang_thai: chua_day, dang_day, da_day, huy)
-   Kiểm tra trùng phòng (unique: ngay_hoc, tiet_bat_dau, phong_hoc_id)
-   Xuất lịch học

---

## 7. QUẢN LÝ LỊCH THI & COI THI

### 7.1. Lịch thi (lich_thi)

-   Xem danh sách lịch thi
-   Thêm lịch thi mới
-   Sửa lịch thi
-   Xóa lịch thi
-   Thiết lập loại thi (loai_thi: Giữa kỳ, Cuối kỳ, Thi lại)
-   Thiết lập thời gian thi (ngay_thi, gio_bat_dau, gio_ket_thuc)
-   Gán phòng thi (phong_thi_id)
-   Phân công giám thị (giam_thi_1_id, giam_thi_2_id)
-   Cập nhật số sinh viên dự thi (so_sinh_vien_du_thi)
-   Upload đề thi, đáp án (de_thi_file, dap_an_file)
-   Gửi thông báo lịch thi (ngay_gui_lich, nguoi_gui_id)
-   Kiểm tra trùng phòng thi (unique: ngay_thi, gio_bat_dau, phong_thi_id)
-   Xuất lịch thi

---

## 8. QUẢN LÝ ĐĂNG KÝ & XẾP LỚP

### 8.1. Đăng ký môn học tạm (dang_ky_mon_hoc_tam)

-   Mở/Đóng đăng ký môn học (thiết lập thời gian trong hoc_ky)
-   Xem danh sách đăng ký tạm
-   Xem ưu tiên sinh viên (uu_tien)
-   Kiểm tra điều kiện đăng ký (môn tiên quyết, tín chỉ tối đa)
-   Duyệt/Hủy đăng ký thủ công
-   Xem trạng thái (trang_thai: cho_xep_lop, da_xep_lop, that_bai)
-   Xem lý do thất bại (ly_do_that_bai)

### 8.2. Quy tắc xếp lớp (quy_tac_xep_lop)

-   Xem danh sách quy tắc
-   Thêm quy tắc mới
-   Sửa quy tắc
-   Xóa quy tắc
-   Thiết lập thứ tự ưu tiên (thu_tu_uu_tien)
-   Kích hoạt/Vô hiệu hóa quy tắc (kich_hoat)

### 8.3. Xếp lớp tự động

-   Chạy thuật toán xếp lớp tự động
-   Áp dụng quy tắc xếp lớp (quy_tac_xep_lop)
-   Tạo lop_hoc_phan_sinh_vien từ dang_ky_mon_hoc_tam
-   Ghi nhận lich_su_xep_lop
-   Xem kết quả xếp lớp (số SV xếp thành công/thất bại)
-   Xử lý trường hợp xếp lớp thất bại
-   Xếp lớp thủ công (phuong_thuc_xep: thu_cong)

### 8.4. Lớp học phần - Sinh viên (lop_hoc_phan_sinh_vien)

-   Xem danh sách sinh viên trong lớp HP
-   Thêm sinh viên vào lớp (thủ công)
-   Xóa sinh viên khỏi lớp
-   Cập nhật trạng thái (trang_thai: da_xep_lop, dang_hoc, da_hoan_thanh, bo_hoc, huy_dang_ky)
-   Xem phương thức xếp (phuong_thuc_xep: tu_dong, thu_cong)
-   Xem người duyệt (nguoi_duyet_id, ngay_duyet)

### 8.5. Lịch sử xếp lớp (lich_su_xep_lop)

-   Xem lịch sử các lần chạy xếp lớp
-   Xem thống kê xếp lớp (số SV cần xếp, thành công, thất bại)
-   Xem thời gian xử lý
-   Xem người chạy (nguoi_chay_id)

---

## 9. QUẢN LÝ CẤU HÌNH ĐIỂM (Cấp quản trị)

### 9.1. Cấu hình đầu điểm (cau_hinh_dau_diem)

-   Xem cấu hình đầu điểm của lớp HP
-   Thiết lập cấu hình đầu điểm (Chuyên cần, Giữa kỳ, Cuối kỳ, Thực hành, Tiểu luận)
-   Sửa cấu hình đầu điểm
-   Xóa cấu hình đầu điểm
-   Thiết lập tỷ lệ % (ty_le - đảm bảo tổng = 100%)
-   Thiết lập số cột điểm (so_cot)
-   Kiểm tra ràng buộc tổng tỷ lệ = 100%

### 9.2. Kiểm soát nhập điểm

-   Khóa/Mở thời gian nhập điểm
-   Chuẩn hóa tính toán ket_qua_hoc_tap
-   Xem tiến độ nhập điểm của giảng viên

---

## 10. QUẢN LÝ HỌC PHÍ

### 10.1. Cấu hình học phí (cau_hinh_hoc_phi)

-   Xem danh sách cấu hình học phí
-   Thêm cấu hình học phí theo năm học
-   Sửa cấu hình học phí
-   Xóa cấu hình học phí
-   Thiết lập đơn giá tín chỉ (don_gia_tren_tin_chi)
-   Thiết lập phí dịch vụ (phi_dich_vu)
-   Thiết lập thời gian áp dụng (ap_dung_tu_ngay, ap_dung_den_ngay)

### 10.2. Học phí học kỳ (hoc_phi_hoc_ky)

-   Xem tổng hợp học phí theo sinh viên
-   Tính toán học phí học kỳ (tong_hoc_phi_mon_hoc, phi_dich_vu, tong_so_tien)
-   Thiết lập hạn đóng (han_dong)
-   Cập nhật trạng thái học phí (trang_thai: chua_nop, da_nop_mot_phan, da_nop_du, qua_han)
-   Xem số tiền còn lại (so_tien_con_lai = tong_so_tien - so_tien_da_dong)
-   Xuất danh sách nợ học phí

### 10.3. Chi tiết học phí môn (chi_tiet_hoc_phi_mon)

-   Xem chi tiết học phí theo môn đăng ký
-   Tính thanh tiền (thanh_tien = so_tin_chi \* don_gia_tin_chi)
-   Cập nhật trạng thái thanh toán (trang_thai: chua_thanh_toan, da_thanh_toan, huy)

### 10.4. Lịch sử đóng học phí (lich_su_dong_hoc_phi)

-   Xem lịch sử đóng học phí
-   Ghi nhận giao dịch đóng học phí
-   Xác nhận thanh toán (phương thức, ngân hàng, mã giao dịch)
-   Upload biên lai (bien_lai_file)
-   Ghi nhận người thu (nguoi_thu_id)
-   Cập nhật so_tien_da_dong trong hoc_phi_hoc_ky

### 10.5. Thông báo & Báo cáo học phí

-   Gửi thông báo nhắc nợ học phí
-   Gửi thông báo quá hạn
-   Xuất báo cáo thu học phí theo kỳ/khoa/ngành
-   Xuất danh sách sinh viên nợ học phí

---

## 11. QUẢN LÝ CẢNH BÁO HỌC VỤ

### 11.1. Cảnh báo học vụ (canh_bao_hoc_vu)

-   Xem danh sách cảnh báo
-   Thêm cảnh báo mới (thủ công hoặc tự động)
-   Sửa cảnh báo
-   Xóa cảnh báo
-   Phân loại cảnh báo (loai_canh_bao: diem_thap, vang_nhieu, no_hoc_phi, hoc_ky_lien_tiep)
-   Thiết lập mức độ (muc_do: canh_cao, dinh_chi, buoc_thoi_hoc)
-   Ghi nhận lý do (ly_do: GPA < 1.0, vắng > 20%, nợ học phí quá hạn)
-   Ghi nhận người cảnh báo (nguoi_canh_bao_id)
-   Xử lý cảnh báo (da_xu_ly, ngay_xu_ly, ket_qua_xu_ly)
-   Gửi thông báo cảnh báo cho sinh viên
-   Xuất danh sách sinh viên bị cảnh báo

---

## 12. QUẢN LÝ THÔNG BÁO ĐÀO TẠO

### 12.1. Gửi thông báo (thong_bao)

-   Tạo thông báo liên quan đào tạo (lich_hoc, lich_thi, hoc_phi, diem, dang_ky_mon)
-   Gửi theo đối tượng (lop_hanh_chinh, lop_hoc_phan, khoa, nganh)
-   Sử dụng mẫu thông báo tự động (mau_thong_bao_tu_dong)
-   Thiết lập liên kết (lien_ket_id, lien_ket_loai: lich_hoc, lich_thi, hoc_phi, diem)

---

## 13. BÁO CÁO & DASHBOARD ĐÀO TẠO

### 13.1. Thống kê sinh viên

-   Thống kê sĩ số theo khoa/ngành/lớp/khóa
-   Thống kê trạng thái học tập
-   Thống kê tiến độ CTĐT

### 13.2. Thống kê đăng ký & xếp lớp

-   Thống kê đăng ký môn học theo học kỳ
-   Thống kê tỷ lệ xếp lớp thành công/thất bại
-   Thống kê lớp học phần (sĩ số, tỷ lệ đầy/thiếu)

### 13.3. Thống kê giảng viên

-   Thống kê tải giảng viên
-   Thống kê trùng lịch giảng viên
-   Thống kê phân công giảng dạy

### 13.4. Thống kê phòng học

-   Thống kê sử dụng phòng học
-   Phát hiện trùng lịch phòng
-   Thống kê tỷ lệ sử dụng phòng

### 13.5. Thống kê kết quả học tập

-   Thống kê điểm danh (tỷ lệ vắng theo lớp/môn)
-   Thống kê tỷ lệ qua môn
-   Thống kê phân bố điểm
-   Thống kê GPA trung bình

### 13.6. Thống kê học phí

-   Thống kê thu học phí theo kỳ
-   Thống kê nợ học phí
-   Thống kê tỷ lệ đóng học phí

### 13.7. Thống kê cảnh báo

-   Thống kê số lượng cảnh báo theo loại/mức độ
-   Thống kê sinh viên bị cảnh báo

### 13.8. Xuất báo cáo

-   Xuất tất cả báo cáo Excel/PDF
