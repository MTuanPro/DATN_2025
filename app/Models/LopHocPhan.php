<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DaoTao\MonHoc;

class LopHocPhan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lop_hoc_phan';

    protected $fillable = [
        'ma_lop_hp',
        'ten_lop_hp',
        'mon_hoc_id',
        'hoc_ky_id',
        'nhom_lop',
        'suc_chua',
        'so_luong_dang_ky',
        'so_luong_toi_thieu',
        'hinh_thuc',
        'link_online',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ghi_chu',
        'trang_thai_lop',
        'ly_do_tra_ve',
        'lan_gui_diem',
        'trang_thai_gui_diem_lan_1',
        'trang_thai_gui_diem_lan_2',
        'cho_phep_gui_diem_lan_1',
        'cho_phep_gui_diem_lan_2',
        'cho_phep_sua_diem_sau_duyet',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Quan hệ với MonHoc
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Quan hệ với HocKy
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Quan hệ với LopHocPhanGiangVien (Phân công giảng dạy)
     */
    public function lopHocPhanGiangVien()
    {
        return $this->hasMany(PhanCongGiangDay::class, 'lop_hoc_phan_id');
    }

    /**
     * Quan hệ với GiangVien thông qua bảng trung gian lop_hoc_phan_giang_vien
     */
    public function giangViens()
    {
        return $this->belongsToMany(
            GiangVien::class,
            'lop_hoc_phan_giang_vien',
            'lop_hoc_phan_id',
            'giang_vien_id'
        )
            ->withPivot('vai_tro', 'ngay_phan_cong')
            ->withTimestamps();
    }

    /**
     * Lấy giảng viên chính
     */
    public function giangVienChinh()
    {
        return $this->hasOne(PhanCongGiangDay::class, 'lop_hoc_phan_id')
            ->where('vai_tro', 'giang_vien_chinh');
    }

    /**
     * Alias cho giangVienChinh
     */
    public function giangVien()
    {
        return $this->giangVienChinh();
    }

    /**
     * Quan hệ với LopHocPhanSinhVien (Sinh viên trong lớp)
     */
    public function lopHocPhanSinhViens()
    {
        return $this->hasMany(LopHocPhanSinhVien::class, 'lop_hoc_phan_id');
    }

    /**
     * Quan hệ với SinhVien thông qua LopHocPhanSinhVien
     */
    public function sinhViens()
    {
        return $this->belongsToMany(SinhVien::class, 'lop_hoc_phan_sinh_vien', 'lop_hoc_phan_id', 'sinh_vien_id')
            ->withPivot('trang_thai', 'ngay_dang_ky')
            ->withTimestamps();
    }

    /**
     * Quan hệ với CauHinhDauDiem
     */
    public function cauHinhDauDiem()
    {
        return $this->hasMany(CauHinhDauDiem::class, 'lop_hoc_phan_id')
            ->orderBy('id');
    }

    /**
     * Quan hệ với LichHocCoDinh
     */
    public function lichHocCoDinhs()
    {
        return $this->hasMany(LichHocCoDinh::class, 'lop_hoc_phan_id');
    }

    /**
     * Quan hệ với LichHocChiTiet
     */
    public function lichHocChiTiets()
    {
        return $this->hasMany(LichHocChiTiet::class, 'lop_hoc_phan_id');
    }

    /**
     * Alias cho lichHocChiTiets (số ít)
     */
    public function lichHocChiTiet()
    {
        return $this->lichHocChiTiets();
    }

    public function lopHocPhanSinhVien()
    {
        return $this->hasMany(LopHocPhanSinhVien::class, 'lop_hoc_phan_id');
    }

    /**
     * Kiểm tra còn chỗ trống không
     */
    public function conChoTrong()
    {
        return $this->so_luong_dang_ky < $this->suc_chua;
    }

    /**
     * Kiểm tra đủ số lượng tối thiểu chưa
     */
    public function duSoLuongToiThieu()
    {
        return $this->so_luong_dang_ky >= $this->so_luong_toi_thieu;
    }

    /**
     * Kiểm tra lớp đã kết thúc chưa (dựa vào trạng thái hoặc ngày kết thúc)
     */
    public function daKetThuc()
    {
        // Nếu trạng thái là 'ket_thuc' thì coi như đã kết thúc
        if ($this->trang_thai_lop === 'ket_thuc') {
            return true;
        }
        
        // Nếu có ngày kết thúc và đã qua ngày đó thì cũng coi như đã kết thúc
        if ($this->ngay_ket_thuc && now()->isAfter($this->ngay_ket_thuc)) {
            return true;
        }
        
        return false;
    }

    /**
     * Kiểm tra lớp đang diễn ra (chưa kết thúc)
     */
    public function dangDienRa()
    {
        return !$this->daKetThuc();
    }

    /**
     * Lấy tên đầy đủ (Mã + Tên môn + Tên lớp)
     */
    public function getTenDayDuAttribute()
    {
        return $this->ma_lop_hp . ' - ' . $this->monHoc->ten_mon . ' (' . $this->ten_lop_hp . ')';
    }

    /**
     * Lấy tên trạng thái tiếng Việt
     */
    public function getTenTrangThaiAttribute()
    {
        $trangThai = [
            'mo_dang_ky' => 'Mở đăng ký',
            'dang_hoc' => 'Đang học',
            'ket_thuc' => 'Kết thúc',
            'huy' => 'Hủy',
            'da_khoa_diem' => 'Đã khóa điểm',
            'da_duyet_diem' => 'Đã duyệt điểm',
        ];

        return $trangThai[$this->trang_thai_lop] ?? $this->trang_thai_lop;
    }
}
