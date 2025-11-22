<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonHocRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Chỉ cho phép Đào tạo tạo môn học
        return auth()->check() && 
               (auth()->user()->hasRole('admin') || 
                auth()->user()->hasRole('truong_phong_dt') || 
                auth()->user()->hasRole('nhan_vien_dt'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ma_mon' => 'required|string|max:20|unique:mon_hoc,ma_mon',
            'ten_mon' => 'required|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:10',
            'so_tin_chi_ly_thuyet' => 'required|integer|min:0|max:10',
            'so_tin_chi_thuc_hanh' => 'required|integer|min:0|max:10',
            'mo_ta' => 'nullable|string',
            'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'khoa_id' => 'required|exists:khoa,id',
            'hinh_thuc_day' => 'required|in:offline,online,hybrid',
            'thoi_luong_hoc' => 'nullable|integer|min:15',
            'so_buoi_hoc' => 'nullable|integer|min:10',
        ];
    }

    /**
     * Custom validation logic
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra: so_tin_chi = so_tin_chi_ly_thuyet + so_tin_chi_thuc_hanh
            if ($this->so_tin_chi != ($this->so_tin_chi_ly_thuyet + $this->so_tin_chi_thuc_hanh)) {
                $validator->errors()->add('so_tin_chi', 
                    'Số tín chỉ phải bằng tổng tín chỉ lý thuyết và thực hành. ' .
                    'Hiện tại: ' . $this->so_tin_chi . ' ≠ ' . 
                    '(' . $this->so_tin_chi_ly_thuyet . ' + ' . $this->so_tin_chi_thuc_hanh . ')'
                );
            }

            // Kiểm tra: thời lượng học phải ít nhất = so_tin_chi * 15 giờ
            if ($this->thoi_luong_hoc && $this->thoi_luong_hoc < ($this->so_tin_chi * 15)) {
                $validator->errors()->add('thoi_luong_hoc', 
                    'Thời lượng học tối thiểu phải là ' . ($this->so_tin_chi * 15) . ' giờ ' .
                    '(= ' . $this->so_tin_chi . ' tín chỉ × 15 giờ/tín chỉ)'
                );
            }

            // Kiểm tra: ít nhất phải có 1 loại tín chỉ (lý thuyết hoặc thực hành)
            if ($this->so_tin_chi_ly_thuyet == 0 && $this->so_tin_chi_thuc_hanh == 0) {
                $validator->errors()->add('so_tin_chi_ly_thuyet', 
                    'Môn học phải có ít nhất 1 tín chỉ lý thuyết hoặc thực hành'
                );
            }
        });
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'ma_mon.required' => 'Mã môn học là bắt buộc',
            'ma_mon.unique' => 'Mã môn học đã tồn tại',
            'ten_mon.required' => 'Tên môn học là bắt buộc',
            'so_tin_chi.required' => 'Số tín chỉ là bắt buộc',
            'so_tin_chi.min' => 'Số tín chỉ tối thiểu là 1',
            'so_tin_chi.max' => 'Số tín chỉ tối đa là 10',
            'so_tin_chi_ly_thuyet.required' => 'Số tín chỉ lý thuyết là bắt buộc',
            'so_tin_chi_thuc_hanh.required' => 'Số tín chỉ thực hành là bắt buộc',
            'loai_mon.required' => 'Loại môn học là bắt buộc',
            'loai_mon.in' => 'Loại môn học không hợp lệ',
            'khoa_id.required' => 'Khoa quản lý môn học là bắt buộc',
            'khoa_id.exists' => 'Khoa không tồn tại',
            'hinh_thuc_day.required' => 'Hình thức dạy là bắt buộc',
            'hinh_thuc_day.in' => 'Hình thức dạy phải là: offline, online hoặc hybrid',
            'thoi_luong_hoc.min' => 'Thời lượng học tối thiểu là 15 giờ',
            'so_buoi_hoc.min' => 'Số buổi học tối thiểu là 10 buổi',
        ];
    }

    /**
     * Custom attribute names
     */
    public function attributes(): array
    {
        return [
            'ma_mon' => 'Mã môn học',
            'ten_mon' => 'Tên môn học',
            'so_tin_chi' => 'Số tín chỉ',
            'so_tin_chi_ly_thuyet' => 'Số tín chỉ lý thuyết',
            'so_tin_chi_thuc_hanh' => 'Số tín chỉ thực hành',
            'mo_ta' => 'Mô tả',
            'loai_mon' => 'Loại môn học',
            'khoa_id' => 'Khoa',
            'hinh_thuc_day' => 'Hình thức dạy',
            'thoi_luong_hoc' => 'Thời lượng học',
            'so_buoi_hoc' => 'Số buổi học',
        ];
    }
}




//  public function attributes(): array
//     {
//         return [
//             'ma_mon' => 'Mã môn học',
//             'ten_mon' => 'Tên môn học',
//             'so_tin_chi' => 'Số tín chỉ',
//             'so_tin_chi_ly_thuyet' => 'Số tín chỉ lý thuyết',
//             'so_tin_chi_thuc_hanh' => 'Số tín chỉ thực hành',
//             'mo_ta' => 'Mô tả',
//             'loai_mon' => 'Loại môn học',
//             'khoa_id' => 'Khoa',
//             'hinh_thuc_day' => 'Hình thức dạy',
//             'thoi_luong_hoc' => 'Thời lượng học',
//             'so_buoi_hoc' => 'Số buổi học',
//         ];
//     }
// }
