<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrataPlan — Em Manutenção</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0a0b14;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            text-align: center;
            max-width: 480px;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            color: #818cf8;
        }

        .badge {
            display: inline-block;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            color: #818cf8;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 99px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .divider {
            width: 48px;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #818cf8);
            border-radius: 2px;
            margin: 0 auto 32px;
        }

        .info-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 18px 22px;
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
        }

        .info-box strong { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.653-4.655m5.8-5.8 1.875-1.875a3.375 3.375 0 0 0-4.773-4.773l-1.875 1.875" />
            </svg>
        </div>

        <div class="badge">Manutenção</div>

        <h1>Estamos melhorando<br>o TrataPlan</h1>

        <div class="divider"></div>

        <p>O sistema está temporariamente indisponível para manutenção programada.<br>
        Voltamos em breve com novidades.</p>

        <div class="info-box">
            <strong>Precisa de ajuda?</strong><br>
            Entre em contato em <strong>contato@contato@trataplan.com.br</strong>
        </div>
    </div>
</body>
</html>
