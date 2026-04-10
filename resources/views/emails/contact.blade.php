<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #1c1c19; background: #fcf9f4; padding: 24px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 32px; }
        h1 { color: #012d1d; font-size: 20px; border-bottom: 2px solid #6a5e33; padding-bottom: 12px; }
        .field { margin: 16px 0; }
        .label { font-weight: bold; color: #77574d; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; }
        .value { margin-top: 4px; color: #1c1c19; }
        .message { background: #f6f3ee; border-left: 4px solid #012d1d; padding: 16px; border-radius: 4px; margin-top: 8px; }
        .footer { margin-top: 32px; font-size: 12px; color: #717973; border-top: 1px solid #e5e2dd; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nova Mensagem de Contato – Trataplan</h1>
        <div class="field">
            <div class="label">Nome</div>
            <div class="value">{{ $contactMessage->name }}</div>
        </div>
        <div class="field">
            <div class="label">E-mail</div>
            <div class="value"><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></div>
        </div>
        @if($contactMessage->phone)
        <div class="field">
            <div class="label">Telefone</div>
            <div class="value">{{ $contactMessage->phone }}</div>
        </div>
        @endif
        @if($contactMessage->subject)
        <div class="field">
            <div class="label">Assunto</div>
            <div class="value">{{ $contactMessage->subject }}</div>
        </div>
        @endif
        <div class="field">
            <div class="label">Mensagem</div>
            <div class="message">{{ $contactMessage->message }}</div>
        </div>
        <div class="field">
            <div class="label">Idioma</div>
            <div class="value">{{ strtoupper($contactMessage->locale) }}</div>
        </div>
        <div class="field">
            <div class="label">Recebida em</div>
            <div class="value">{{ $contactMessage->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="footer">
            Esta mensagem foi enviada através do formulário de contato em trataplan.com.br
        </div>
    </div>
</body>
</html>
