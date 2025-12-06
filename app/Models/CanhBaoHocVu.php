<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Models\DaoTao;

class CanhBaoHocVu extends Model
{
    use HasFactory;

    protected $table = 'canh_bao_hoc_vu';

    protected $fillable = [
        'sinh_vien_id',
        'hoc_ky_id',
        'loai_canh_bao',
        'muc_do',
        'ly_do',
        'ghi_chu',
        'ngay_canh_bao',
        'nguoi_tao_id',
        'nguoi_canh_bao_id', // Legacy field, use nguoi_tao_id instead
        'nguoi_xu_ly_id',
        'trang_thai',
        'ket_qua_xu_ly',
        'da_xem',
    ];

    protected $casts = [
        'ngay_canh_bao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: CanhBaoHocVu belongs to SinhVien
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: CanhBaoHocVu belongs to HocKy
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Relationship: CanhBaoHocVu belongs to User (người tạo)
     */
    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Relationship: CanhBaoHocVu belongs to User (người xử lý)
     */
    public function nguoiXuLy()
    {
        return $this->belongsTo(User::class, 'nguoi_xu_ly_id');
    }

    /**
     * Relationship: CanhBaoHocVu belongs to DaoTao (người cảnh báo) - Legacy support
     * @deprecated Use nguoiTao() instead
     */
    public function nguoiCanhBao()
    {
        return $this->nguoiTao();
    }

    /**
     * Lấy badge class theo mức độ cảnh báo
     */
    public function getMucDoBadgeAttribute()
    {
        $badges = [
            'canh_cao' => 'warning',
            'dinh_chi' => 'danger',
            'buoc_thoi_hoc' => 'dark',
        ];

        return $badges[$this->muc_do] ?? 'secondary';
    }

    /**
     * Lấy label mức độ cảnh báo
     */
    public function getMucDoLabelAttribute()
    {
        $labels = [
            'canh_cao' => 'Cảnh cáo',
            'dinh_chi' => 'Đình chỉ',
            'buoc_thoi_hoc' => 'Buộc thôi học',
        ];

        return $labels[$this->muc_do] ?? 'Không xác định';
    }

    /**
     * Lấy label loại cảnh báo
     */
    public function getLoaiCanhBaoLabelAttribute()
    {
        $labels = [
            'diem_thap' => 'Điểm thấp',
            'vang_nhieu' => 'Vắng nhiều',
            'no_hoc_phi' => 'Nợ học phí',
            'hoc_ky_lien_tiep' => 'Kết quả kém nhiều học kỳ liên tiếp',
        ];

        return $labels[$this->loai_canh_bao] ?? 'Khác';
    }

    /**
     * Scope: Lọc theo mức độ
     */
    public function scopeMucDo($query, $mucDo)
    {
        return $query->where('muc_do', $mucDo);
    }

    /**
     * Scope: Lọc theo loại cảnh báo
     */
    public function scopeLoai($query, $loai)
    {
        return $query->where('loai_canh_bao', $loai);
    }

    /**
     * Scope: Lọc cảnh báo chưa xử lý
     */
    public function scopeChuaXuLy($query)
    {
        return $query->where('trang_thai', 'chua_xu_ly');
    }

    /**
     * Scope: Lọc cảnh báo đã xử lý
     */
    public function scopeDaXuLy($query)
    {
        return $query->where('trang_thai', 'da_xu_ly');
    }

    /**
     * Scope: Lọc theo trạng thái
     */
    public function scopeTrangThai($query, $trangThai)
    {
        return $query->where('trang_thai', $trangThai);
    }
}
