<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'FanZone')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ============================================
           FANZONE AUTH DESIGN SYSTEM
           Shared variables — mirrors layout_dashboard
        ============================================ */
        :root {
            --fz-base:         #121212;
            --fz-surface:      #1E1E1E;
            --fz-surface-2:    #252525;
            --fz-surface-3:    #2C2C2C;
            --fz-green:        #00C853;
            --fz-green-dim:    rgba(0, 200, 83, 0.12);
            --fz-green-border: rgba(0, 200, 83, 0.25);
            --fz-amber:        #FFB300;
            --fz-amber-dim:    rgba(255, 179, 0, 0.12);
            --fz-red:          #FF3D3D;
            --fz-text:         #F5F5F5;
            --fz-muted:        #9E9E9E;
            --fz-border:       rgba(255, 255, 255, 0.08);
            --fz-border-md:    rgba(255, 255, 255, 0.12);

            --fz-radius-sm:    8px;
            --fz-radius-md:    12px;
            --fz-radius-lg:    16px;
            --fz-radius-xl:    24px;

            --fz-font: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* ============================================
           RESET & BASE
        ============================================ */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            background-color: var(--fz-base);
            color: var(--fz-text);
            font-family: var(--fz-font);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }
        button { cursor: pointer; font-family: inherit; }

        /* ============================================
           SCROLLBAR
        ============================================ */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--fz-surface-3);
            border-radius: 4px;
        }

        /* ============================================
           AUTH SHELL
        ============================================ */
        .fz-auth-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ============================================
           AUTH HEADER — top brand bar
        ============================================ */
        .fz-auth-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--fz-border);
            flex-shrink: 0;
        }

        .fz-auth-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .fz-auth-brand-icon {
            width: 32px;
            height: 32px;
            background: var(--fz-green);
            border-radius: var(--fz-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #121212;
            font-size: 15px;
        }

        .fz-auth-brand-name {
              font-size: 18px;
              font-weight: 800;
              letter-spacing: 0.6px;
            
        }

        .fz-auth-header-right {
            font-size: 12px;
            color: var(--fz-muted);
        }

        /* ============================================
           AUTH HERO STRIP
           Subtle football atmosphere above the form
        ============================================ */
        .fz-auth-hero {
            background: linear-gradient(
                135deg,
                rgba(0, 200, 83, 0.12) 0%,
                rgba(255, 179, 0, 0.06) 50%,
                transparent 100%
            );
            border-bottom: 1px solid var(--fz-border);
            padding: 32px 24px;
            text-align: center;
        }

        .fz-auth-hero-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--fz-text);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .fz-auth-hero-title span {
            color: var(--fz-green);
        }

        .fz-auth-hero-sub {
            font-size: 13px;
            color: var(--fz-muted);
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ============================================
           AUTH BODY — content slot
        ============================================ */
        .fz-auth-body {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 16px 48px;
        }

        .fz-auth-content {
            width: 100%;
            max-width: @yield('content-width', '520px');
        }

        /* ============================================
           AUTH CARD — the main form container
        ============================================ */
        .fz-auth-card {
            background: var(--fz-surface);
            border: 1px solid var(--fz-border-md);
            border-radius: var(--fz-radius-lg);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .fz-auth-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--fz-border);
            background: var(--fz-surface-2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .fz-auth-card-header-icon {
            width: 32px;
            height: 32px;
            background: var(--fz-green-dim);
            border: 1px solid var(--fz-green-border);
            border-radius: var(--fz-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--fz-green);
            font-size: 13px;
            flex-shrink: 0;
        }

        .fz-auth-card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--fz-text);
        }

        .fz-auth-card-body {
            padding: 20px;
        }

        /* ============================================
           FORM FIELDS — shared across login & preference
        ============================================ */
        .fz-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .fz-field:last-of-type { margin-bottom: 0; }

        .fz-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--fz-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .fz-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .fz-input-icon {
            position: absolute;
            left: 12px;
            color: var(--fz-muted);
            font-size: 13px;
            pointer-events: none;
            z-index: 1;
        }

        .fz-input {
            width: 100%;
            background: var(--fz-surface-2);
            border: 1px solid var(--fz-border-md);
            border-radius: var(--fz-radius-sm);
            color: var(--fz-text);
            font-size: 13px;
            font-family: var(--fz-font);
            padding: 11px 12px 11px 38px;
            outline: none;
            transition: border-color 0.15s, background 0.15s;
            appearance: none;
            -webkit-appearance: none;
        }

        .fz-input:focus {
             border-color: var(--fz-green);
             background: var(--fz-surface-3);
            box-shadow: 0 0 0 2px rgba(0, 200, 83, 0.15);
        }

        .fz-btn:active {
            transform: scale(0.97);
        }

        .fz-input::placeholder {
            color: var(--fz-muted);
            opacity: 0.5;
        }

        .fz-input:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Select */
        .fz-select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239E9E9E' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        .fz-select option {
            background: var(--fz-surface-2);
            color: var(--fz-text);
        }

        .fz-field-hint {
            font-size: 11px;
            color: var(--fz-muted);
            line-height: 1.5;
        }

        /* ============================================
           BUTTONS
        ============================================ */
        .fz-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            font-family: var(--fz-font);
            padding: 11px 20px;
            border-radius: var(--fz-radius-sm);
            border: none;
            cursor: pointer;
            transition: opacity 0.15s, background 0.15s;
            letter-spacing: 0.3px;
            text-decoration: none;
        }

        .fz-btn-primary {
            background: var(--fz-green);
            color: #121212;
            width: 100%;
        }

        .fz-btn-primary:hover { opacity: 0.88; }

        .fz-btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .fz-btn-ghost {
            background: transparent;
            color: var(--fz-muted);
            border: 1px solid var(--fz-border-md);
            opacity: 0.7;
        }

        .fz-btn-ghost:hover {
            background: var(--fz-surface-2);
            color: var(--fz-text);
              opacity: 1;
        }

        /* ============================================
           VALIDATION ERRORS
        ============================================ */
        .fz-error-bag {
            background: rgba(255, 61, 61, 0.08);
            border: 1px solid rgba(255, 61, 61, 0.25);
            border-radius: var(--fz-radius-sm);
            padding: 12px 16px;
            margin-bottom: 16px;
            animation: fadeIn 0.2s ease;
        }


        @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

        .fz-error-bag ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .fz-error-bag li {
            font-size: 12px;
            color: var(--fz-red);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .fz-error-bag li::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 10px;
        }

        /* ============================================
           AUTH FOOTER
        ============================================ */
        .fz-auth-footer {
            text-align: center;
            padding: 16px 24px;
            border-top: 1px solid var(--fz-border);
            font-size: 11px;
            color: var(--fz-muted);
        }

        /* ============================================
           STEP INDICATOR — for multi-step flows
        ============================================ */
        .fz-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }

        .fz-step {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fz-step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--fz-surface-2);
            border: 1.5px solid var(--fz-border-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--fz-muted);
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .fz-step-dot.active {
            background: var(--fz-green);
            border-color: var(--fz-green);
             transform: scale(1.05);
            color: #121212;
        }

        .fz-step-dot.done {
            background: var(--fz-green-dim);
            border-color: var(--fz-green-border);
            color: var(--fz-green);
             opacity: 0.9;
        }

        .fz-step-label {
            font-size: 11px;
            color: var(--fz-muted);
            white-space: nowrap;
        }

        .fz-step-label.active { color: var(--fz-text); font-weight: 600; }

        .fz-step-line {
            width: 40px;
            height: 1px;
            background: var(--fz-border-md);
            margin: 0 4px;
        }

        .fz-step-line.done { background: var(--fz-green-border); }

        @stack('auth-styles')
    </style>

    @stack('styles')
