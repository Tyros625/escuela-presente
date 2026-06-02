<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Escuela Presente</title>

    <meta name="description" content="Escuela Presente">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="{{ global_asset('/assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ global_asset('/assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ global_asset('/assets/media/favicons/apple-touch-icon-180x180.png') }}">

    <script>
        (function () {
            try {
                var pref = localStorage.getItem('ep-color-scheme');
                var dark = pref === 'dark' || (pref === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) {
                    document.documentElement.classList.add('ep-dark');
                    document.documentElement.dataset.colorScheme = 'dark';
                }
            } catch (e) {}
        })();
    </script>

    <style>
        html.ep-dark body {
            background-color: #1e2442;
            color: #e4e7ef;
        }

        #app-splash {
            --splash-bg-1: #0f172a;
            --splash-bg-2: #1e3a5f;
            --splash-bg-3: #312e81;
            --splash-accent: #38bdf8;
            --splash-gold: #fbbf24;
            --splash-text: #e2e8f0;
            --splash-muted: #94a3b8;
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: radial-gradient(ellipse 120% 80% at 50% 120%, #1d4ed8 0%, transparent 55%),
                radial-gradient(ellipse 90% 60% at 0% 0%, #4c1d95 0%, transparent 50%),
                radial-gradient(ellipse 70% 50% at 100% 20%, #0e7490 0%, transparent 45%),
                linear-gradient(160deg, var(--splash-bg-1) 0%, var(--splash-bg-2) 45%, var(--splash-bg-3) 100%);
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
        }

        #app-splash .splash-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.45;
            animation: splash-drift 14s ease-in-out infinite;
        }

        #app-splash .splash-orb--1 {
            width: 280px;
            height: 280px;
            background: #38bdf8;
            top: 10%;
            left: 15%;
            animation-delay: 0s;
        }

        #app-splash .splash-orb--2 {
            width: 220px;
            height: 220px;
            background: #a78bfa;
            bottom: 15%;
            right: 10%;
            animation-delay: -4s;
        }

        #app-splash .splash-orb--3 {
            width: 180px;
            height: 180px;
            background: #fbbf24;
            top: 55%;
            left: 55%;
            animation-delay: -7s;
        }

        @keyframes splash-drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(24px, -18px) scale(1.08); }
            66% { transform: translate(-16px, 22px) scale(0.94); }
        }

        #app-splash .splash-stage {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.75rem;
            padding: 2rem;
            text-align: center;
            animation: splash-fade-in 1s ease-out both;
        }

        @keyframes splash-fade-in {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #app-splash .splash-emblem {
            position: relative;
            width: 112px;
            height: 112px;
        }

        #app-splash .splash-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid transparent;
        }

        #app-splash .splash-ring--outer {
            border-top-color: var(--splash-accent);
            border-right-color: rgba(56, 189, 248, 0.25);
            animation: splash-spin 2.4s linear infinite;
        }

        #app-splash .splash-ring--mid {
            inset: 14px;
            border-bottom-color: var(--splash-gold);
            border-left-color: rgba(251, 191, 36, 0.2);
            animation: splash-spin 1.8s linear infinite reverse;
        }

        #app-splash .splash-ring--inner {
            inset: 28px;
            border-top-color: rgba(255, 255, 255, 0.5);
            animation: splash-spin 1.2s linear infinite;
        }

        @keyframes splash-spin {
            to { transform: rotate(360deg); }
        }

        #app-splash .splash-core {
            position: absolute;
            inset: 38px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.04));
            box-shadow: 0 0 40px rgba(56, 189, 248, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.35rem;
            letter-spacing: 0.06em;
            color: #fff;
            animation: splash-pulse 2s ease-in-out infinite;
        }

        @keyframes splash-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(56, 189, 248, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25); }
            50% { transform: scale(1.04); box-shadow: 0 0 56px rgba(56, 189, 248, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.35); }
        }

        #app-splash .splash-title {
            margin: 0;
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 600;
            letter-spacing: 0.02em;
            color: var(--splash-text);
            text-shadow: 0 2px 24px rgba(0, 0, 0, 0.35);
        }

        #app-splash .splash-title span {
            background: linear-gradient(90deg, #fff 0%, var(--splash-accent) 50%, var(--splash-gold) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: splash-shimmer 4s linear infinite;
        }

        @keyframes splash-shimmer {
            to { background-position: 200% center; }
        }

        #app-splash .splash-dots {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: center;
        }

        #app-splash .splash-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--splash-accent);
            animation: splash-dot 1.4s ease-in-out infinite;
        }

        #app-splash .splash-dots span:nth-child(2) { animation-delay: 0.2s; background: var(--splash-gold); }
        #app-splash .splash-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes splash-dot {
            0%, 80%, 100% { transform: scale(0.65); opacity: 0.45; }
            40% { transform: scale(1); opacity: 1; }
        }

        #app-splash .splash-bar {
            width: min(220px, 70vw);
            height: 3px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            overflow: hidden;
        }

        #app-splash .splash-bar::after {
            content: "";
            display: block;
            height: 100%;
            width: 40%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--splash-accent), var(--splash-gold));
            animation: splash-bar 1.6s ease-in-out infinite;
        }

        @keyframes splash-bar {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }
    </style>
</head>

<body>
    <noscript>
        <strong>We're sorry but OneUI Vue Edition doesn't work properly without JavaScript enabled. Please enable it
            to continue.</strong>
    </noscript>

    <script>window.__APP_SPLASH_AT = Date.now();</script>

    <div id="app-splash" aria-live="polite" aria-busy="true">
        <div class="splash-orb splash-orb--1" aria-hidden="true"></div>
        <div class="splash-orb splash-orb--2" aria-hidden="true"></div>
        <div class="splash-orb splash-orb--3" aria-hidden="true"></div>
        <div class="splash-stage">
            <div class="splash-emblem" aria-hidden="true">
                <div class="splash-ring splash-ring--outer"></div>
                <div class="splash-ring splash-ring--mid"></div>
                <div class="splash-ring splash-ring--inner"></div>
                <div class="splash-core">EP</div>
            </div>
            <h1 class="splash-title"><span>Escuela Presente</span></h1>
            <div class="splash-dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
            <div class="splash-bar" role="progressbar" aria-label="Cargando aplicación"></div>
        </div>
    </div>

    <div id="app"></div>

    @if(function_exists('tenant') && tenant())
    <script>window.__TENANT_APP = true;</script>
    @endif

    @vite('resources/js/main.js')
</body>

</html>
