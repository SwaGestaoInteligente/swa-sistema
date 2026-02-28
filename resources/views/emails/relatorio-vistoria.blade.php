<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de vistoria</title>
</head>
<body style="font-family: Arial, sans-serif; color:#0f2144; line-height:1.6; max-width:560px; margin:0 auto; padding:24px 16px;">
    <div style="background:#0c2e69; padding:16px 20px; border-radius:8px 8px 0 0;">
        <h2 style="margin:0; color:#ffffff; font-size:18px;">SWA Gestão — Relatório de Vistoria</h2>
    </div>
    <div style="border:1px solid #dce6f4; border-top:none; border-radius:0 0 8px 8px; padding:20px;">
        <p style="margin:0 0 8px;">
            <strong>Condomínio:</strong> {{ $relatorio->condominio->nome ?? '-' }}
        </p>
        <p style="margin:0 0 16px;">
            <strong>Arquivo:</strong> {{ $relatorio->file_name }}
        </p>
        @if (!empty($mensagem))
            <p style="margin:0 0 20px; background:#edf3fb; padding:12px; border-radius:6px; border-left:4px solid #0c2e69;">{{ $mensagem }}</p>
        @endif
        <p style="margin:0 0 20px;">O relatório está disponível para download pelo link seguro abaixo. O link expira em <strong>24 horas</strong>.</p>
        <div style="text-align:center; margin:24px 0;">
            <a href="{{ $downloadUrl }}"
               style="display:inline-block; background:#0c2e69; color:#ffffff; text-decoration:none;
                      font-weight:bold; font-size:16px; padding:14px 32px; border-radius:8px;">
                ⬇ Baixar Relatório PDF
            </a>
        </div>
        <p style="margin:0; color:#62779a; font-size:12px; text-align:center;">
            Link seguro com expiração automática em 24h. Não compartilhe este e-mail.
        </p>
    </div>
</body>
</html>
