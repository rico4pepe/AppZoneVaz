<nav class="fz-bottom-nav" aria-label="Mobile navigation">

    {{-- Feed --}}
    <a href="{{ route('feed') }}"
       class="fz-bn-item {{ request()->routeIs('feed') ? 'active' : '' }}"
       title="Feed">
        <div class="fz-bn-icon-wrap">
            <i class="fas fa-rss"></i>
        </div>
        <span class="fz-bn-label">Feed</span>
    </a>

    {{-- Matches --}}
    <a href="{{ Route::has('matches.index') ? route('matches.index') : '#' }}"
       class="fz-bn-item {{ request()->routeIs('matches.*') ? 'active' : '' }}"
       title="Matches">
        <div class="fz-bn-icon-wrap">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <span class="fz-bn-label">Matches</span>
    </a>

    {{-- Chat (center focal) --}}
    <button
        class="fz-bn-item fz-bn-live"
        id="fz-live-btn"
        onclick="FanZoneApp.toggleChat()"
        title="Chat"
        aria-label="Open chat">
        <div class="fz-bn-live-icon">
            <i class="fas fa-comment-dots" id="fz-live-icon"></i>
            <span class="fz-live-count" id="fz-live-count" style="display:none">0</span>
        </div>
        <span class="fz-bn-label" id="fz-live-label">Chat</span>
    </button>

    {{-- More --}}
    <a href="#"
       class="fz-bn-item {{ request()->routeIs('more') ? 'active' : '' }}"
       title="More">
        <div class="fz-bn-icon-wrap">
            <i class="fas fa-ellipsis-h"></i>
        </div>
        <span class="fz-bn-label">More</span>
    </a>

</nav>

{{-- ============================================
     CHAT SHEET — slides up from bottom on mobile
============================================ --}}
<div class="fz-chat-sheet" id="fz-chat-sheet" aria-hidden="true">

    {{-- Sheet handle --}}
    <div class="fz-chat-sheet-handle" onclick="FanZoneApp.toggleChat()">
        <div class="fz-handle-bar"></div>
    </div>

    {{-- Sheet header --}}
    <div class="fz-chat-sheet-header">
        <div class="fz-chat-sheet-title">
            <span class="fz-live-badge">LIVE</span>
            <span>Match Chat</span>
        </div>
        <button class="fz-chat-sheet-close" onclick="FanZoneApp.toggleChat()" aria-label="Close chat">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    {{-- Chat mount — React picks this up --}}
    <div class="fz-chat-sheet-body">
        <div id="chat-app-mobile"
             data-user="{{ auth()->id() }}"
             data-username="{{ auth()->user()->display_name ?? auth()->user()->name }}"
             data-default-room="{{ $defaultMatchId ?? '' }}">
        </div>
    </div>

</div>

{{-- Sheet backdrop --}}
<div class="fz-chat-backdrop" id="fz-chat-backdrop" onclick="FanZoneApp.toggleChat()"></div>

