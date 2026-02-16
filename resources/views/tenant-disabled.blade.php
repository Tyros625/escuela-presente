<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso no disponible - Escuela Presente</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            min-height: 100vh;
            color: #0f172a;
        }
        /* 왼쪽 절반: 이미지 (화면 너비의 50%, 전체 높이) */
        .half-image {
            width: 50%;
            min-height: 100vh;
            background: #e0f2fe url("{{ asset('assets/fonts/image/david.png') }}") center/cover no-repeat;
        }
        /* 오른쪽 절반: 정보 */
        .half-content {
            width: 50%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #fff;
        }
        .content-inner {
            max-width: 400px;
            width: 100%;
        }
        .content-inner h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .content-inner p {
            font-size: 0.9375rem;
            line-height: 1.65;
            color: #475569;
            margin-bottom: 1rem;
        }
        .contact-block {
            margin-top: 1.25rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border-radius: 0.75rem;
            border: 1px solid #bbf7d0;
        }
        .contact-block .label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #15803d;
            margin-bottom: 0.35rem;
        }
        .contact-block .whatsapp-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: 0.02em;
        }
        .contact-block .hint {
            font-size: 0.8125rem;
            color: #64748b;
            margin-top: 0.5rem;
        }
        .footer-note {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.8125rem;
            color: #94a3b8;
        }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .half-image { width: 100%; min-height: 40vh; }
            .half-content { width: 100%; min-height: auto; padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="half-image"></div>
    <div class="half-content">
        <div class="content-inner">
            @if(($reason ?? null) === 'expired')
                <h1>Período de prueba finalizado</h1>
                <p>Su período de acceso gratuito ha finalizado. Para continuar utilizando la plataforma, contacte al administrador.</p>
            @elseif(($reason ?? null) === 'blocked')
                <h1>Acceso desactivado</h1>
                <p>El acceso a esta institución está actualmente desactivado. Si considera que es un error, contacte al administrador.</p>
            @else
                <h1>Acceso no disponible</h1>
                <p>El acceso no está disponible en este momento. Contacte al administrador si tiene dudas.</p>
            @endif
            <p>Puede escribir por <strong>WhatsApp</strong> al administrador para solicitar la reactivación o ampliar su período de acceso.</p>
            <div class="contact-block">
                <div class="label">Contactar por WhatsApp</div>
                <div class="whatsapp-number">+52 1 55 2969 8426</div>
                <div class="hint">David — Escuela Presente</div>
            </div>
            <div class="footer-note">Escuela Presente — Contacte al administrador</div>
        </div>
    </div>
</body>
</html>
