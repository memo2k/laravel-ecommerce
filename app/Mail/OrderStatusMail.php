<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $previousStatus = null,
    ) {
        $this->order->loadMissing(['orderProducts.product']);
    }

    public function envelope(): Envelope
    {
        $status = (string) $this->order->status;

        $subject = match ($status) {
            'Pending'    => "Order #{$this->order->id} received",
            'Processing' => "Order #{$this->order->id} is being processed",
            'Shipped'    => "Order #{$this->order->id} is on its way",
            'Delivered'  => "Order #{$this->order->id} has been delivered",
            'Cancelled'  => "Order #{$this->order->id} has been cancelled",
            default      => "Order #{$this->order->id} status update",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.order_status_mail',
            with: [
                'order' => $this->order,
                'previousStatus' => $this->previousStatus,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