<style>
    /* ============================================
       BOTTOM NAV SHELL
    ============================================ */
    .fz-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: var(--fz-bottom-nav-h);
        background: var(--fz-surface);
        border-top: 1px solid var(--fz-border);
        display: none;
        grid-template-columns: repeat(4, 1fr);
        align-items: center;
        z-index: 200;
        padding-bottom: env(safe-area-inset-bottom);
    }

    @media (max-width: 767px) {
        .fz-bottom-nav {
            display: grid;
        }
    }

    /* ============================================
       NAV ITEMS
    ============================================ */
    .fz-bn-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        padding: 6px 0;
        color: var(--fz-muted);
        background: transparent;
        border: none;
        text-decoration: none;
        transition: color 0.15s;
        position: relative;
        cursor: pointer;
        font-family: var(--fz-font);
        width: 100%;
        height: 100%;
    }

    .fz-bn-item.active {
        color: var(--fz-green);
    }

    .fz-bn-item.active .fz-bn-icon-wrap {
        background: var(--fz-green-dim);
    }

    .fz-bn-icon-wrap {
        width: 32px;
        height: 26px;
        border-radius: var(--fz-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: background 0.15s;
    }

    .fz-bn-label {
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    /* ============================================
       LIVE BUTTON — centre focal point
    ============================================ */
    .fz-bn-live {
        color: var(--fz-text);
    }

    .fz-bn-live-icon {
        width: 44px;
        height: 34px;
        background: var(--fz-green);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #121212;
        position: relative;
        transition: background 0.15s, transform 0.15s;
        margin-top: -8px; /* lifts it above the nav bar */
        box-shadow: 0 4px 14px rgba(0, 200, 83, 0.35);
    }

    .fz-bn-live.chat-open .fz-bn-live-icon {
        background: var(--fz-surface-3);
        box-shadow: none;
        color: var(--fz-muted);
    }

    .fz-bn-live .fz-bn-label {
        color: var(--fz-green);
        font-weight: 700;
    }

    .fz-bn-live.chat-open .fz-bn-label {
        color: var(--fz-muted);
    }

    /* Live match count badge */
    .fz-live-count {
        position: absolute;
        top: -4px;
        right: -4px;
        background: var(--fz-red);
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        padding: 1px 4px;
        border-radius: 8px;
        min-width: 16px;
        text-align: center;
        border: 1.5px solid var(--fz-surface);
    }

    /* ============================================
       CHAT SHEET
    ============================================ */
    .fz-chat-sheet {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 75vh;
        background: var(--fz-surface);
        border-top: 1px solid var(--fz-border-md);
        border-radius: 20px 20px 0 0;
        z-index: 300;
        display: flex;
        flex-direction: column;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
        padding-bottom: env(safe-area-inset-bottom);
    }

    .fz-chat-sheet.open {
        transform: translateY(0);
    }

    /* Hidden on desktop — chat widget handles itself there */
    @media (min-width: 768px) {
        .fz-chat-sheet {
            display: none;
        }
    }

    /* Handle */
    .fz-chat-sheet-handle {
        display: flex;
        justify-content: center;
        padding: 10px 0 6px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .fz-handle-bar {
        width: 36px;
        height: 4px;
        background: var(--fz-surface-3);
        border-radius: 2px;
    }

    /* Sheet header */
    .fz-chat-sheet-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 16px 10px;
        border-bottom: 1px solid var(--fz-border);
        flex-shrink: 0;
    }

    .fz-chat-sheet-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--fz-text);
    }

    .fz-chat-sheet-close {
        width: 30px;
        height: 30px;
        background: var(--fz-surface-2);
        border: 1px solid var(--fz-border);
        border-radius: var(--fz-radius-sm);
        color: var(--fz-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: background 0.15s;
    }

    .fz-chat-sheet-close:hover {
        background: var(--fz-surface-3);
        color: var(--fz-text);
    }

    /* Sheet body — chat mounts here */
    .fz-chat-sheet-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }

    .fz-chat-sheet-body #chat-app-mobile {
        height: 100%;
    }

    /* ============================================
       BACKDROP
    ============================================ */
    .fz-chat-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 299;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .fz-bn-item:active {
    transform: scale(0.92);
}
    .fz-chat-backdrop.open {
        display: block;
        opacity: 1;
    }

    @media (min-width: 768px) {
        .fz-chat-backdrop {
            display: none !important;
        }
    }
</style>

<script>
(function () {
    const CHAT_KEY = 'fz_chat_open';

    const sheet    = document.getElementById('fz-chat-sheet');
    const backdrop = document.getElementById('fz-chat-backdrop');
    const liveBtn  = document.getElementById('fz-live-btn');
    const liveLabel = document.getElementById('fz-live-label');
    const liveIcon  = document.getElementById('fz-live-icon');

    let chatOpen = false;

    function openChat() {
        if (!sheet) return;
        chatOpen = true;

        sheet.classList.add('open');
        sheet.setAttribute('aria-hidden', 'false');

        backdrop.classList.add('open');

        liveBtn.classList.add('chat-open');
        liveLabel.textContent = 'Close';
        liveIcon.className = 'fas fa-chevron-down';

        // Prevent body scroll while sheet is open
        document.body.style.overflow = 'hidden';
    }

    function closeChat() {
        if (!sheet) return;
        chatOpen = false;

        sheet.classList.remove('open');
        sheet.setAttribute('aria-hidden', 'true');

        backdrop.classList.remove('open');

        liveBtn.classList.remove('chat-open');
        liveLabel.textContent = 'Close';
        liveIcon.className = 'fas fa-comment-dots';

        document.body.style.overflow = '';
    }

    function toggleChat() {
        chatOpen ? closeChat() : openChat();
    }

    /**
     * Update the live match count badge on the Live button.
     * Called from React once match data loads.
     * Usage: FanZoneApp.setLiveCount(3)
     */
    function setLiveCount(count) {
        const badge = document.getElementById('fz-live-count');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Expose to global FanZoneApp
    window.FanZoneApp = window.FanZoneApp || {};
    window.FanZoneApp.toggleChat  = toggleChat;
    window.FanZoneApp.openChat    = openChat;
    window.FanZoneApp.closeChat   = closeChat;
    window.FanZoneApp.setLiveCount = setLiveCount;

})();
</script>