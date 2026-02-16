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
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 50%, #fefce8 100%);
            color: #0f172a;
            padding: 1.5rem;
        }
        .page-card {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2rem;
            max-width: 720px;
            width: 100%;
            background: #fff;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .admin-side {
            flex: 0 0 200px;
            text-align: center;
        }
        .admin-side img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #e0f2fe;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.15);
        }
        .admin-side .name {
            margin-top: 1rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #0c4a6e;
        }
        .content-side {
            flex: 1;
            min-width: 260px;
        }
        .content-side h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .content-side p {
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
        @media (max-width: 560px) {
            .page-card { flex-direction: column; text-align: center; }
            .admin-side { flex: none; }
            .content-side { min-width: 0; }
        }
    </style>
</head>
<body>
    <div class="page-card">
        <div class="admin-side">
            <img src="{{ asset('assets/fonts/image/admin.png') }}" alt="Administrador" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div style="display:none; width:160px; height:160px; margin:0 auto; background:linear-gradient(135deg,#e0f2fe,#fef3c7); border-radius:50%; border:4px solid #e0f2fe;"></div>
            <div class="name">David</div>
        </div>
        <div class="content-side">
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
