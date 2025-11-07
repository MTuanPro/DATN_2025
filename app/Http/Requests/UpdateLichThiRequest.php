<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLichThiRequest extends FormRequest
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
            'lop_hoc_phan_id' => 'required|exists:lop_hoc_phan,id',
            'loai_thi' => 'required|in:giua_ky,cuoi_ky,thi_lai',
            'ngay_thi' => 'required|date',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $gioBatDau = $this->input('gio_bat_dau');
                    if ($gioBatDau && strtotime($value) <= strtotime($gioBatDau)) {
                        $fail('Giờ kết thúc phải sau giờ bắt đầu.');
                    }
                },
            ],
            'phong_thi_id' => 'nullable|exists:phong_hoc,id',
            'so_sinh_vien_du_thi' => 'nullable|integer|min:0',
            'giam_thi_1_id' => 'nullable|exists:giang_vien,id',
            'giam_thi_2_id' => 'nullable|exists:giang_vien,id|different:giam_thi_1_id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'de_thi_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'dap_an_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
            'de_thi_file' => 'đề thi',
            'dap_an_file' => 'đáp án',
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
            'de_thi_file.file' => 'Đề thi phải là file.',
            'de_thi_file.mimes' => 'Đề thi phải là file PDF, DOC hoặc DOCX.',
            'de_thi_file.max' => 'Đề thi không được vượt quá 10MB.',
            'dap_an_file.file' => 'Đáp án phải là file.',
            'dap_an_file.mimes' => 'Đáp án phải là file PDF, DOC hoặc DOCX.',
            'dap_an_file.max' => 'Đáp án không được vượt quá 10MB.',
            'ghi_chu.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }
}
