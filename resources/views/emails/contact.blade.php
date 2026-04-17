<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova mensagem – Trataplan</title>
</head>
<body style="margin:0;padding:0;background-color:#f0ede8;font-family:Arial,Helvetica,sans-serif;">

    <!-- Wrapper -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
           style="background-color:#f0ede8;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                       style="max-width:600px;">

                    <!-- ── CABEÇALHO ── -->
                    <tr>
                        <td style="background-color:#012d1d;border-radius:16px 16px 0 0;padding:36px 40px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <!-- Logo text mark -->
                                        <div style="font-size:22px;font-weight:900;letter-spacing:0.08em;color:#ffffff;
                                                    text-transform:uppercase;font-family:Arial,sans-serif;">
                                            TRATAPLAN
                                        </div>
                                        <div style="font-size:11px;color:rgba(255,255,255,0.45);letter-spacing:0.2em;
                                                    text-transform:uppercase;margin-top:2px;font-family:Arial,sans-serif;">
                                            Tratamento de Madeira
                                        </div>
                                    </td>
                                    <td align="right" valign="middle">
                                        @if($contactMessage->subject === 'orcamento')
                                        <span style="display:inline-block;background-color:rgba(251,191,36,0.18);
                                                     color:#fbbf24;font-size:10px;font-weight:700;
                                                     letter-spacing:0.2em;text-transform:uppercase;
                                                     padding:5px 12px;border-radius:99px;font-family:Arial,sans-serif;">
                                            Orçamento
                                        </span>
                                        @else
                                        <span style="display:inline-block;background-color:rgba(255,255,255,0.10);
                                                     color:rgba(255,255,255,0.7);font-size:10px;font-weight:700;
                                                     letter-spacing:0.2em;text-transform:uppercase;
                                                     padding:5px 12px;border-radius:99px;font-family:Arial,sans-serif;">
                                            Contato
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Título -->
                            <div style="margin-top:28px;">
                                <div style="font-size:26px;font-weight:900;color:#ffffff;line-height:1.2;font-family:Arial,sans-serif;">
                                    @if($contactMessage->subject === 'orcamento')
                                        Nova Solicitação de Orçamento
                                    @else
                                        Nova Mensagem de Contato
                                    @endif
                                </div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:6px;font-family:Arial,sans-serif;">
                                    Recebida em {{ $contactMessage->created_at->format('d/m/Y') }} às {{ $contactMessage->created_at->format('H:i') }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- ── CORPO ── -->
                    <tr>
                        <td style="background-color:#ffffff;padding:32px 40px 8px;">

                            <!-- Remetente -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="background-color:#f6f4ef;border-radius:12px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <div style="font-size:10px;font-weight:700;letter-spacing:0.2em;color:#9a8b7a;
                                                    text-transform:uppercase;margin-bottom:10px;font-family:Arial,sans-serif;">
                                            Remetente
                                        </div>
                                        <div style="font-size:18px;font-weight:700;color:#012d1d;font-family:Arial,sans-serif;">
                                            {{ $contactMessage->name }}
                                        </div>
                                        <div style="margin-top:4px;">
                                            <a href="mailto:{{ $contactMessage->email }}"
                                               style="font-size:14px;color:#6a5e33;text-decoration:none;font-family:Arial,sans-serif;">
                                                {{ $contactMessage->email }}
                                            </a>
                                        </div>
                                        @if($contactMessage->phone)
                                        <div style="margin-top:4px;font-size:13px;color:#717973;font-family:Arial,sans-serif;">
                                            📞 {{ $contactMessage->phone }}
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Assunto / Tipo -->
                            @if($contactMessage->subject && $contactMessage->subject !== 'orcamento')
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="margin-bottom:20px;">
                                <tr>
                                    <td>
                                        <div style="font-size:10px;font-weight:700;letter-spacing:0.2em;color:#9a8b7a;
                                                    text-transform:uppercase;margin-bottom:6px;font-family:Arial,sans-serif;">
                                            Assunto / Interesse
                                        </div>
                                        <div style="font-size:14px;color:#1c1c19;font-family:Arial,sans-serif;
                                                    display:inline-block;background-color:#edeae5;padding:6px 14px;
                                                    border-radius:8px;font-weight:600;">
                                            {{ $contactMessage->subject }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Mensagem -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="margin-bottom:24px;">
                                <tr>
                                    <td>
                                        <div style="font-size:10px;font-weight:700;letter-spacing:0.2em;color:#9a8b7a;
                                                    text-transform:uppercase;margin-bottom:8px;font-family:Arial,sans-serif;">
                                            Mensagem
                                        </div>
                                        <div style="background-color:#f6f4ef;border-left:4px solid #012d1d;
                                                    border-radius:0 10px 10px 0;padding:18px 20px;
                                                    font-size:14px;color:#1c1c19;line-height:1.7;
                                                    font-family:Arial,sans-serif;white-space:pre-line;">{{ $contactMessage->message }}</div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ── RODAPÉ INFO ── -->
                    <tr>
                        <td style="background-color:#ffffff;padding:0 40px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="border-top:1px solid #e8e5e0;padding-top:20px;margin-top:0;">
                                <tr>
                                    <td width="50%" style="padding-top:20px;vertical-align:top;">
                                        <div style="font-size:10px;font-weight:700;letter-spacing:0.15em;color:#9a8b7a;
                                                    text-transform:uppercase;margin-bottom:4px;font-family:Arial,sans-serif;">
                                            Idioma
                                        </div>
                                        <div style="font-size:13px;color:#1c1c19;font-family:Arial,sans-serif;">
                                            {{ strtoupper($contactMessage->locale) === 'PT' ? '🇧🇷 Português' : '🇺🇸 English' }}
                                        </div>
                                    </td>
                                    @if($contactMessage->unit_email)
                                    <td width="50%" style="padding-top:20px;vertical-align:top;">
                                        <div style="font-size:10px;font-weight:700;letter-spacing:0.15em;color:#9a8b7a;
                                                    text-transform:uppercase;margin-bottom:4px;font-family:Arial,sans-serif;">
                                            Unidade destinatária
                                        </div>
                                        <div style="font-size:13px;color:#1c1c19;font-family:Arial,sans-serif;">
                                            {{ $contactMessage->unit_email }}
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ── CTA RESPONDER ── -->
                    <tr>
                        <td style="background-color:#012d1d;padding:28px 40px;text-align:center;border-radius:0 0 0 0;">
                            <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode('[Trataplan] ' . ($contactMessage->subject ?: 'Sua mensagem')) }}"
                               style="display:inline-block;background-color:#fbbf24;color:#012d1d;
                                      font-size:13px;font-weight:900;letter-spacing:0.15em;text-transform:uppercase;
                                      text-decoration:none;padding:14px 32px;border-radius:10px;
                                      font-family:Arial,sans-serif;">
                                Responder ao Cliente →
                            </a>
                        </td>
                    </tr>

                    <!-- ── RODAPÉ ── -->
                    <tr>
                        <td style="background-color:#f0ede8;padding:20px 40px;text-align:center;
                                   border-radius:0 0 16px 16px;">
                            <div style="font-size:11px;color:#9a8b7a;font-family:Arial,sans-serif;line-height:1.6;">
                                Esta mensagem foi enviada pelo formulário de contato em
                                <a href="https://trataplan.com.br" style="color:#6a5e33;text-decoration:none;">trataplan.com.br</a>
                                <br>Responda diretamente para {{ $contactMessage->email }}
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
