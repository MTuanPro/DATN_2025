<?php

namespace App\Mail;

use App\Models\CanhBaoHocVu;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CanhBaoHocVuMail extends Mailable
{
    use Queueable, SerializesModels;

    public $canhBao;

    /**
     * Create a new message instance.
     */
    public function __construct(CanhBaoHocVu $canhBao)
    {
        $this->canhBao = $canhBao;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $mucDoText = [
            'canh_cao' => 'CẢNH CÁO',
            'dinh_chi' => 'ĐÌNH CHỈ',
            'buoc_thoi_hoc' => 'BUỘC THÔI HỌC',
        ];

        return new Envelope(
            subject: '⚠️ Cảnh Báo Học Vụ: ' . ($mucDoText[$this->canhBao->muc_do] ?? 'CẢNH BÁO'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.canh-bao-hoc-vu',
            with: [
                'canhBao' => $this->canhBao,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
