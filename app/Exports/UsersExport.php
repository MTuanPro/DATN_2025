<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport
{
    protected $users;
    protected $headings;

    public function __construct($users = null)
    {
        $this->users = $users;
        $this->headings = [
            'ID',
            'Họ tên',
            'Email',
            'Vai trò',
            'Trạng thái',
            'Email đã xác thực',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }

    /**
     * Export users to Excel
     */
    public function download($filename = 'users.xlsx')
    {
        $users = $this->users ?? User::with('vaiTro')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $columnIndex = 'A';
        foreach ($this->headings as $heading) {
            $sheet->setCellValue($columnIndex . '1', $heading);
            $columnIndex++;
        }

        // Style header row
        $lastColumn = chr(64 + count($this->headings));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Add data
        $rowIndex = 2;
        foreach ($users as $user) {
            $vaiTros = $user->vaiTro->pluck('ten_vai_tro')->implode(', ');
            $trangThai = $this->getTrangThaiText($user->trang_thai);
            $emailVerified = $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực';

            $sheet->setCellValue('A' . $rowIndex, $user->id);
            $sheet->setCellValue('B' . $rowIndex, $user->name);
            $sheet->setCellValue('C' . $rowIndex, $user->email);
            $sheet->setCellValue('D' . $rowIndex, $vaiTros);
            $sheet->setCellValue('E' . $rowIndex, $trangThai);
            $sheet->setCellValue('F' . $rowIndex, $emailVerified);
            $sheet->setCellValue('G' . $rowIndex, $user->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('H' . $rowIndex, $user->updated_at->format('d/m/Y H:i'));

            $rowIndex++;
        }

        // Auto-size columns
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Get status text in Vietnamese
     */
    private function getTrangThaiText($status)
    {
        $statusMap = [
            'hoat_dong' => 'Hoạt động',
            'khoa' => 'Khóa',
            'ngung_hoat_dong' => 'Ngừng hoạt động'
        ];

        return $statusMap[$status] ?? $status;
    }
}
