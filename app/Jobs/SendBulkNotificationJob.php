<?php

namespace App\Jobs;

use App\Models\NguoiNhanThongBao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * ID của thông báo
     *
     * @var int
     */
    protected $thongBaoId;

    /**
     * Danh sách user IDs cần nhận thông báo
     *
     * @var array
     */
    protected $nguoiNhanIds;

    /**
     * Create a new job instance.
     */
    public function __construct(int $thongBaoId, array $nguoiNhanIds)
    {
        $this->thongBaoId = $thongBaoId;
        $this->nguoiNhanIds = $nguoiNhanIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $totalRecipients = count($this->nguoiNhanIds);

        Log::info("Bắt đầu gửi thông báo hàng loạt", [
            'thong_bao_id' => $this->thongBaoId,
            'so_nguoi_nhan' => $totalRecipients,
        ]);

        try {
            // Batch insert để tối ưu performance
            $now = now();
            $data = [];

            foreach ($this->nguoiNhanIds as $userId) {
                $data[] = [
                    'thong_bao_id' => $this->thongBaoId,
                    'nguoi_nhan_id' => $userId,
                    'da_doc' => false,
                    'da_gui_email' => false,
                    'da_gui_sms' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Chia nhỏ thành chunks 500 records để tránh query quá lớn
            $chunks = array_chunk($data, 500);
            $totalInserted = 0;

            foreach ($chunks as $index => $chunk) {
                NguoiNhanThongBao::insert($chunk);
                $totalInserted += count($chunk);

                // Log progress
                if (($index + 1) % 10 === 0) {
                    Log::info("Đã xử lý {$totalInserted}/{$totalRecipients} người nhận");
                }
            }

            $duration = round(microtime(true) - $startTime, 2);

            Log::info("Hoàn thành gửi thông báo hàng loạt", [
                'thong_bao_id' => $this->thongBaoId,
                'so_nguoi_nhan' => $totalInserted,
                'thoi_gian' => "{$duration}s",
            ]);
        } catch (\Exception $e) {
            Log::error("Lỗi khi gửi thông báo hàng loạt", [
                'thong_bao_id' => $this->thongBaoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Throw exception để job được retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job gửi thông báo hàng loạt thất bại sau {$this->tries} lần thử", [
            'thong_bao_id' => $this->thongBaoId,
            'so_nguoi_nhan' => count($this->nguoiNhanIds),
            'error' => $exception->getMessage(),
        ]);
    }
}