</head>
<body>

<div class="fz-auth-shell">

    {{-- Brand header --}}
    <header class="fz-auth-header">
        <div class="fz-auth-brand">
            <div class="fz-auth-brand-icon">
                <i class="fas fa-futbol"></i>
            </div>
            <span class="fz-auth-brand-name">FanZone</span>
        </div>
        <div class="fz-auth-header-right">
            @yield('header-right')
        </div>
    </header>

    {{-- Hero strip --}}
    @hasSection('hero')
        <div class="fz-auth-hero">
            @yield('hero')
        </div>
    @endif

    {{-- Main content --}}
    <div class="fz-auth-body">
        <div class="fz-auth-content" style="max-width: @yield('content-width', '520px')">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="fz-error-bag">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Session success --}}
            @if(session('success'))
                <div class="fz-alert fz-alert-success"
                     style="margin-bottom:16px;padding:12px 16px;border-radius:var(--fz-radius-sm);background:var(--fz-green-dim);border:1px solid var(--fz-green-border);color:var(--fz-green);font-size:13px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')

        </div>
    </div>

    {{-- Footer --}}
    <footer class="fz-auth-footer">
        &copy; {{ date('Y') }} FanZone. All Rights Reserved.
    </footer>

</div>

{{-- SweetAlert2 — available on auth pages --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('scripts')

</body>
</html>