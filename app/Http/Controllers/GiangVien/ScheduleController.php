<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LichHocChiTiet;
use App\Models\LichHocCoDinh;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleController extends Controller
{
    /**
     * Hiển thị lịch dạy cá nhân theo ngày/tuần/tháng
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (! $giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $period = $request->get('period', 'week'); // day|week|month
        $date = $request->get('date', Carbon::today()->toDateString());

        $start = Carbon::parse($date);
        switch ($period) {
            case 'day':
                $start = $start->copy()->startOfDay();
                $end = $start->copy()->endOfDay();
                break;
            case 'month':
                $start = $start->copy()->startOfMonth();
                $end = $start->copy()->endOfMonth();
                break;
            case 'week':
            default:
                // Thu 2 là bắt đầu tuần
                $start = $start->copy()->startOfWeek(Carbon::MONDAY);
                $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
                break;
        }

        // Lấy các buổi đã sinh trong bảng lich_hoc_chi_tiet
        $chiTiets = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc', 'giangVien', 'lichHocCoDinh'])
            ->whereBetween('ngay_hoc', [$start->toDateString(), $end->toDateString()])
            ->where('giang_vien_id', $giangVien->id)
            ->orderBy('ngay_hoc')
            ->orderBy('tiet_bat_dau')
            ->get();

        $events = [];

        foreach ($chiTiets as $ct) {
            $events[] = [
                'type' => 'chi_tiet',
                'date' => Carbon::parse($ct->ngay_hoc)->toDateString(),
                'weekday' => Carbon::parse($ct->ngay_hoc)->format('l'),
                'tiet_bat_dau' => $ct->tiet_bat_dau,
                'tiet_ket_thuc' => $ct->tiet_ket_thuc,
                'gio_bat_dau' => $ct->gio_bat_dau ? Carbon::parse($ct->gio_bat_dau)->format('H:i') : null,
                'gio_ket_thuc' => $ct->gio_ket_thuc ? Carbon::parse($ct->gio_ket_thuc)->format('H:i') : null,
                'phong' => $ct->phongHoc->ten_phong ?? null,
                'lop_hoc_phan' => $ct->lopHocPhan->ma_lop_hp ?? null,
                'ten_mon' => $ct->lopHocPhan->monHoc->ten_mon ?? 'N/A',
                'ma_mon' => $ct->lopHocPhan->monHoc->ma_mon ?? null,
                'link_online' => $ct->link_online,
                'source' => $ct,
            ];
        }

        // Lấy lịch cố định và phát sinh các buổi trong khoảng
        $coDinhs = LichHocCoDinh::with(['lopHocPhan.monHoc', 'phongHoc'])
            ->where('giang_vien_id', $giangVien->id)
            ->get();

        foreach ($coDinhs as $cd) {
            // thu_trong_tuan: 2..8 (2=Mon,..,8=Sun)
            $weekdayNumber = intval($cd->thu_trong_tuan);
            // Map to Carbon dayOfWeek: Monday=1 .. Sunday=0 (Carbon uses 0 for Sunday when dayOfWeekIso? We'll compute by name)
            // Simpler: iterate dates between start and end and match weekday strings
            $periodStart = $start->copy();
            while ($periodStart->lte($end)) {
                // Map Carbon isoWeekDay 1..7 (1=Mon .. 7=Sun) to our thu_trong_tuan where 2=Mon..8=Sun
                $iso = $periodStart->isoWeekday(); // 1..7
                $thu_val = $iso + 1; // 2..8
                if ($thu_val == $weekdayNumber) {
                    $events[] = [
                        'type' => 'co_dinh',
                        'date' => $periodStart->toDateString(),
                        'weekday' => $periodStart->format('l'),
                        'tiet_bat_dau' => $cd->tiet_bat_dau,
                        'tiet_ket_thuc' => $cd->tiet_ket_thuc,
                        'gio_bat_dau' => $cd->gio_bat_dau ? Carbon::parse($cd->gio_bat_dau)->format('H:i') : null,
                        'gio_ket_thuc' => $cd->gio_ket_thuc ? Carbon::parse($cd->gio_ket_thuc)->format('H:i') : null,
                        'phong' => $cd->phongHoc->ten_phong ?? null,
                        'lop_hoc_phan' => $cd->lopHocPhan->ma_lop_hp ?? null,
                        'ten_mon' => $cd->lopHocPhan->monHoc->ten_mon ?? 'N/A',
                        'ma_mon' => $cd->lopHocPhan->monHoc->ma_mon ?? null,
                        'link_online' => $cd->link_online,
                        'source' => $cd,
                    ];
                }
                $periodStart->addDay();
            }
        }

        // Loại bỏ các buổi trùng (ưu tiên 'chi_tiet' hơn 'co_dinh')
        $unique = [];
        foreach ($events as $ev) {
            $key = ($ev['date'] ?? '') . '|' . ($ev['tiet_bat_dau'] ?? '') . '|' . ($ev['tiet_ket_thuc'] ?? '') . '|' . ($ev['lop_hoc_phan'] ?? '');

            if (! isset($unique[$key])) {
                $unique[$key] = $ev;
                continue;
            }

            // Nếu đã có nhưng là lịch cố định và hiện tại là chi_tiet thì thay thế
            if (($unique[$key]['type'] ?? null) === 'co_dinh' && ($ev['type'] ?? null) === 'chi_tiet') {
                $unique[$key] = $ev;
            }
            // Nếu cả hai là cùng type thì giữ bản đầu tiên (không thay)
        }

        $events = array_values($unique);

        // Sắp xếp lại theo date và tiet_bat_dau
        usort($events, function ($a, $b) {
            if (($a['date'] ?? '') === ($b['date'] ?? '')) {
                return ($a['tiet_bat_dau'] ?? 0) <=> ($b['tiet_bat_dau'] ?? 0);
            }
            return strcmp($a['date'] ?? '', $b['date'] ?? '');
        });

        return view('giangvien.schedule.index', compact('events', 'period', 'date', 'start', 'end'));
    }

    /**
     * Xuất lịch (CSV)
     */
    public function export(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (! $giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $period = $request->get('period', 'week');
        $date = $request->get('date', Carbon::today()->toDateString());

        $start = Carbon::parse($date);
        switch ($period) {
            case 'day':
                $start = $start->copy()->startOfDay();
                $end = $start->copy()->endOfDay();
                break;
            case 'month':
                $start = $start->copy()->startOfMonth();
                $end = $start->copy()->endOfMonth();
                break;
            case 'week':
            default:
                $start = $start->copy()->startOfWeek(Carbon::MONDAY);
                $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
                break;
        }

        // Reuse index logic to build events quickly (call index code or replicate minimal parts)
        $chiTiets = LichHocChiTiet::with(['lopHocPhan', 'phongHoc'])
            ->whereBetween('ngay_hoc', [$start->toDateString(), $end->toDateString()])
            ->where('giang_vien_id', $giangVien->id)
            ->orderBy('ngay_hoc')
            ->orderBy('tiet_bat_dau')
            ->get();

        $events = [];
        foreach ($chiTiets as $ct) {
            $events[] = [
                'date' => Carbon::parse($ct->ngay_hoc)->toDateString(),
                'weekday' => Carbon::parse($ct->ngay_hoc)->format('l'),
                'tiet' => $ct->tiet_bat_dau . '-' . $ct->tiet_ket_thuc,
                'gio_bat_dau' => $ct->gio_bat_dau ? Carbon::parse($ct->gio_bat_dau)->format('H:i') : null,
                'gio_ket_thuc' => $ct->gio_ket_thuc ? Carbon::parse($ct->gio_ket_thuc)->format('H:i') : null,
                'phong' => $ct->phongHoc->ten_phong ?? null,
                'lop_hoc_phan' => $ct->lopHocPhan->ma_lop_hp ?? null,
                'link_online' => $ct->link_online,
            ];
        }

        // Thêm từ lich co dinh
        $coDinhs = LichHocCoDinh::with(['lopHocPhan', 'phongHoc'])
            ->where('giang_vien_id', $giangVien->id)
            ->get();

        foreach ($coDinhs as $cd) {
            $periodStart = $start->copy();
            while ($periodStart->lte($end)) {
                $iso = $periodStart->isoWeekday();
                $thu_val = $iso + 1;
                if ($thu_val == intval($cd->thu_trong_tuan)) {
                    $events[] = [
                        'date' => $periodStart->toDateString(),
                        'weekday' => $periodStart->format('l'),
                        'tiet' => $cd->tiet_bat_dau . '-' . $cd->tiet_ket_thuc,
                        'gio_bat_dau' => $cd->gio_bat_dau ? Carbon::parse($cd->gio_bat_dau)->format('H:i') : null,
                        'gio_ket_thuc' => $cd->gio_ket_thuc ? Carbon::parse($cd->gio_ket_thuc)->format('H:i') : null,
                        'phong' => $cd->phongHoc->ten_phong ?? null,
                        'lop_hoc_phan' => $cd->lopHocPhan->ma_lop_hp ?? null,
                        'link_online' => $cd->link_online,
                    ];
                }
                $periodStart->addDay();
            }
        }

        usort($events, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        $fileName = sprintf('lich_day_%s_%s.csv', $period, $start->toDateString());

        $response = new StreamedResponse(function () use ($events) {
            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['Ngày', 'Thứ', 'Tiết', 'Giờ bắt đầu', 'Giờ kết thúc', 'Phòng', 'Lớp HP', 'Link online']);

            foreach ($events as $row) {
                fputcsv($handle, [
                    $row['date'] ?? '',
                    $row['weekday'] ?? '',
                    $row['tiet'] ?? ($row['tiet_bat_dau'] . '-' . $row['tiet_ket_thuc'] ?? ''),
                    $row['gio_bat_dau'] ?? '',
                    $row['gio_ket_thuc'] ?? '',
                    $row['phong'] ?? '',
                    $row['lop_hoc_phan'] ?? '',
                    $row['link_online'] ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
