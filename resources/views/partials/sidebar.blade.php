<aside class="fz-sidebar" id="fz-sidebar" aria-label="Main navigation">

    {{-- ============================================
         LOGO / BRAND
    ============================================ --}}
    <div class="fz-sb-brand">
        <div class="fz-sb-logo-icon">
            <i class="fas fa-futbol"></i>
        </div>
        <span class="fz-sb-logo-text">FanZone</span>
    </div>

    {{-- ============================================
         NAVIGATION
    ============================================ --}}
    <nav class="fz-sb-nav" aria-label="Sidebar navigation">

        {{-- CORE --}}
        <div class="fz-sb-group">
            <span class="fz-sb-group-label">Core</span>
        </div>

        <a href="{{ route('dashboard') }}"
           class="fz-sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           title="Dashboard">
            <i class="fas fa-home fz-sb-icon"></i>
            <span class="fz-sb-label">Home</span>
        </a>

        <a href="#"
           class="fz-sb-item {{ request()->routeIs('matches.*') ? 'active' : '' }}"
           title="Matches">
            <i class="fas fa-calendar-alt fz-sb-icon"></i>
            <span class="fz-sb-label">Matches</span>
        </a>

        <a href="{{ route('feed') }}"
           class="fz-sb-item {{ request()->routeIs('feed') ? 'active' : '' }}"
           title="Content Feed">
            <i class="fas fa-rss fz-sb-icon"></i>
            <span class="fz-sb-label">Content Feed</span>
        </a>

        {{-- COMMUNITY --}}
        <div class="fz-sb-group">
            <span class="fz-sb-group-label">Community</span>
        </div>

        <a href="#"
           class="fz-sb-item {{ request()->routeIs('leaderboard') ? 'active' : '' }}"
           title="Leaderboard">
            <i class="fas fa-trophy fz-sb-icon"></i>
            <span class="fz-sb-label">Leaderboard</span>
        </a>

        <a href="#"
           class="fz-sb-item {{ request()->routeIs('notifications') ? 'active' : '' }}"
           title="Notifications">
            <i class="fas fa-bell fz-sb-icon"></i>
            <span class="fz-sb-label">Notifications</span>
            {{-- Unread badge --}}
            <span class="fz-sb-badge" id="fz-notif-count" style="display:none">0</span>
        </a>

        {{-- EXPLORE --}}
        <div class="fz-sb-group">
            <span class="fz-sb-group-label">Explore</span>
        </div>

        <a href="#"
           class="fz-sb-item {{ request()->routeIs('leagues') ? 'active' : '' }}"
           title="Leagues">
            <i class="fas fa-layer-group fz-sb-icon"></i>
            <span class="fz-sb-label">Leagues</span>
        </a>

        <a href="#"
           class="fz-sb-item {{ request()->routeIs('news') ? 'active' : '' }}"
           title="News">
            <i class="fas fa-newspaper fz-sb-icon"></i>
            <span class="fz-sb-label">News</span>
        </a>

    </nav>

    {{-- ============================================
         SUBSCRIPTION BADGE
    ============================================ --}}
    <div class="fz-sb-footer">
        <div class="fz-sb-sub-badge">
            <div class="fz-sb-sub-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="fz-sb-sub-info">
                <span class="fz-sb-sub-plan">
                    {{ auth()->user()->plan ?? 'Free' }}
                </span>
                <span class="fz-sb-sub-exp">
                    @if(auth()->user()->expires_at)
                        Expires in {{ now()->diffInDays(auth()->user()->expires_at) }} days
                    @else
                        No active plan
                    @endif
                </span>
            </div>
        </div>
    </div>

</aside>

