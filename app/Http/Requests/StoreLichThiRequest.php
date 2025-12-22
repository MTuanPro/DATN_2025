<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Models\DanhMuc\PhongHoc;

class StoreLichThiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Sẽ xử lý qua middleware CheckRole
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'lop_hoc_phan_id' => [
                'required',
                'exists:lop_hoc_phan,id',
                function ($attribute, $value, $fail) {
                    // Kiểm tra lớp học phần đã có lịch thi loại này chưa
                    $loaiThi = $this->input('loai_thi');
                    $lichThiTonTai = LichThi::where('lop_hoc_phan_id', $value)
                        ->where('loai_thi', $loaiThi)
                        ->exists();
                    
                    if ($lichThiTonTai) {
                        $lopHocPhan = LopHocPhan::find($value);
                        $loaiThiText = $loaiThi == 'giua_ky' ? 'Giữa kỳ' : ($loaiThi == 'cuoi_ky' ? 'Cuối kỳ' : 'Thi lại');
                        $fail("Lớp học phần {$lopHocPhan->ma_lop_hp} đã có lịch thi {$loaiThiText}.");
                    }
                },
            ],
            'loai_thi' => 'required|in:giua_ky,cuoi_ky,thi_lai',
            'ngay_thi' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $lopHocPhanId = $this->input('lop_hoc_phan_id');
                    if (!$lopHocPhanId) return;
                    
                    $lopHocPhan = LopHocPhan::find($lopHocPhanId);
                    if (!$lopHocPhan || !$lopHocPhan->ngay_ket_thuc) return;
                    
                    $ngayThi = \Carbon\Carbon::parse($value);
                    $ngayKetThucHoc = \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc);
                    
                    if ($ngayThi->lte($ngayKetThucHoc)) {
                        $fail('Ngày thi phải sau ngày kết thúc học của lớp học phần (' . 
                              $ngayKetThucHoc->format('d/m/Y') . ').');
                    }
                },
            ],
            'ca_hoc_id' => 'required|exists:ca_hoc,id',
            'gio_bat_dau' => 'nullable|date_format:H:i',
            'gio_ket_thuc' => 'nullable|date_format:H:i',
            'phong_thi_id' => [
                'nullable',
                'exists:phong_hoc,id',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    
                    $ngayThi = $this->input('ngay_thi');
                    $gioBatDau = $this->input('gio_bat_dau');
                    $gioKetThuc = $this->input('gio_ket_thuc');
                    
                    if (!$ngayThi || !$gioBatDau || !$gioKetThuc) return;
                    
                    // Kiểm tra phòng thi có trùng lịch không
                    $trungLich = LichThi::kiemTraXungDotPhong($value, $ngayThi, $gioBatDau, $gioKetThuc);
                    
                    if ($trungLich) {
                        // Lấy thông tin lịch thi trùng để hiển thị
                        $lichThiTrung = LichThi::where('phong_thi_id', $value)
                            ->where('ngay_thi', $ngayThi)
                            ->where(function ($q) use ($gioBatDau, $gioKetThuc) {
                                $q->where('gio_ket_thuc', '>=', $gioBatDau)
                                  ->where('gio_bat_dau', '<=', $gioKetThuc);
                            })
                            ->with(['phongThi', 'lopHocPhan.monHoc'])
                            ->first();
                        
                        if ($lichThiTrung) {
                            $phong = PhongHoc::find($value);
                            $fail("Phòng {$phong->ten_phong} đã có lịch thi vào {$ngayThi} từ {$lichThiTrung->gio_bat_dau} đến {$lichThiTrung->gio_ket_thuc} (Môn: {$lichThiTrung->lopHocPhan->monHoc->ten_mon}).");
                        }
                    }
                },
            ],
            'so_sinh_vien_du_thi' => 'nullable|integer|min:0',
            'giam_thi_1_id' => [
                'nullable',
                'exists:giang_vien,id',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    
                    $ngayThi = $this->input('ngay_thi');
                    $gioBatDau = $this->input('gio_bat_dau');
                    $gioKetThuc = $this->input('gio_ket_thuc');
                    
                    if (!$ngayThi || !$gioBatDau || !$gioKetThuc) return;
                    
                    // Kiểm tra giảng viên có trùng lịch coi thi không
                    $trungLich = LichThi::kiemTraXungDotGiamThi($value, $ngayThi, $gioBatDau, $gioKetThuc);
                    
                    if ($trungLich) {
                        // Lấy thông tin lịch thi trùng để hiển thị
                        $lichThiTrung = LichThi::where('ngay_thi', $ngayThi)
                            ->where(function ($q) use ($value) {
                                $q->where('giam_thi_1_id', $value)
                                  ->orWhere('giam_thi_2_id', $value);
                            })
                            ->where(function ($q) use ($gioBatDau, $gioKetThuc) {
                                $q->where('gio_ket_thuc', '>=', $gioBatDau)
                                  ->where('gio_bat_dau', '<=', $gioKetThuc);
                            })
                            ->with(['lopHocPhan.monHoc', 'phongThi'])
                            ->first();
                        
                        if ($lichThiTrung) {
                            $giangVien = GiangVien::find($value);
                            $phongThi = $lichThiTrung->phongThi ? $lichThiTrung->phongThi->ten_phong : 'N/A';
                            $fail("Giảng viên {$giangVien->ho_ten} đã có lịch coi thi vào {$ngayThi} từ {$lichThiTrung->gio_bat_dau} đến {$lichThiTrung->gio_ket_thuc} (Môn: {$lichThiTrung->lopHocPhan->monHoc->ten_mon}, Phòng: {$phongThi}).");
                        }
                    }
                },
            ],
            'giam_thi_2_id' => [
                'nullable',
                'exists:giang_vien,id',
                'different:giam_thi_1_id',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    
                    $ngayThi = $this->input('ngay_thi');
                    $gioBatDau = $this->input('gio_bat_dau');
                    $gioKetThuc = $this->input('gio_ket_thuc');
                    
                    if (!$ngayThi || !$gioBatDau || !$gioKetThuc) return;
                    
                    // Kiểm tra giảng viên có trùng lịch coi thi không
                    $trungLich = LichThi::kiemTraXungDotGiamThi($value, $ngayThi, $gioBatDau, $gioKetThuc);
                    
                    if ($trungLich) {
                        // Lấy thông tin lịch thi trùng để hiển thị
                        $lichThiTrung = LichThi::where('ngay_thi', $ngayThi)
                            ->where(function ($q) use ($value) {
                                $q->where('giam_thi_1_id', $value)
                                  ->orWhere('giam_thi_2_id', $value);
                            })
                            ->where(function ($q) use ($gioBatDau, $gioKetThuc) {
                                $q->where('gio_ket_thuc', '>=', $gioBatDau)
                                  ->where('gio_bat_dau', '<=', $gioKetThuc);
                            })
                            ->with(['lopHocPhan.monHoc', 'phongThi'])
                            ->first();
                        
                        if ($lichThiTrung) {
                            $giangVien = GiangVien::find($value);
                            $phongThi = $lichThiTrung->phongThi ? $lichThiTrung->phongThi->ten_phong : 'N/A';
                            $fail("Giảng viên {$giangVien->ho_ten} đã có lịch coi thi vào {$ngayThi} từ {$lichThiTrung->gio_bat_dau} đến {$lichThiTrung->gio_ket_thuc} (Môn: {$lichThiTrung->lopHocPhan->monHoc->ten_mon}, Phòng: {$phongThi}).");
                        }
                    }
                },
            ],
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'ghi_chu' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'lop_hoc_phan_id' => 'lớp học phần',
            'loai_thi' => 'loại thi',
            'ngay_thi' => 'ngày thi',
            'gio_bat_dau' => 'giờ bắt đầu',
            'gio_ket_thuc' => 'giờ kết thúc',
            'phong_thi_id' => 'phòng thi',
            'so_sinh_vien_du_thi' => 'số sinh viên dự thi',
            'giam_thi_1_id' => 'giám thị 1',
            'giam_thi_2_id' => 'giám thị 2',
            'hinh_thuc' => 'hình thức thi',
            'link_online' => 'link thi online',
            'ghi_chu' => 'ghi chú',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lop_hoc_phan_id.required' => 'Vui lòng chọn lớp học phần.',
            'lop_hoc_phan_id.exists' => 'Lớp học phần không tồn tại.',
            'loai_thi.required' => 'Vui lòng chọn loại thi.',
            'loai_thi.in' => 'Loại thi không hợp lệ.',
            'ngay_thi.required' => 'Vui lòng chọn ngày thi.',
            'ngay_thi.date' => 'Ngày thi không đúng định dạng.',
            'ngay_thi.after_or_equal' => 'Ngày thi phải từ hôm nay trở đi.',
            'gio_bat_dau.required' => 'Vui lòng nhập giờ bắt đầu.',
            'gio_bat_dau.date_format' => 'Giờ bắt đầu không đúng định dạng (HH:mm).',
            'gio_ket_thuc.required' => 'Vui lòng nhập giờ kết thúc.',
            'gio_ket_thuc.date_format' => 'Giờ kết thúc không đúng định dạng (HH:mm).',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'phong_thi_id.exists' => 'Phòng thi không tồn tại.',
            'so_sinh_vien_du_thi.integer' => 'Số sinh viên dự thi phải là số nguyên.',
            'so_sinh_vien_du_thi.min' => 'Số sinh viên dự thi phải lớn hơn hoặc bằng 0.',
            'giam_thi_1_id.exists' => 'Giám thị 1 không tồn tại.',
            'giam_thi_2_id.exists' => 'Giám thị 2 không tồn tại.',
            'giam_thi_2_id.different' => 'Giám thị 2 phải khác giám thị 1.',
            'hinh_thuc.required' => 'Vui lòng chọn hình thức thi.',
            'hinh_thuc.in' => 'Hình thức thi không hợp lệ.',
            'link_online.url' => 'Link thi online không đúng định dạng.',
            'ghi_chu.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }
}
