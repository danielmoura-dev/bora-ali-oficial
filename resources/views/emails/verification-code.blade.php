<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme seu e-mail</title>
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
        .code-box { background: #f5f3ff; border: 2px dashed #a5b4fc;
                    border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center; }
        .code { font-size: 40px; font-weight: bold; letter-spacing: 10px;
                color: #4f46e5; font-family: monospace; }
        .code-label { font-size: 12px; color: #6b7280; margin-top: 8px; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center;
                  font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Confirme seu e-mail</h1>
        <p>Só mais um passo para entrar no Bora Ali</p>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $user->name }}</strong>!</p>
        <p style="color: #4b5563; font-size: 14px;">
            Use o código abaixo para confirmar seu endereço de e-mail.
            Ele expira em <strong>15 minutos</strong>.
        </p>

        <div class="code-box">
            <div class="code">{{ $user->getAttributes()['verification_code'] }}</div>
            <div class="code-label">Código de verificação</div>
        </div>

        <p style="font-size: 13px; color: #6b7280; text-align: center;">
            Se você não criou uma conta no Bora Ali, ignore este e-mail.
        </p>
    </div>

    <div class="footer">
        Bora Ali — Plataforma de Eventos Regionais<br>
        Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
