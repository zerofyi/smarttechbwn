<?php

namespace App\Mail;

use App\Models\UserOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UserOrder $order,
        public string $status,
        public ?string $note = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SmartTech — Order '.$this->order->order_number.' '.$this->status,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status',
            with: [
                'order' => $this->order,
                'status' => $this->status,
                'note' => $this->note,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
