@php
    $currency = fn ($n) => '$' . number_format((float) $n, 2);
    $customerName = trim(implode(' ', array_filter([
        $order->customer_first_name,
        $order->customer_last_name,
    ])));
    $placedAt = $order->created_at?->timezone(config('app.timezone'))->format('M j, Y \a\t g:i A');
    $cityLine = trim(implode(', ', array_filter([$order->city, $order->state, $order->zip])));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->id }} confirmed</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f4f5;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:8px;">
                    <tr>
                        <td style="padding:28px 28px 8px;">
                            <p style="margin:0 0 6px;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">{{ config('app.name') }}</p>
                            <h1 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#0f172a;">Order #{{ $order->id }} confirmed</h1>
                            <p style="margin:0;font-size:15px;line-height:1.55;color:#334155;">
                                @if($customerName !== '')
                                    Hi {{ $customerName }}, thanks for shopping with us. We've received your order and will send another update when it ships.
                                @else
                                    Thanks for shopping with us. We've received your order and will send another update when it ships.
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px;">
                            <p style="margin:0;font-size:14px;color:#64748b;">
                                Status: <strong style="color:#0f172a;">{{ $order->status }}</strong>
                                @if($placedAt)
                                    <span style="color:#94a3b8;"> · Placed {{ $placedAt }}</span>
                                @endif
                            </p>
                        </td>
                    </tr>

                    @if($order->orderProducts && $order->orderProducts->count())
                        <tr>
                            <td style="padding:0 28px 8px;">
                                <h2 style="margin:0 0 8px;font-size:14px;font-weight:700;color:#0f172a;">Items</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 28px 20px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    @foreach($order->orderProducts as $line)
                                        @php
                                            $title = $line->product?->name ?? 'Product #' . $line->product_id;
                                        @endphp
                                        <tr>
                                            <td style="padding:8px 0;border-top:1px solid #e2e8f0;font-size:14px;color:#334155;">
                                                {{ $title }}
                                                <span style="color:#94a3b8;">× {{ $line->quantity }}</span>
                                            </td>
                                            <td align="right" style="padding:8px 0;border-top:1px solid #e2e8f0;font-size:14px;color:#0f172a;white-space:nowrap;">
                                                {{ $currency($line->total) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td style="padding:10px 0 0;border-top:2px solid #0f172a;font-size:15px;font-weight:700;color:#0f172a;">Total</td>
                                        <td align="right" style="padding:10px 0 0;border-top:2px solid #0f172a;font-size:15px;font-weight:700;color:#0f172a;white-space:nowrap;">
                                            {{ $currency($order->total_amount) }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:0 28px 20px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="50%" valign="top" style="padding-right:10px;font-size:13px;line-height:1.55;color:#334155;">
                                        <strong style="display:block;margin-bottom:4px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;">Delivery</strong>
                                        @if(!empty($order->delivery_address))
                                            {{ $order->delivery_address }}<br>
                                        @endif
                                        @if($cityLine !== '')
                                            {{ $cityLine }}<br>
                                        @endif
                                        @if(!empty($order->country))
                                            {{ $order->country }}
                                        @endif
                                    </td>
                                    <td width="50%" valign="top" style="padding-left:10px;font-size:13px;line-height:1.55;color:#334155;">
                                        <strong style="display:block;margin-bottom:4px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;">Payment &amp; contact</strong>
                                        @if(!empty($order->payment_method))
                                            {{ $order->payment_method }}<br>
                                        @endif
                                        @if(!empty($order->customer_phone))
                                            {{ $order->customer_phone }}<br>
                                        @endif
                                        @if(!empty($order->customer_email))
                                            {{ $order->customer_email }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if(!empty($order->customer_notes))
                        <tr>
                            <td style="padding:0 28px 20px;">
                                <p style="margin:0;font-size:13px;line-height:1.55;color:#64748b;">
                                    <strong style="color:#334155;">Your note:</strong> {{ $order->customer_notes }}
                                </p>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:16px 28px 28px;border-top:1px solid #e2e8f0;">
                            <p style="margin:0;font-size:13px;line-height:1.55;color:#64748b;">
                                Shop Details: <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-size:12px;color:#94a3b8;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
