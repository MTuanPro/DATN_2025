# Script tạo tất cả migrations cho S-MIS21

# Đăng ký môn học
php artisan make:migration create_dang_ky_mon_hoc_tam_table
php artisan make:migration create_lop_hoc_phan_sinh_vien_table
php artisan make:migration create_lich_su_xep_lop_table
php artisan make:migration create_quy_tac_xep_lop_table

# Lịch học và thời khóa biểu
php artisan make:migration create_lich_hoc_co_dinh_table
php artisan make:migration create_lich_hoc_chi_tiet_table
php artisan make:migration create_lich_thi_table

# Điểm và đánh giá
php artisan make:migration create_cau_hinh_dau_diem_table
php artisan make:migration create_nhap_diem_table
php artisan make:migration create_ket_qua_hoc_tap_table
php artisan make:migration create_bang_diem_table
php artisan make:migration create_diem_danh_table

# Học phí
php artisan make:migration create_cau_hinh_hoc_phi_table
php artisan make:migration create_hoc_phi_hoc_ky_table
php artisan make:migration create_chi_tiet_hoc_phi_mon_table
php artisan make:migration create_lich_su_dong_hoc_phi_table

# Cảnh báo học vụ
php artisan make:migration create_canh_bao_hoc_vu_table

# AI Chatbot
php artisan make:migration create_ai_chatbot_knowledge_base_table
php artisan make:migration create_ai_chatbot_conversation_table
php artisan make:migration create_ai_chatbot_message_table
php artisan make:migration create_ai_chatbot_feedback_table

# Thông báo
php artisan make:migration create_thong_bao_table
php artisan make:migration create_nguoi_nhan_thong_bao_table
php artisan make:migration create_mau_thong_bao_tu_dong_table

# Nhật ký hoạt động
php artisan make:migration create_nhat_ky_hoat_dong_table

# Cache và session tables cho Laravel
php artisan make:migration create_cache_table
php artisan make:migration create_jobs_table

Write-Host "✓ Đã tạo tất cả migrations!" -ForegroundColor Green
