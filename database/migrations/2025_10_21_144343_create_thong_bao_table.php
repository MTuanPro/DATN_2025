<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->string('loai_nguon')->default('thu_cong')->comment('thu_cong: Admin gửi, tu_dong: Hệ thống tự động');
            $table->string('loai_thong_bao')->comment('tin_tuc, thong_bao_chung, tin_gap, lich_hoc, lich_thi, hoc_phi, diem, dang_ky_mon');
            $table->string('muc_do_quan_trong')->default('binh_thuong')->comment('rat_quan_trong, quan_trong, binh_thuong');
            $table->boolean('ghim_dau_trang')->default(false)->comment('Ghim thông báo ở đầu');
            $table->string('doi_tuong')->comment('sinh_vien, giang_vien, dao_tao, lop_hanh_chinh, lop_hoc_phan, khoa, nganh, all');
            $table->unsignedBigInteger('doi_tuong_cu_the_id')->nullable()->comment('ID cụ thể: khoa_id, nganh_id, lop_hanh_chinh_id, lop_hoc_phan_id');
            $table->foreignId('nguoi_gui_id')->nullable()->constrained('users')->onDelete('set null')->comment('NULL nếu là thông báo tự động');
            $table->string('anh_dai_dien')->nullable()->comment('Ảnh đại diện cho tin tức/thông báo');
            $table->string('file_dinh_kem')->nullable()->comment('File đính kèm (PDF, Word, Excel)');
            $table->unsignedBigInteger('lien_ket_id')->nullable()->comment('ID liên quan: lich_hoc_chi_tiet_id, lich_thi_id, hoc_phi_id');
            $table->string('lien_ket_loai')->nullable()->comment('Loại liên kết: lich_hoc, lich_thi, hoc_phi, diem');
            $table->timestamp('ngay_gui');
            $table->timestamp('ngay_het_han')->nullable();
            $table->timestamp('hien_thi_tu_ngay')->nullable()->comment('Thời gian bắt đầu hiển thị (hẹn giờ)');
            $table->boolean('gui_email')->default(false);
            $table->boolean('gui_sms')->default(false);
            $table->boolean('gui_web_notification')->default(true);
            $table->integer('so_luot_xem')->default(0);
            $table->string('trang_thai')->default('cong_khai')->comment('cong_khai, nhap, da_xoa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
