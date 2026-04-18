<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
        $this->order->loadMissing(['orderProducts.product']);
    }

    public function envelope(): Envelope
    {
        $customerFirstName = $this->order->customer_first_name;

        return new Envelope(
            subject: "Order #{$this->order->id} confirmed — thank you, {$customerFirstName}!"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.order_place_mail',
            with: [
                'order' => $this->order,
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
