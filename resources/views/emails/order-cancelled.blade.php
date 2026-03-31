<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido cancelado</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: #f9fafb; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 520px; margin: 0 auto; background: white;
                     border-radius: 16px; overflow: hidden; border: 1px solid #f3f4f6; }
        .header { background: #dc2626; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: #fecaca; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .info-row { display: flex; justify-content: space-between;
                    padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #1f2937; }
        .notice { background: #fef3c7; border: 1px solid #fde68a;
                  border-radius: 12px; padding: 16px; margin: 20px 0; }
        .notice p { margin: 0; font-size: 13px; color: #92400e; }
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
        <h1>❌ Pedido cancelado</h1>
        <p>Seu pedido foi cancelado</p>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $order->user->name }}</strong>.</p>
        <p style="color: #4b5563; font-size: 14px;">
            Seu pedido para o evento abaixo foi cancelado.
        </p>

        <div style="margin: 20px 0;">
            <div class="info-row">
                <span class="info-label">Evento</span>
                <span class="info-value">{{ $order->event->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pedido</span>
                <span class="info-value">{{ $order->reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Valor</span>
                <span class="info-value">{{ $order->formattedTotal() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value" style="color: #dc2626;">
                    {{ $order->status === 'refunded' ? 'Reembolsado' : 'Cancelado' }}
                </span>
            </div>
        </div>

        <div class="notice">
            <p>
                @if($order->status === 'refunded')
                    💰 O reembolso será processado em até 10 dias úteis,
                    dependendo do seu banco ou operadora de cartão.
                @else
                    Se você pagou via Pix, entre em contato com o organizador
                    para solicitar o reembolso.
                @endif
            </p>
        </div>

        <a href="{{ route('home') }}" class="btn">
            Ver outros eventos
        </a>
    </div>

    <div class="footer">
        Bora Ali — Plataforma de Eventos Regionais
    </div>
</div>
</body>
</html>