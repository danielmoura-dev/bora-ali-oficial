<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: #f9fafb; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 480px; margin: 0 auto; background: white;
                     border-radius: 16px; overflow: hidden;
                     border: 1px solid #f3f4f6; }
        .header { background: #4f46e5; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: #c7d2fe; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .btn { display: block; width: fit-content; margin: 24px auto;
               background: #4f46e5; color: white; text-decoration: none;
               padding: 14px 32px; border-radius: 12px; font-weight: 600;
               font-size: 15px; text-align: center; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center;
                  font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Redefinir senha</h1>
        <p>Recebemos uma solicitação para a sua conta</p>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $userName }}</strong>!</p>
        <p style="color: #4b5563; font-size: 14px;">
            Clique no botão abaixo para redefinir sua senha.
            O link expira em <strong>1 hora</strong>.
        </p>

        <a href="{{ $resetUrl }}" class="btn">Redefinir minha senha</a>

        <p style="font-size: 13px; color: #6b7280;">
            Se o botão não funcionar, copie e cole este link no seu navegador:
        </p>
        <p style="font-size: 12px; color: #6b7280; word-break: break-all;">
            {{ $resetUrl }}
        </p>

        <p style="font-size: 13px; color: #6b7280; margin-top: 24px; text-align: center;">
            Se você não solicitou a redefinição de senha, ignore este e-mail.
            Sua senha permanece a mesma.
        </p>
    </div>

    <div class="footer">
        Bora Ali — Plataforma de Eventos Regionais<br>
        Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
