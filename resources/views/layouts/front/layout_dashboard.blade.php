<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'FanZone')</title>

    <meta name="app-url" content="{{ url('') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-user-id" content="{{ auth()->id() }}">
    <meta name="auth-username" content="{{ auth()->user()->display_name ?? auth()->user()->name ?? '' }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        body {
    font-size: 14px;
}




        /* ============================================
           FANZONE DESIGN SYSTEM — CSS VARIABLES
        ============================================ */
        :root {
            --fz-base:        #121212;
            --fz-surface:     #1E1E1E;
            --fz-surface-2:   #252525;
            --fz-surface-3:   #2C2C2C;
            --fz-green:       #00C853;
            --fz-green-dim:   rgba(0, 200, 83, 0.12);
            --fz-green-border:rgba(0, 200, 83, 0.25);
            --fz-amber:       #FFB300;
            --fz-amber-dim:   rgba(255, 179, 0, 0.12);
            --fz-red:         #FF3D3D;
            --fz-text:        #F5F5F5;
            --fz-muted:       #9E9E9E;
            --fz-border:      rgba(255, 255, 255, 0.08);
            --fz-border-md:   rgba(255, 255, 255, 0.12);

            --fz-sidebar-w:       220px;
            --fz-sidebar-w-icon:  52px;
            --fz-topbar-h:        54px;
            --fz-bottom-nav-h:    60px;
            --fz-radius-sm:       8px;
            --fz-radius-md:       12px;
            --fz-radius-lg:       16px;

           --fz-font: 'Inter', system-ui, -apple-system, sans-serif;
            --fz-space-xs: 4px;
            --fz-space-sm: 8px;
            --fz-space-md: 12px;
            --fz-space-lg: 16px;
            --fz-space-xl: 20px;
        }

        .fz-green {
    position: relative;
}

.fz-green::after {
    content: '';
    width: 6px;
    height: 6px;
    background: var(--fz-green);
    border-radius: 50%;
    position: absolute;
    top: -2px;
    right: -6px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    70% { opacity: 0; transform: scale(2); }
    100% { opacity: 0; }
}

        /* ============================================
           RESET & BASE
        ============================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            background-color: var(--fz-base);
            color: var(--fz-text);
            font-family: var(--fz-font);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a { color: inherit; text-decoration: none; }
        button { cursor: pointer; font-family: inherit; }

        /* ============================================
           SCROLLBAR
        ============================================ */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--fz-surface-3); border-radius: 4px; }

        /* ============================================
           APP SHELL LAYOUT
        ============================================ */
        .fz-app {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ============================================
           SIDEBAR — DESKTOP ONLY
        ============================================ */
        .fz-sidebar {
            width: var(--fz-sidebar-w);
            height: 100vh;
            background: var(--fz-surface);
            border-right: 1px solid var(--fz-border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: width 0.25s ease;
            overflow: hidden;
            position: relative;
            z-index: 100;
        }

        .fz-sidebar.collapsed {
            width: var(--fz-sidebar-w-icon);
        }

        /* ============================================
           MAIN CONTENT AREA
        ============================================ */
        .fz-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        .fz-topbar {
            height: var(--fz-topbar-h);
            background: var(--fz-surface);
            border-bottom: 1px solid var(--fz-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            z-index: 90;
        }

        .fz-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            /* bottom padding accounts for mobile bottom nav */
            padding-bottom: calc(20px + var(--fz-bottom-nav-h));
        }

        /* On desktop, no bottom nav padding needed */
        @media (min-width: 768px) {
            .fz-content {
                padding-bottom: 20px;
            }
        }

        /* ============================================
           MOBILE BOTTOM NAV
        ============================================ */
        .fz-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--fz-bottom-nav-h);
            background: var(--fz-surface);
            border-top: 1px solid var(--fz-border);
            z-index: 200;
            /* Safe area for notched phones */
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* ============================================
           SIDEBAR OVERLAY — MOBILE (if ever needed)
        ============================================ */
        .fz-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 150;
        }

        /* ============================================
           RESPONSIVE BREAKPOINTS
        ============================================ */
        @media (max-width: 767px) {
            .fz-sidebar {
                display: none; /* sidebar hidden on mobile */
            }

            .fz-bottom-nav {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
            }
        }

        /* ============================================
           LIVE BADGE PULSE ANIMATION
        ============================================ */
        @keyframes fz-pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        .fz-live-badge {
            background: var(--fz-red);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.8px;
            animation: fz-pulse 1.5s ease-in-out infinite;
        }

        /* ============================================
   POLL CARD
============================================ */
.fz-poll-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: var(--fz-space-md);
}

