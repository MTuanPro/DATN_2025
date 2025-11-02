<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';

    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'loai_nguon',
        'loai_thong_bao',
        'muc_do_quan_trong',
        'ghim_dau_trang',
        'doi_tuong',
        'doi_tuong_cu_the_id',
        'nguoi_gui_id',
        'anh_dai_dien',
        'file_dinh_kem',
        'lien_ket_id',
        'lien_ket_loai',
        'ngay_gui',
        'ngay_het_han',
        'hien_thi_tu_ngay',
        'gui_email',
        'gui_sms',
        'gui_web_notification',
        'so_luot_xem',
        'trang_thai',
    ];

    protected $casts = [
        'ghim_dau_trang' => 'boolean',
        'gui_email' => 'boolean',
        'gui_sms' => 'boolean',
        'gui_web_notification' => 'boolean',
        'ngay_gui' => 'datetime',
        'ngay_het_han' => 'datetime',
        'hien_thi_tu_ngay' => 'datetime',
        'so_luot_xem' => 'integer',
    ];

    /**
     * Relationship: Người gửi
     */
    public function nguoiGui()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    /**
     * Relationship: Người nhận thông báo
     */
    public function nguoiNhan()
    {
        return $this->hasMany(NguoiNhanThongBao::class, 'thong_bao_id');
    }

    /**
     * Scope: Chỉ lấy thông báo công khai
     */
    public function scopeCongKhai($query)
    {
        return $query->where('trang_thai', 'cong_khai');
    }

    /**
     * Scope: Thông báo đang hiển thị (trong thời gian hiệu lực)
     */
    public function scopeDangHienThi($query)
    {
        $now = now();
        return $query->where('trang_thai', 'cong_khai')
            ->where(function ($q) use ($now) {
                $q->whereNull('hien_thi_tu_ngay')
                    ->orWhere('hien_thi_tu_ngay', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ngay_het_han')
                    ->orWhere('ngay_het_han', '>=', $now);
            });
    }

    /**
     * Scope: Thông báo được ghim
     */
    public function scopeGhim($query)
    {
        return $query->where('ghim_dau_trang', true);
    }

    /**
     * Scope: Thông báo theo đối tượng
     */
    public function scopeDoiTuong($query, $doiTuong)
    {
        return $query->where('doi_tuong', $doiTuong);
    }

    /**
     * Scope: Thông báo theo loại
     */
    public function scopeLoai($query, $loai)
    {
        return $query->where('loai_thong_bao', $loai);
    }

    /**
     * Tăng số lượt xem
     */
    public function tangLuotXem()
    {
        $this->increment('so_luot_xem');
    }

    /**
     * Kiểm tra thông báo có đang hiển thị không
     */
    public function isDangHienThi()
    {
        if ($this->trang_thai !== 'cong_khai') {
            return false;
        }

        $now = now();

        if ($this->hien_thi_tu_ngay && $this->hien_thi_tu_ngay > $now) {
            return false;
        }

        if ($this->ngay_het_han && $this->ngay_het_han < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get badge color cho mức độ quan trọng
     */
    public function getMucDoColor()
    {
        return match ($this->muc_do_quan_trong) {
            'rat_quan_trong' => 'danger',
            'quan_trong' => 'warning',
            'binh_thuong' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get badge color cho loại thông báo
     */
    public function getLoaiColor()
    {
        return match ($this->loai_thong_bao) {
            'tin_gap' => 'danger',
            'lich_thi' => 'warning',
            'diem' => 'success',
            'hoc_phi' => 'primary',
            'lich_hoc' => 'info',
            'dang_ky_mon' => 'warning',
            'tin_tuc' => 'secondary',
            'thong_bao_chung' => 'dark',
            default => 'secondary',
        };
    }
}
