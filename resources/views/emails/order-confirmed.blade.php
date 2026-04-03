<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido confirmado</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: #f9fafb; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 520px; margin: 0 auto; background: white;
                     border-radius: 16px; overflow: hidden;
                     border: 1px solid #f3f4f6; }
        .header { background: #4f46e5; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: #c7d2fe; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .ticket { background: #f5f3ff; border: 2px dashed #a5b4fc;
                  border-radius: 12px; padding: 20px; margin: 20px 0; text-align: center; }
        .ticket-code { font-size: 28px; font-weight: bold; letter-spacing: 6px;
                       color: #4f46e5; font-family: monospace; }
        .ticket-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .info-row { display: flex; justify-content: space-between;
                    padding: 10px 0; border-bottom: 1px solid #f3f4f6;
                    font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #1f2937; }
        .btn { display: block; background: #4f46e5; color: white; text-decoration: none;
               padding: 14px 24px; border-radius: 12px; text-align: center;
               font-weight: 600; margin: 24px 0; font-size: 15px; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center;
                  font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎉 Pedido confirmado!</h1>
        <p>
            Sua {{ $order->event->ticketLabel() }} está confirmada
        </p>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $order->user->name }}</strong>!</p>
        <p style="color: #4b5563; font-size: 14px;">
            Seu pagamento foi confirmado e
            {{ $order->event->ticketLabel(true) }} estão prontos.
        </p>

        {{-- Info do evento --}}
        <div style="margin: 20px 0;">
            <div class="info-row">
                <span class="info-label">Evento</span>
                <span class="info-value">{{ $order->event->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Data</span>
                <span class="info-value">
                    {{ $order->event->starts_at->translatedFormat('d \d\e M \d\e Y · H:i') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Local</span>
                <span class="info-value">
                    {{ $order->event->venue_name }}, {{ $order->event->city }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Pedido</span>
                <span class="info-value">{{ $order->reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total pago</span>
                <span class="info-value">{{ $order->formattedTotal() }}</span>
            </div>
        </div>

        {{-- Códigos dos ingressos --}}
        @foreach($order->items as $item)
            <div class="ticket">
                <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;">
                    {{ $item->quantity }}× {{ $item->ticketType->name }}
                </div>
                <div class="ticket-code">{{ $item->ticket_code }}</div>
                <div class="ticket-label">Apresente este código na entrada</div>
            </div>
        @endforeach

        <a href="{{ route('tickets.my') }}" class="btn">
            Meus {{ $order->event->ticketLabel(true) }}
        </a>

        <p style="font-size: 13px; color: #6b7280; text-align: center;">
            Salve este e-mail. Você precisará do código na entrada do evento.
        </p>
    </div>

    <div class="footer">
        Bora Ali — Plataforma de Eventos Regionais<br>
        Você recebeu este e-mail porque realizou uma compra em nossa plataforma.
    </div>
</div>
</body>
</html>