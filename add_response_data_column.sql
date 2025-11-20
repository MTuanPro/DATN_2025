-- Thêm cột response_data vào bảng lich_su_dong_hoc_phi
ALTER TABLE `lich_su_dong_hoc_phi` 
ADD COLUMN `response_data` JSON NULL COMMENT 'Dữ liệu phản hồi từ cổng thanh toán' 
AFTER `ghi_chu`;
