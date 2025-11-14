<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SimpleArrayExport
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write data to sheet
        $rowIndex = 1;
        foreach ($this->data as $row) {
            $colIndex = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue($colIndex . $rowIndex, $cell);
                $colIndex++;
            }
            $rowIndex++;
        }

        // Style header row if exists
        if (!empty($this->data)) {
            $sheet->getStyle('A1:' . chr(64 + count($this->data[0])) . '1')->getFont()->setBold(true);
        }

        // Auto-size columns
        foreach (range('A', chr(64 + count($this->data[0] ?? []))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