/* ============================================
   OPTIONS
============================================ */
.fz-poll-options {
    display: flex;
    flex-direction: column;
    gap: var(--fz-space-sm);
}

.fz-poll-option {
    width: 100%;
    text-align: left;
    padding: 10px 12px;
    border-radius: var(--fz-radius-md);
    border: 1px solid var(--fz-border);
    background: var(--fz-surface-2);
    font-size: 13px;
    color: var(--fz-text);
    transition: all 0.15s ease;
}

.fz-poll-option:active {
    transform: scale(0.98);
}

.fz-poll-option.active {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

/* ============================================
   SUBMIT BUTTON
============================================ */
.fz-poll-submit {
    margin-top: var(--fz-space-sm);
    padding: 10px;
    border-radius: var(--fz-radius-md);
    border: none;
    background: var(--fz-green);
    color: #121212;
    font-weight: 600;
    font-size: 13px;
}

.fz-poll-submit:disabled {
    opacity: 0.5;
}

        /* ============================================
        RESULTS
        ============================================ */
      /* POLL */
.fz-poll-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 12px;
}

/* OPTIONS */
.fz-poll-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fz-poll-option {
    width: 100%;
    text-align: left;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid var(--fz-border);
    background: var(--fz-surface-2);
    font-size: 13px;
    color: var(--fz-text);
    transition: all 0.15s ease;
}

.fz-poll-option:active {
    transform: scale(0.98);
}

