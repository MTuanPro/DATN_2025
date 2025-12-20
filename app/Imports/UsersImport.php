<?php

namespace App\Imports;

use App\Models\User;
use App\Models\VaiTro;
use App\Models\Admin;
use App\Models\DaoTao;
use App\Mail\VerifyEmailMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UsersImport
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    /**
     * Import users from Excel file
     */
    public function import($filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row
            array_shift($rows);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we start from row 2 (after header)
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Skip instruction rows (rows that start with "Hướng dẫn:" or similar)
                if (!empty($row[0]) && (
                    stripos($row[0], 'hướng dẫn') !== false || 
                    stripos($row[0], 'huong dan') !== false ||
                    $row[0] === '-' || 
                    substr($row[0], 0, 1) === '-'
                )) {
                    continue;
                }

                // Skip rows where all important fields are empty
                $hasData = !empty(trim($row[0] ?? '')) || 
                          !empty(trim($row[1] ?? '')) || 
                          !empty(trim($row[2] ?? ''));
                
                if (!$hasData) {
                    continue;
                }

                $this->processRow($row, $rowNumber);
            }

            return [
                'success' => $this->successCount,
                'error' => $this->errorCount,
                'errors' => $this->errors
            ];

        } catch (\Exception $e) {
            return [
                'success' => 0,
                'error' => 1,
                'errors' => ['Lỗi đọc file: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Process a single row from Excel
     */
    protected function processRow($row, $rowNumber)
    {
        try {
            // Map columns: Họ tên, Email, Mật khẩu, Vai trò, Trạng thái
            $data = [
                'name' => trim($row[0] ?? ''),
                'email' => trim($row[1] ?? ''),
                'password' => trim($row[2] ?? ''),
                'vai_tro' => trim($row[3] ?? ''),
                'trang_thai' => trim($row[4] ?? 'hoat_dong'),
            ];

            // Validate data
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'vai_tro' => 'required|string',
                'trang_thai' => 'in:hoat_dong,khoa,ngung_hoat_dong',
            ], [
                'name.required' => 'Họ tên không được để trống',
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không hợp lệ',
                'email.unique' => 'Email đã tồn tại trong hệ thống',
                'password.required' => 'Mật khẩu không được để trống',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'vai_tro.required' => 'Vai trò không được để trống',
                'trang_thai.in' => 'Trạng thái không hợp lệ',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Dòng {$rowNumber}: " . implode(', ', $validator->errors()->all());
                $this->errorCount++;
                return;
            }

            // Find vai tro by ma_vai_tro or ten_vai_tro
            $vaiTro = VaiTro::where('ma_vai_tro', $data['vai_tro'])
                ->orWhere('ten_vai_tro', 'LIKE', $data['vai_tro'])
                ->first();

            if (!$vaiTro) {
                // Try to find available roles to suggest
                $availableRoles = VaiTro::pluck('ma_vai_tro')->toArray();
                $this->errors[] = "Dòng {$rowNumber}: Vai trò '{$data['vai_tro']}' không tồn tại. Các vai trò hợp lệ: " . implode(', ', $availableRoles);
                $this->errorCount++;
                return;
            }

            // Map trang thai text to value
            $trangThaiMap = [
                'hoat_dong' => 'hoat_dong',
                'hoạt động' => 'hoat_dong',
                'khoa' => 'khoa',
                'khóa' => 'khoa',
                'ngung_hoat_dong' => 'ngung_hoat_dong',
                'ngừng hoạt động' => 'ngung_hoat_dong',
            ];

            $trangThai = $trangThaiMap[strtolower($data['trang_thai'])] ?? 'hoat_dong';

            // Create user
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'trang_thai' => $trangThai,
            ]);

            // Attach vai tro - Ensure it's attached
            if ($vaiTro && $vaiTro->id) {
                $user->vaiTro()->syncWithoutDetaching([$vaiTro->id]);
            }

            // Tự động tạo Admin profile nếu gán vai trò admin
            if ($vaiTro->ma_vai_tro === 'admin1') {
                $existingAdmin = Admin::where('user_id', $user->id)->first();
                if (!$existingAdmin) {
                    $year = date('Y');
                    $lastAdmin = Admin::whereYear('created_at', $year)
                        ->orderBy('id', 'desc')
                        ->first();
                    $sequence = $lastAdmin ? (int)substr($lastAdmin->ma_admin, -4) + 1 : 1;
                    $maAdmin = 'AD' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    Admin::create([
                        'user_id' => $user->id,
                        'ma_admin' => $maAdmin,
                        'ho_ten' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }

            // Tự động tạo DaoTao profile nếu gán vai trò đào tạo
            if (in_array($vaiTro->ma_vai_tro, ['truong_phong_dt', 'nhan_vien_dt'])) {
                $existingDaoTao = DaoTao::where('user_id', $user->id)->first();
                if (!$existingDaoTao) {
                    $year = date('Y');
                    $lastDaoTao = DaoTao::whereYear('created_at', $year)
                        ->orderBy('id', 'desc')
                        ->first();
                    $sequence = $lastDaoTao ? (int)substr($lastDaoTao->ma_dao_tao, -4) + 1 : 1;
                    $maDaoTao = 'DT' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    DaoTao::create([
                        'user_id' => $user->id,
                        'ma_dao_tao' => $maDaoTao,
                        'ho_ten' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }

            // Gửi email xác thực
            $token = Str::random(64);
            
            // Xóa token cũ nếu có
            DB::table('email_verification_tokens')
                ->where('email', $user->email)
                ->delete();
            
            // Tạo token mới
            DB::table('email_verification_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Tạo URL xác thực
            $verificationUrl = url('/email/verify/' . $token . '?email=' . urlencode($user->email));

            try {
                Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
            } catch (\Exception $mailError) {
                // Log email error but don't fail the import
                \Log::warning("Failed to send verification email to {$user->email}: " . $mailError->getMessage());
            }

            DB::commit();
            $this->successCount++;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Dòng {$rowNumber}: " . $e->getMessage();
            $this->errorCount++;
        }
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Download sample template
     */
    public static function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách Users');

        // Set headers
        $headers = ['Họ tên', 'Email', 'Mật khẩu', 'Vai trò', 'Trạng thái'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '1', $header);
            $columnIndex++;
        }

        // Style header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Add sample data
        $sheet->setCellValue('A2', 'Nguyễn Văn A');
        $sheet->setCellValue('B2', 'nguyenvana@example.com');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'sinh_vien');
        $sheet->setCellValue('E2', 'hoat_dong');

        // Add data validation for Vai trò column (D)
        $validation = $sheet->getCell('D2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Giá trị không hợp lệ');
        $validation->setError('Vui lòng chọn vai trò từ danh sách');
        $validation->setPromptTitle('Chọn vai trò');
        $validation->setPrompt('Chọn một vai trò từ danh sách dropdown');
        $validation->setFormula1('"admin1,truong_phong_dt,nhan_vien_dt,giang_vien,sinh_vien"');

        // Apply validation to next 999 rows
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell('D' . $i)->setDataValidation(clone $validation);
        }

        // Add data validation for Trạng thái column (E)
        $statusValidation = $sheet->getCell('E2')->getDataValidation();
        $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowInputMessage(true);
        $statusValidation->setShowErrorMessage(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setErrorTitle('Giá trị không hợp lệ');
        $statusValidation->setError('Vui lòng chọn trạng thái từ danh sách');
        $statusValidation->setPromptTitle('Chọn trạng thái');
        $statusValidation->setPrompt('Chọn một trạng thái từ danh sách dropdown');
        $statusValidation->setFormula1('"hoat_dong,khoa,ngung_hoat_dong"');

        // Apply validation to next 999 rows
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell('E' . $i)->setDataValidation(clone $statusValidation);
        }

        // Add note/instructions as comments in cells
        $sheet->getComment('A1')->getText()->createTextRun('Họ tên của người dùng (bắt buộc, tối đa 255 ký tự)');
        $sheet->getComment('B1')->getText()->createTextRun('Email phải hợp lệ và chưa tồn tại trong hệ thống');
        $sheet->getComment('C1')->getText()->createTextRun('Mật khẩu tối thiểu 8 ký tự');
        $sheet->getComment('D1')->getText()->createTextRun('Chọn vai trò từ dropdown list');
        $sheet->getComment('E1')->getText()->createTextRun('Chọn trạng thái từ dropdown list');

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create instructions sheet
        $instructionSheet = $spreadsheet->createSheet(1);
        $instructionSheet->setTitle('Hướng dẫn');
        
        $instructionSheet->setCellValue('A1', 'HƯỚNG DẪN IMPORT TÀI KHOẢN');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $instructionSheet->setCellValue('A3', 'CẤU TRÚC FILE:');
        $instructionSheet->getStyle('A3')->getFont()->setBold(true);
        
        $instructions = [
            ['Cột', 'Tên trường', 'Yêu cầu', 'Mô tả'],
            ['A', 'Họ tên', 'Bắt buộc', 'Họ và tên đầy đủ của người dùng, tối đa 255 ký tự'],
            ['B', 'Email', 'Bắt buộc', 'Email hợp lệ và chưa tồn tại trong hệ thống'],
            ['C', 'Mật khẩu', 'Bắt buộc', 'Mật khẩu đăng nhập, tối thiểu 8 ký tự'],
            ['D', 'Vai trò', 'Bắt buộc', 'Mã vai trò hoặc tên vai trò (xem danh sách bên dưới)'],
            ['E', 'Trạng thái', 'Tùy chọn', 'hoat_dong (mặc định), khoa, hoặc ngung_hoat_dong'],
        ];
        
        $row = 4;
        foreach ($instructions as $instruction) {
            $col = 'A';
            foreach ($instruction as $value) {
                $instructionSheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Style instruction table header
        $instructionSheet->getStyle('A4:D4')->getFont()->setBold(true);
        $instructionSheet->getStyle('A4:D4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Add role list
        $instructionSheet->setCellValue('A11', 'DANH SÁCH VAI TRÒ:');
        $instructionSheet->getStyle('A11')->getFont()->setBold(true);
        
        $roles = [
            ['Mã vai trò', 'Tên vai trò', 'Mô tả'],
            ['admin1', 'Quản trị viên', 'Quản lý toàn bộ hệ thống, có quyền cao nhất'],
            ['truong_phong_dt', 'Trưởng phòng Đào tạo', 'Quản lý công tác đào tạo, duyệt điểm, xếp lớp'],
            ['nhan_vien_dt', 'Nhân viên Đào tạo', 'Hỗ trợ công tác đào tạo, quản lý học vụ'],
            ['giang_vien', 'Giảng viên', 'Giảng dạy, nhập điểm, điểm danh sinh viên'],
            ['sinh_vien', 'Sinh viên', 'Đăng ký môn học, xem điểm, xem thời khóa biểu'],
        ];
        
        $row = 12;
        foreach ($roles as $role) {
            $instructionSheet->setCellValue('A' . $row, $role[0]);
            $instructionSheet->setCellValue('B' . $row, $role[1]);
            $instructionSheet->setCellValue('C' . $row, $role[2]);
            $row++;
        }
        
        $instructionSheet->getStyle('A12:C12')->getFont()->setBold(true);
        $instructionSheet->getStyle('A12:C12')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Add status list
        $instructionSheet->setCellValue('A' . ($row + 2), 'DANH SÁCH TRẠNG THÁI:');
        $instructionSheet->getStyle('A' . ($row + 2))->getFont()->setBold(true);
        
        $statusList = [
            ['Mã trạng thái', 'Tên trạng thái', 'Mô tả'],
            ['hoat_dong', 'Hoạt động', 'Tài khoản đang hoạt động bình thường'],
            ['khoa', 'Khóa', 'Tài khoản bị khóa, không thể đăng nhập'],
            ['ngung_hoat_dong', 'Ngừng hoạt động', 'Tài khoản tạm ngừng hoạt động'],
        ];
        
        $statusRow = $row + 3;
        foreach ($statusList as $status) {
            $instructionSheet->setCellValue('A' . $statusRow, $status[0]);
            $instructionSheet->setCellValue('B' . $statusRow, $status[1]);
            $instructionSheet->setCellValue('C' . $statusRow, $status[2]);
            $statusRow++;
        }
        
        $instructionSheet->getStyle('A' . ($row + 3) . ':C' . ($row + 3))->getFont()->setBold(true);
        $instructionSheet->getStyle('A' . ($row + 3) . ':C' . ($row + 3))->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Add important notes
        $instructionSheet->setCellValue('A' . ($statusRow + 2), 'LƯU Ý QUAN TRỌNG:');
        $instructionSheet->getStyle('A' . ($statusRow + 2))->getFont()->setBold(true)->getColor()->setARGB('FFFF0000');
        
        $noteRow = $statusRow + 3;
        $notes = [
            '• Cột Vai trò và Trạng thái có dropdown list, vui lòng chọn từ danh sách',
            '• Email phải là duy nhất, không được trùng với email đã tồn tại',
            '• Mật khẩu phải có ít nhất 8 ký tự',
            '• Xóa dòng dữ liệu mẫu trước khi nhập dữ liệu thực',
            '• Chỉ nhập dữ liệu vào sheet "Danh sách Users", không chỉnh sửa các sheet khác',
        ];
        
        foreach ($notes as $note) {
            $instructionSheet->setCellValue('A' . $noteRow, $note);
            $noteRow++;
        }
        
        // Auto-size columns in instruction sheet
        foreach (range('A', 'C') as $col) {
            $instructionSheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set active sheet back to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="mau_import_users.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
