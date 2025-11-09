<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichThi extends Model
{
    use SoftDeletes;

    protected $table = 'lich_thi';

    protected $fillable = [
        'lop_hoc_phan_id',
        'loai_thi',
        'ngay_thi',
        'gio_bat_dau',
        'gio_ket_thuc',
        'phong_thi_id',
        'so_sinh_vien_du_thi',
        'giam_thi_1_id',
        'giam_thi_2_id',
        'hinh_thuc',
        'link_online',
        'de_thi_file',
        'dap_an_file',
        'ghi_chu',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'ngay_thi' => 'date',
        'so_sinh_vien_du_thi' => 'integer',
    ];

    /**
     * Relationships
     */

    // Lịch thi thuộc về một lớp học phần
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    // Lịch thi thuộc về một học kỳ (thông qua lớp học phần)
    public function hocKy()
    {
        return $this->hasOneThrough(
            HocKy::class,
            LopHocPhan::class,
            'id',           // Foreign key on lop_hoc_phan table
            'id',           // Foreign key on hoc_ky table
            'lop_hoc_phan_id', // Local key on lich_thi table
            'hoc_ky_id'     // Local key on lop_hoc_phan table
        );
    }

    // Lịch thi tại một phòng thi
    public function phongThi()
    {
        return $this->belongsTo(\App\Models\DanhMuc\PhongHoc::class, 'phong_thi_id');
    }

    // Alias để tương thích ngược
    public function phongHoc()
    {
        return $this->phongThi();
    }

    // Giám thị 1
    public function giamThi1()
    {
        return $this->belongsTo(GiangVien::class, 'giam_thi_1_id');
    }

    // Giám thị 2
    public function giamThi2()
    {
        return $this->belongsTo(GiangVien::class, 'giam_thi_2_id');
    }

    /**
     * Scopes
     */

    // Lọc theo học kỳ (thông qua lớp học phần)
    public function scopeByHocKy($query, $hocKyId)
    {
        return $query->whereHas('lopHocPhan', function($q) use ($hocKyId) {
            $q->where('hoc_ky_id', $hocKyId);
        });
    }

    // Lọc theo loại thi
    public function scopeByLoaiThi($query, $loaiThi)
    {
        return $query->where('loai_thi', $loaiThi);
    }

    // Lọc theo ngày thi
    public function scopeByNgayThi($query, $from, $to = null)
    {
        if ($to) {
            return $query->whereBetween('ngay_thi', [$from, $to]);
        }
        return $query->whereDate('ngay_thi', $from);
    }

    // Lọc theo giảng viên giám thị
    public function scopeByGiamThi($query, $giangVienId)
    {
        return $query->where(function($q) use ($giangVienId) {
            $q->where('giam_thi_1_id', $giangVienId)
              ->orWhere('giam_thi_2_id', $giangVienId);
        });
    }

    /**
     * Accessors & Mutators
     */

    // Lấy tên loại thi
    public function getLoaiThiTextAttribute()
    {
        $loaiThi = [
            'giua_ky' => 'Giữa kỳ',
            'cuoi_ky' => 'Cuối kỳ',
            'thi_lai' => 'Thi lại',
        ];

        return $loaiThi[$this->loai_thi] ?? $this->loai_thi;
    }

    // Lấy tên hình thức thi
    public function getHinhThucThiTextAttribute()
    {
        $hinhThuc = [
            'offline' => 'Thi tại trường',
            'online' => 'Thi trực tuyến',
            'hybrid' => 'Kết hợp',
        ];

        return $hinhThuc[$this->hinh_thuc_thi] ?? $this->hinh_thuc_thi;
    }

    // Kiểm tra có giám thị không
    public function hasGiamThi()
    {
        return $this->giam_thi_1_id || $this->giam_thi_2_id;
    }

    // Kiểm tra đã upload đề thi chưa
    public function hasDeThe()
    {
        return !empty($this->de_thi);
    }

    // Kiểm tra đã upload đáp án chưa
    public function hasDapAn()
    {
        return !empty($this->dap_an);
    }

    /**
     * Relationship: Lịch thi có nhiều sinh viên dự thi
     */
    public function lichThiSinhViens()
    {
        return $this->hasMany(LichThiSinhVien::class, 'lich_thi_id');
    }

    /**
     * Relationship: Lấy danh sách sinh viên thông qua bảng trung gian
     */
    public function sinhViens()
    {
        return $this->belongsToMany(
            SinhVien::class,
            'lich_thi_sinh_vien',
            'lich_thi_id',
            'sinh_vien_id'
        )->withPivot('phong_thi_id', 'so_bao_danh', 'trang_thai', 'ghi_chu')
          ->withTimestamps();
    }
}
