<?php

namespace App\Traits;

trait ImportHelper
{
    /**
     * Parse ngày với nhiều định dạng hỗ trợ
     * 
     * @param string|null $dateString
     * @return string|null Định dạng Y-m-d hoặc null
     * @throws \Exception
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        $dateString = trim($dateString);
        
        // Thử các định dạng phổ biến
        $formats = [
            'Y-m-d',           // 2025-12-18
            'd/m/Y',           // 18/12/2025
            'd-m-Y',           // 18-12-2025
            'd.m.Y',           // 18.12.2025
            'Y/m/d',           // 2025/12/18
            'm/d/Y',           // 12/18/2025 (US format)
        ];
        
        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                return $date->format('Y-m-d'); // Chuẩn hóa về Y-m-d
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Nếu không parse được với format cụ thể, thử parse tự động
        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Không thể parse ngày: {$dateString}. Vui lòng sử dụng định dạng YYYY-MM-DD hoặc DD/MM/YYYY");
        }
    }
}

