<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng chức năng - lưu danh sách các route/chức năng trong hệ thống
     * Mỗi chức năng sẽ được gắn với một quyền
     * 
     * Ví dụ:
     * - route_name: 'dao-tao.khoa.store' 
     * - ten_chuc_nang: 'Thêm khoa'
     * - nhom: 'Quản lý khoa'
     * - actor: 'dao_tao'
     */
    public function up(): void
    {
        Schema::create('chuc_nang', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique()->comment('Tên route Laravel: dao-tao.khoa.store');
            $table->string('ten_chuc_nang')->comment('Tên hiển thị: Thêm khoa');
            $table->string('nhom')->nullable()->comment('Nhóm chức năng: Quản lý khoa');
            $table->string('actor', 50)->comment('Actor sở hữu: admin, dao_tao, giang_vien, sinh_vien');
            $table->string('method')->default('GET')->comment('HTTP method: GET, POST, PUT, DELETE');
            $table->string('uri')->nullable()->comment('URI pattern: dao-tao/khoa');
            $table->foreignId('quyen_id')->nullable()->constrained('quyen')->onDelete('set null');
            $table->boolean('yeu_cau_quyen')->default(true)->comment('Có yêu cầu kiểm tra quyền không');
            $table->timestamps();

            $table->index('actor');
            $table->index('nhom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuc_nang');
    }
};
