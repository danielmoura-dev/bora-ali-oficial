<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete de evento</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: #f9fafb; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 520px; margin: 0 auto; background: white;
                     border-radius: 16px; overflow: hidden;
                     border: 1px solid #f3f4f6; }
        .header { background: linear-gradient(135deg, #6366f1, #8b5cf6);
                  padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: #e0e7ff; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .countdown { background: #fef3c7; border: 1px solid #fde68a;
                     border-radius: 12px; padding: 16px; text-align: center;
                     margin: 20px 0; }
        .countdown p { margin: 0; font-size: 18px; font-weight: bold; color: #92400e; }
        .info-row { display: flex; justify-content: space-between;
                    padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #1f2937; }
        .ticket { background: #f5f3ff; border: 2px dashed #a5b4fc;
                  border-radius: 12px; padding: 16px; margin: 16px 0; text-align: center; }
        .ticket-code { font-size: 24px; font-weight: bold; letter-spacing: 6px;
                       color: #4f46e5; font-family: monospace; }
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
        <h1>🎉 Seu evento é amanhã!</h1>
        <p>Não esqueça de levar seus ingressos</p>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $order->user->name }}</strong>!</p>

        <div class="countdown">
            <p>⏰ {{ $event->title }} começa amanhã!</p>
        </div>

        <div style="margin: 20px 0;">
            <div class="info-row">
                <span class="info-label">📅 Data e hora</span>
                <span class="info-value">
                    {{ $event->starts_at->translatedFormat('d \d\e M · H:i') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">📍 Local</span>
                <span class="info-value">{{ $event->venue_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">🗺️ Endereço</span>
                <span class="info-value">
                    {{ $event->venue_address }}, {{ $event->city }}/{{ $event->state }}
                </span>
            </div>
        </div>

        <p style="font-size: 14px; color: #4b5563; font-weight: 600;">
            Seus ingressos:
        </p>

        @foreach($order->items as $item)
            <div class="ticket">
                <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">
                    {{ $item->quantity }}× {{ $item->ticketType->name }}
                </div>
                <div class="ticket-code">{{ $item->ticket_code }}</div>
            </div>
        @endforeach

        <a href="{{ route('tickets.my') }}" class="btn">
            Ver meus ingressos
        </a>
    </div>

    <div class="footer">
        Bora Ali — Plataforma de Eventos Regionais<br>
        Você recebeu este lembrete porque tem ingressos para este evento.
    </div>
</div>
</body>
</html>