<style>
    /* ============================================
       SIDEBAR SHELL
    ============================================ */
    .fz-sidebar {
        width: var(--fz-sidebar-w);
        height: 100vh;
        background: var(--fz-surface);
        border-right: 1px solid var(--fz-border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        overflow: hidden;
        transition: width 0.25s ease;
        position: relative;
        z-index: 100;
    }

    .fz-sidebar.collapsed {
        width: var(--fz-sidebar-w-icon);
    }

    /* ============================================
       BRAND
    ============================================ */
    .fz-sb-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 14px;
        border-bottom: 1px solid var(--fz-border);
        flex-shrink: 0;
        min-height: var(--fz-topbar-h);
    }

    .fz-sb-logo-icon {
        width: 28px;
        height: 28px;
        background: var(--fz-green);
        border-radius: var(--fz-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #121212;
        font-size: 13px;
    }

    .fz-sb-logo-text {
        font-size: 16px;
        font-weight: 800;
        color: var(--fz-green);
        letter-spacing: 0.5px;
        white-space: nowrap;
        opacity: 1;
        transition: opacity 0.2s ease;
    }

    .fz-sidebar.collapsed .fz-sb-logo-text {
        opacity: 0;
        pointer-events: none;
    }

    /* ============================================
       NAV
    ============================================ */
    .fz-sb-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 8px 0;
        display: flex;
        flex-direction: column;
    }

    /* Group label */
    .fz-sb-group {
        padding: 14px 16px 4px;
    }

    .fz-sb-group-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--fz-muted);
        white-space: nowrap;
        opacity: 1;
        transition: opacity 0.15s ease;
    }

    .fz-sidebar.collapsed .fz-sb-group-label {
        opacity: 0;
    }

    /* Nav item */
    .fz-sb-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        color: var(--fz-muted);
        transition: background 0.15s, color 0.15s;
        position: relative;
        white-space: nowrap;
        border-left: 2px solid transparent;
    }

    .fz-sb-item:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--fz-text);
    }

    .fz-sb-item.active {
        background: var(--fz-green-dim);
        color: var(--fz-green);
        border-left-color: var(--fz-green);
    }

    .fz-sb-icon {
        font-size: 15px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    .fz-sb-label {
        font-size: 13px;
        opacity: 1;
        transition: opacity 0.2s ease;
        flex: 1;
    }

    .fz-sidebar.collapsed .fz-sb-label {
        opacity: 0;
        pointer-events: none;
    }

    /* ============================================
       NOTIFICATION BADGE
    ============================================ */
    .fz-sb-badge {
        background: var(--fz-red);
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
        opacity: 1;
        transition: opacity 0.2s ease;
    }

    .fz-sidebar.collapsed .fz-sb-badge {
        opacity: 0;
    }

    /* ============================================
       TOOLTIP ON COLLAPSED — show item label
    ============================================ */
    .fz-sidebar.collapsed .fz-sb-item {
        justify-content: center;
        padding: 10px 0;
    }

    .fz-sidebar.collapsed .fz-sb-item:hover::after {
        content: attr(title);
        position: absolute;
        left: calc(var(--fz-sidebar-w-icon) + 8px);
        background: var(--fz-surface-3);
        color: var(--fz-text);
        font-size: 12px;
        padding: 5px 10px;
        border-radius: var(--fz-radius-sm);
        border: 1px solid var(--fz-border-md);
        white-space: nowrap;
        z-index: 999;
        pointer-events: none;
    }

    /* ============================================
       FOOTER — SUBSCRIPTION BADGE
    ============================================ */
    .fz-sb-footer {
        border-top: 1px solid var(--fz-border);
        padding: 12px;
        flex-shrink: 0;
    }

    .fz-sb-sub-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--fz-green-dim);
        border: 1px solid var(--fz-green-border);
        border-radius: var(--fz-radius-md);
        padding: 10px 12px;
        overflow: hidden;
    }

    .fz-sb-sub-icon {
        color: var(--fz-green);
        font-size: 14px;
        flex-shrink: 0;
        width: 20px;
        text-align: center;
    }

    .fz-sb-sub-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        opacity: 1;
        transition: opacity 0.2s ease;
        min-width: 0;
    }

    .fz-sidebar.collapsed .fz-sb-sub-info {
        opacity: 0;
        pointer-events: none;
    }

    .fz-sidebar.collapsed .fz-sb-sub-badge {
        justify-content: center;
        padding: 10px 0;
        background: transparent;
        border-color: transparent;
    }

    .fz-sb-sub-plan {
        font-size: 12px;
        font-weight: 700;
        color: var(--fz-green);
        text-transform: capitalize;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fz-sb-sub-exp {
        font-size: 10px;
        color: var(--fz-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ============================================
       MOBILE — sidebar hidden entirely
    ============================================ */
    @media (max-width: 767px) {
        .fz-sidebar {
            display: none;
        }
    }
</style>

<script>
    /**
     * FanZoneApp — global app controller
     * Defined here because sidebar state lives in the sidebar.
     * Navbar calls FanZoneApp.toggleSidebar() — wired up here.
     */
    window.FanZoneApp = window.FanZoneApp || {};

    (function () {
        const STORAGE_KEY = 'fz_sidebar_collapsed';
        const sidebar = document.getElementById('fz-sidebar');

        function isCollapsed() {
            return localStorage.getItem(STORAGE_KEY) === '1';
        }

        function applyState() {
            if (!sidebar) return;
            if (isCollapsed()) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }

        function toggleSidebar() {
            if (!sidebar) return;
            const nowCollapsed = sidebar.classList.toggle('collapsed');
            localStorage.setItem(STORAGE_KEY, nowCollapsed ? '1' : '0');
        }

        function collapseSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('collapsed');
            localStorage.setItem(STORAGE_KEY, '1');
        }

        function expandSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('collapsed');
            localStorage.setItem(STORAGE_KEY, '0');
        }

        // Apply saved state immediately on load
        applyState();

        // Expose to global scope
        window.FanZoneApp.toggleSidebar  = toggleSidebar;
        window.FanZoneApp.collapseSidebar = collapseSidebar;
        window.FanZoneApp.expandSidebar   = expandSidebar;

    })();
</script>