.fz-poll-option.active {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

/* SUBMIT */
.fz-poll-submit {
    margin-top: 8px;
    padding: 10px;
    border-radius: 12px;
    border: none;
    background: var(--fz-green);
    color: #121212;
    font-weight: 600;
    font-size: 13px;
}

.fz-poll-submit:disabled {
    opacity: 0.5;
}

/* RESULTS */
.fz-poll-results {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fz-poll-result-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.fz-poll-result-head {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
}

.fz-poll-option-text.selected {
    color: var(--fz-green);
    font-weight: 600;
}

/* BAR */
.fz-poll-bar {
    height: 6px;
    background: var(--fz-surface-2);
    border-radius: 4px;
    overflow: hidden;
}

.fz-poll-bar-fill {
    height: 100%;
    background: var(--fz-muted);
    transition: width 0.4s ease;
}

.fz-poll-bar-fill.selected {
    background: var(--fz-green);
}


/* ============================================
   QUIZ
============================================ */
.fz-quiz-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 6px;
}

.fz-quiz-desc {
    font-size: 12px;
    color: var(--fz-muted);
    margin-bottom: 12px;
}

/* OPTIONS */
.fz-quiz-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fz-quiz-option {
    width: 100%;
    text-align: left;
    padding: 10px 12px;
    border-radius: var(--fz-radius-md);
    border: 1px solid var(--fz-border);
    background: var(--fz-surface-2);
    font-size: 13px;
    color: var(--fz-text);
    transition: all 0.15s ease;
}

.fz-quiz-option:active {
    transform: scale(0.98);
}

/* SELECTED */
.fz-quiz-option.active {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

/* RESULT STATES */
.fz-quiz-option.correct {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

.fz-quiz-option.wrong {
    border-color: var(--fz-red);
    background: rgba(255, 61, 61, 0.1);
    color: var(--fz-red);
}

/* SUBMIT */
.fz-quiz-submit {
    margin-top: 10px;
    padding: 10px;
    border-radius: var(--fz-radius-md);
    border: none;
    background: var(--fz-green);
    color: #121212;
    font-weight: 600;
    font-size: 13px;
}

.fz-quiz-submit:disabled {
    opacity: 0.5;
}

/* RESULT TEXT */
.fz-quiz-result {
    margin-top: 10px;
    padding: 10px;
    border-radius: var(--fz-radius-md);
    font-size: 13px;
    font-weight: 600;
}

.fz-quiz-result.success {
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

.fz-quiz-result.error {
    background: rgba(255, 61, 61, 0.1);
    color: var(--fz-red);
}
      
        /* ============================================
            FEED LIST
        ============================================ */
            .fz-feed-list {
                display: flex;
                flex-direction: column;
                gap: var(--fz-space-md);
            }

            /* ============================================
            FEED ITEM WRAPPER
            ============================================ */
            .fz-feed-item {
                transition: transform 0.15s ease;
            }

            .fz-feed-item:active {
                transform: scale(0.98);
            }

            /* ============================================
            SKELETON LOADER
            ============================================ */
            .fz-feed-skeleton {
                height: 120px;
                border-radius: var(--fz-radius-md);
                background: linear-gradient(
                    90deg,
                    var(--fz-surface) 25%,
                    var(--fz-surface-2) 50%,
                    var(--fz-surface) 75%
                );
                background-size: 200% 100%;
                animation: fz-shimmer 1.2s infinite;
            }

            @keyframes fz-shimmer {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }


            /* ============================================
   TRIVIA
============================================ */
.fz-trivia-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
}

/* INPUT */
.fz-trivia-input-wrap {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fz-trivia-input {
    padding: 10px 12px;
    border-radius: var(--fz-radius-md);
    border: 1px solid var(--fz-border);
    background: var(--fz-surface-2);
    color: var(--fz-text);
    font-size: 13px;
}

.fz-trivia-input:focus {
    outline: none;
    border-color: var(--fz-green);
}

/* SUBMIT */
.fz-trivia-submit {
    padding: 10px;
    border-radius: var(--fz-radius-md);
    border: none;
    background: var(--fz-green);
    color: #121212;
    font-weight: 600;
    font-size: 13px;
}

.fz-trivia-submit:disabled {
    opacity: 0.5;
}

/* RESULT */
.fz-trivia-result {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fz-trivia-status {
    padding: 10px;
    border-radius: var(--fz-radius-md);
    font-weight: 600;
    font-size: 13px;
}

.fz-trivia-status.success {
    background: var(--fz-green-dim);
    color: var(--fz-green);
}

.fz-trivia-status.error {
    background: rgba(255, 61, 61, 0.1);
    color: var(--fz-red);
}

/* ANSWERS */
.fz-trivia-answer {
    display: flex;
    flex-direction: column;
    font-size: 13px;
}

.fz-trivia-answer.correct {
    color: var(--fz-green);
    font-weight: 600;
}

/* CHAT ROOT */
.fz-chat {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--fz-base);
}

/* HEADER */
.fz-chat-header {
    padding: 10px 12px;
    border-bottom: 1px solid var(--fz-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.fz-chat-title {
    font-weight: 600;
    font-size: 13px;
}

.fz-chat-fans {
    margin-left: 6px;
    font-size: 11px;
    color: var(--fz-muted);
}

.fz-chat-room {
    background: var(--fz-surface);
    border: 1px solid var(--fz-border);
    color: var(--fz-text);
    font-size: 12px;
    padding: 4px;
}

/* BODY */
.fz-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* SYSTEM */
.fz-chat-system {
    text-align: center;
    font-size: 11px;
    color: var(--fz-muted);
}

/* MESSAGE ROW */
.fz-chat-row {
    display: flex;
}

.fz-chat-row.me {
    justify-content: flex-end;
}

/* BUBBLE */
.fz-chat-bubble {
    max-width: 75%;
    padding: 8px 10px;
    border-radius: 12px;
    background: var(--fz-surface-2);
    font-size: 13px;
}

.fz-chat-row.me .fz-chat-bubble {
    background: var(--fz-green);
    color: #121212;
}

/* USER */
.fz-chat-user {
    font-size: 10px;
    margin-bottom: 2px;
    color: var(--fz-muted);
}

/* TEXT */
.fz-chat-text {
    word-break: break-word;
}

/* INPUT */
.fz-chat-input {
    display: flex;
    gap: 6px;
    padding: 8px;
    border-top: 1px solid var(--fz-border);
}

.fz-chat-textbox {
    flex: 1;
    padding: 10px;
    border-radius: 20px;
    border: 1px solid var(--fz-border);
    background: var(--fz-surface-2);
    color: var(--fz-text);
}

.fz-chat-textbox:focus {
    outline: none;
    border-color: var(--fz-green);
}

.fz-chat-send {
    width: 40px;
    border-radius: 50%;
    border: none;
    background: var(--fz-green);
    color: #121212;
    font-size: 16px;
}

/* TYPING */
.fz-chat-typing {
    font-size: 11px;
    color: var(--fz-muted);
}

/* TOAST */
.fz-chat-toast {
    position: absolute;
    bottom: 70px;
    right: 10px;
    background: var(--fz-surface-3);
    padding: 8px 10px;
    border-radius: 8px;
    font-size: 12px;
}

        /* ============================================
           UTILITY CLASSES
        ============================================ */
        .fz-card {
            background: var(--fz-surface);
            border: 1px solid var(--fz-border);
            border-radius: var(--fz-radius-md);
            transition: background 0.2s ease, border 0.2s ease;
        }

            .fz-card:active {
                transform: scale(0.99);
            }

        .fz-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--fz-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .fz-card-body { padding: 14px 16px; }

        .fz-text-title {
    font-size: 15px;
    font-weight: 600;
}

.fz-text-body {
    font-size: 13px;
}

.fz-text-meta {
    font-size: 11px;
    color: var(--fz-muted);
}

button:active,
.fz-bn-item:active,
.fz-sb-item:active {
    transform: scale(0.96);
    opacity: 0.85;
}

        .fz-section-title {
           font-size: 14px;
            font-weight: 600;
            color: var(--fz-text);
        }

        .fz-link {
            font-size: 12px;
            color: var(--fz-green);
            opacity: 0.85;
            cursor: pointer;
        }

        .fz-link:active {
                opacity: 1;
            }

        .fz-muted { color: var(--fz-muted); }
        .fz-green { color: var(--fz-green); }
        .fz-amber { color: var(--fz-amber); }
        .fz-red   { color: var(--fz-red); }
    </style>

    @stack('styles')
</head>
<body>

<div class="fz-app">

    {{-- Sidebar — desktop only, injected here --}}
    @include('partials.sidebar')

    {{-- Main column --}}
    <div class="fz-main">

        {{-- Top bar --}}
        @include('partials.navbar')

        {{-- Page content --}}
        <div class="fz-content">
            @yield('content')
        </div>

    </div>

    {{-- Bottom nav — mobile only, injected here --}}
    @include('partials.bottom-nav')

</div>

{{-- Footer modal (onboarding) --}}
@include('partials.footer')

{{-- Global JS config — available to all React components --}}
<script>
    window.FanZone = {
        appUrl:   "{{ url('') }}",
        userId:   {{ auth()->id() ?? 'null' }},
        username: "{{ auth()->user()->display_name ?? auth()->user()->name ?? '' }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

{{-- Vite assets --}}
@viteReactRefresh
@vite('resources/js/app.js')
@vite('resources/js/chat.jsx')
@vite('resources/js/poll.jsx')
@vite('resources/js/quizz.jsx')
@vite('resources/js/trivia.jsx')
@vite('resources/js/feed.jsx')
@vite('resources/js/leaderboard-widget.jsx')

{{-- Alpine --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Bootstrap JS — for modal support (onboarding) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>