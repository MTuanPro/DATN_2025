<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BaoCaoSinhVienYeuMail extends Mailable
{
    use Queueable, SerializesModels;

    public $giangVien;
    public $danhSachSinhVien;

    /**
     * Create a new message instance.
     */
    public function __construct($giangVien, $danhSachSinhVien)
    {
        $this->giangVien = $giangVien;
        $this->danhSachSinhVien = $danhSachSinhVien;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Báo cáo sinh viên chuyên cần yếu - Lớp chủ nhiệm',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bao-cao-sinh-vien-yeu',
            with: [
                'giangVien' => $this->giangVien,
                'danhSachSinhVien' => $this->danhSachSinhVien,
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
