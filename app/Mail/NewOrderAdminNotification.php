<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public array $items;

    public array $customerFields;

    public function __construct(
        public Order $order,
        public string $completedUrl,
        public string $cancelledUrl,
    ) {
        $this->items = $order->items ?: [[
            'name' => $order->variant_name,
            'option_name' => $order->option_name,
            'unit_price' => (float) $order->total,
            'quantity' => 1,
            'subtotal' => (float) $order->total,
        ]];

        $this->customerFields = collect($order->customer_data ?? [])
            ->reject(function (array $field) {
                $privateData = implode(' ', [
                    $field['type'] ?? '',
                    $field['field_key'] ?? '',
                    $field['label'] ?? '',
                ]);

                return preg_match('/password|backup[\s_-]*code/i', $privateData) === 1;
            })
            ->values()
            ->all();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan baru ' . $this->order->order_number . ' - Veluna Store',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.new-order',
        );
    }
}
