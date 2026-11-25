<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->booking_code }}</title>
    <style>
        @page { margin: 36px 40px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #03121A; font-size: 12px; line-height: 1.55; }
        h1, h2, h3, p { margin: 0; }
        .brand { font-size: 20px; font-weight: bold; color: #0770CD; letter-spacing: -0.4px; }
        .brand span { color: #F96D01; }
        .muted { color: #687176; }
        .header { width: 100%; border-bottom: 2px solid #0194F3; padding-bottom: 16px; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.6px; }
        .paid { background: #E7F7EF; color: #05A569; }
        .pending { background: #FFF4E5; color: #B25A00; }
        .cancelled { background: #FDECEC; color: #C0392B; }
        .section { margin-top: 22px; }
        .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.8px; color: #687176; }
        .box { background: #F7F9FA; border-radius: 8px; padding: 12px 14px; }
        table { width: 100%; border-collapse: collapse; }
        .items th { background: #F7F9FA; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.6px; color: #687176; padding: 8px 10px; }
        .items td { padding: 10px; border-bottom: 1px solid #E6EAED; vertical-align: top; }
        .totals td { padding: 6px 0; }
        .totals .grand { font-size: 16px; font-weight: bold; color: #C45300; }
        .footer { margin-top: 30px; border-top: 1px solid #E6EAED; padding-top: 12px; font-size: 10px; color: #687176; }
        .note { background: #FFF1E5; border-radius: 8px; padding: 10px 12px; color: #8A3D00; font-size: 11px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <p class="brand">nusa<span>stay</span></p>
                <p class="muted" style="margin-top:4px;">Hotels across Indonesia</p>
                <p class="muted">hello@nusastay.test · +62 21 5000 1234</p>
            </td>
            <td class="right">
                <h1 style="font-size:22px;">INVOICE</h1>
                <p style="margin-top:4px;"><strong>{{ $booking->booking_code }}</strong></p>
                <p class="muted">Issued {{ $booking->created_at->format('j F Y') }}</p>
                <p style="margin-top:8px;">
                    @php
                        $badge = match ($booking->status) {
                            'paid', 'completed' => 'paid',
                            'cancelled' => 'cancelled',
                            default => 'pending',
                        };
                    @endphp
                    <span class="badge {{ $badge }}">{{ ucfirst($booking->status) }}</span>
                </p>
            </td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td style="width:50%; padding-right:10px;">
                <p class="label">Billed to</p>
                <div class="box" style="margin-top:6px;">
                    <p><strong>{{ $booking->customer_name }}</strong></p>
                    <p class="muted">{{ $booking->user?->email }}</p>
                    <p class="muted">{{ $booking->user?->phone }}</p>
                </div>
            </td>
            <td style="width:50%; padding-left:10px;">
                <p class="label">Property</p>
                <div class="box" style="margin-top:6px;">
                    <p><strong>{{ $booking->room?->hotel?->name }}</strong></p>
                    <p class="muted">{{ $booking->room?->hotel?->address }}</p>
                    <p class="muted">{{ $booking->room?->hotel?->city }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td style="width:33%;"><p class="label">Check-in</p><p><strong>{{ $booking->check_in_date->format('D, j M Y') }}</strong></p><p class="muted">From 14:00</p></td>
            <td style="width:33%;"><p class="label">Check-out</p><p><strong>{{ $booking->check_out_date->format('D, j M Y') }}</strong></p><p class="muted">Until 12:00</p></td>
            <td style="width:34%;"><p class="label">Guests</p><p><strong>{{ $booking->guest_count }} {{ $booking->guest_count === 1 ? 'guest' : 'guests' }}</strong></p><p class="muted">{{ $booking->total_nights }} {{ $booking->total_nights === 1 ? 'night' : 'nights' }}</p></td>
        </tr>
    </table>

    <table class="items section">
        <thead>
            <tr>
                <th style="width:52%;">Description</th>
                <th style="width:16%;" class="right">Nights</th>
                <th style="width:16%;" class="right">Rate</th>
                <th style="width:16%;" class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $booking->room?->roomType?->name }}</strong><br>
                    <span class="muted">Room {{ $booking->room?->room_number }} · {{ $booking->room?->hotel?->name }}</span>
                </td>
                <td class="right">{{ $booking->total_nights }}</td>
                <td class="right">Rp {{ number_format((float) $booking->price_per_night, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format((float) $booking->price_per_night * $booking->total_nights, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="section">
        <tr>
            <td style="width:55%; vertical-align:top;">
                <p class="label">Payment</p>
                <div style="margin-top:6px;">
                    @forelse ($booking->payments as $payment)
                        <p style="margin-bottom:4px;">
                            {{ $payment->method_label }} · {{ $payment->reference_code }}
                            <span class="muted">({{ ucfirst($payment->status) }})</span>
                        </p>
                    @empty
                        <p class="muted">No payment recorded.</p>
                    @endforelse
                </div>
                @if ($booking->notes)
                    <p class="label" style="margin-top:14px;">Notes</p>
                    <p class="muted" style="margin-top:4px;">{{ $booking->notes }}</p>
                @endif
            </td>
            <td style="width:45%;">
                <table class="totals">
                    <tr>
                        <td class="muted">Subtotal</td>
                        <td class="right">Rp {{ number_format((float) $booking->price_per_night * $booking->total_nights, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="muted">Tax &amp; service</td>
                        <td class="right">Rp {{ number_format((float) $booking->total_price - (float) $booking->price_per_night * $booking->total_nights, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-top:10px;"><strong>Total</strong></td>
                        <td class="right grand" style="padding-top:10px;">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($booking->status === 'pending')
        <p class="note section">
            This booking is awaiting payment. The room is held until
            {{ $booking->expires_at?->format('j M Y H:i') ?? 'the hold expires' }} WIB.
        </p>
    @elseif ($booking->status === 'cancelled')
        <p class="note section">
            This booking was cancelled{{ $booking->cancelled_at ? ' on '.$booking->cancelled_at->format('j M Y') : '' }}.
            Reason: {{ $booking->cancel_reason ?: 'not specified' }}.
        </p>
    @endif

    <div class="footer">
        <p><strong>NusaStay</strong> · This invoice is generated automatically and is valid without a signature.</p>
        <p style="margin-top:4px;">Questions? Reply to your WhatsApp confirmation or email hello@nusastay.test.</p>
    </div>
</body>
</html>
