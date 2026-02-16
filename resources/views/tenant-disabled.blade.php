<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso no disponible - Escuela Presente</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            padding: 1rem;
        }
        .card {
            max-width: 420px;
            width: 100%;
            background: rgba(255,255,255,0.06);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.9; }
        h1 { font-size: 1.35rem; margin: 0 0 1rem; font-weight: 600; }
        p { margin: 0 0 1rem; line-height: 1.6; color: #94a3b8; font-size: 0.95rem; }
        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: #25D366;
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-whatsapp:hover { background: #20bd5a; color: #fff; transform: translateY(-1px); }
        .contact { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.875rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        @if(($reason ?? null) === 'expired')
            <h1>Período de prueba finalizado</h1>
            <p>Su período de acceso gratuito ha finalizado. Para continuar utilizando la plataforma, póngase en contacto con el administrador.</p>
        @elseif(($reason ?? null) === 'blocked')
            <h1>Acceso desactivado</h1>
            <p>El acceso a esta institución está actualmente desactivado por el administrador. Si considera que es un error, contacte al administrador.</p>
        @else
            <h1>Acceso no disponible</h1>
            <p>El acceso a esta institución no está disponible en este momento. Contacte al administrador si tiene dudas.</p>
        @endif
        <p>Puede escribir por <strong>WhatsApp</strong> al administrador para solicitar la reactivación o ampliar su período de acceso.</p>
        @if(!empty($whatsappUrl))
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-whatsapp">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Contactar por WhatsApp
            </a>
        @endif
        <div class="contact">Escuela Presente — Contacte al administrador</div>
    </div>
</body>
</html>
