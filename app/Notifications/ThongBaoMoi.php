<?php

namespace App\Notifications;

use App\Models\ThongBao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification cho thông báo mới
 * Sử dụng Laravel Notification channels: database, mail, broadcast
 */
class ThongBaoMoi extends Notification implements ShouldQueue
{
    use Queueable;

    public $thongBao;
    public $noiDungNgan;

    /**
     * Create a new notification instance.
     */
    public function __construct(ThongBao $thongBao, $noiDungNgan = null)
    {
        $this->thongBao = $thongBao;
        $this->noiDungNgan = $noiDungNgan ?? mb_substr($thongBao->noi_dung, 0, 100);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Thêm broadcast nếu cần realtime
        if ($this->thongBao->gui_web_notification ?? true) {
            $channels[] = 'broadcast';
        }
        
        // Mail sẽ gửi riêng qua NotificationService
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->getNotificationUrl($notifiable);
        
        return (new MailMessage)
            ->subject($this->thongBao->tieu_de)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line($this->noiDungNgan)
            ->action('Xem chi tiết', $url)
            ->line('Cảm ơn bạn đã sử dụng hệ thống!');
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'thong_bao_id' => $this->thongBao->id,
            'tieu_de' => $this->thongBao->tieu_de,
            'noi_dung_ngan' => $this->noiDungNgan,
            'loai_thong_bao' => $this->thongBao->loai_thong_bao,
            'muc_do_quan_trong' => $this->thongBao->muc_do_quan_trong,
            'anh_dai_dien' => $this->thongBao->anh_dai_dien,
            'url' => $this->getNotificationUrl($notifiable),
            'nguoi_gui_id' => $this->thongBao->nguoi_gui_id,
            'nguoi_gui_name' => $this->thongBao->nguoiGui->name ?? 'Hệ thống',
            'ngay_gui' => $this->thongBao->ngay_gui?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'thong_bao_id' => $this->thongBao->id,
            'tieu_de' => $this->thongBao->tieu_de,
            'noi_dung_ngan' => $this->noiDungNgan,
            'loai_thong_bao' => $this->thongBao->loai_thong_bao,
            'muc_do_quan_trong' => $this->thongBao->muc_do_quan_trong,
            'anh_dai_dien' => $this->thongBao->anh_dai_dien,
            'url' => $this->getNotificationUrl($notifiable),
            'ngay_gui' => $this->thongBao->ngay_gui?->format('Y-m-d H:i:s'),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get notification URL based on user role
     */
    private function getNotificationUrl($notifiable): string
    {
        $role = $notifiable->role ?? 'sinh_vien';
        
        switch ($role) {
            case 'admin':
                return route('admin.thong-bao.show', $this->thongBao->id);
            case 'dao_tao':
                return route('daotao.thong-bao.show', $this->thongBao->id);
            case 'giang_vien':
                return route('giangvien.thong-bao.show', $this->thongBao->id);
            case 'sinh_vien':
            default:
                return route('sinhvien.thong-bao.show', $this->thongBao->id);
        }
    }

    /**
     * The channels the notification should broadcast on.
     */
    public function broadcastOn(): array
    {
        return ['user.' . $this->thongBao->id];
    }
}
