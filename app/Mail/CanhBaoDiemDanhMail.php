<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CanhBaoDiemDanhMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sinhVien;
    public $lopHocPhan;
    public $thongKe;

    /**
     * Create a new message instance.
     */
    public function __construct($sinhVien, $lopHocPhan, $thongKe)
    {
        $this->sinhVien = $sinhVien;
        $this->lopHocPhan = $lopHocPhan;
        $this->thongKe = $thongKe;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Cảnh báo chuyên cần - ' . $this->lopHocPhan->ma_lop_hp,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.canh-bao-diem-danh',
            with: [
                'sinhVien' => $this->sinhVien,
                'lopHocPhan' => $this->lopHocPhan,
                'thongKe' => $this->thongKe,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
