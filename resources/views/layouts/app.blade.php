<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ChatApp')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💬</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green: #25D366;
            --dark-green: #128C7E;
            --teal: #075E54;
            --light-green: #DCF8C6;
            --bg: #ECE5DD;
            --sidebar-bg: #FFFFFF;
            --header-bg: #075E54;
            --msg-out: #DCF8C6;
            --msg-in: #FFFFFF;
            --text: #303030;
            --gray: #667781;
            --border: #E9EDEF;
            --hover: #F0F2F5;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
        }
        /* ─── FLASH MESSAGES ─── */
        .flash {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            padding: .75rem 1.25rem;
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
            animation: slideIn .3s ease;
        }
        .flash.success { background: #25D366; color: white; }
        .flash.error   { background: #e53e3e; color: white; }
        @keyframes slideIn { from { transform: translateX(100px); opacity: 0; } }
        @keyframes fadeOut { to { opacity: 0; pointer-events: none; } }
    </style>
    @yield('styles')
</head>
<body>

@if(session('success'))
    <div class="flash success" id="flash">{{ session('success') }}</div>
    <script>setTimeout(() => document.getElementById('flash')?.remove(), 4000)</script>
@endif
@if(session('error'))
    <div class="flash error" id="flash">{{ session('error') }}</div>
    <script>setTimeout(() => document.getElementById('flash')?.remove(), 4000)</script>
@endif

@yield('content')

@yield('scripts')
</body>
</html>
