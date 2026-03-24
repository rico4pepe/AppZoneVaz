<header class="fz-topbar">

    {{-- LEFT: Sidebar toggle + page title --}}
    <div class="fz-topbar-left">

        <!-- <button
            class="fz-sidebar-toggle"
            onclick="FanZoneApp.toggleSidebar()"
            aria-label="Toggle sidebar"
            title="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button> -->

       <span class="fz-topbar-title d-mobile-only">FanZone</span>

    </div>

    {{-- RIGHT: Notifications + user chip + logout --}}
    <div class="fz-topbar-right">

        {{-- Notification bell --}}
        <button class="fz-icon-btn fz-notif-btn" aria-label="Notifications" title="Notifications">
            <i class="fas fa-bell"></i>
            {{-- Dot — only renders if there are unread notifications --}}
            {{-- @if($unreadNotifications ?? false) --}}
            <span class="fz-notif-dot"></span>
            {{-- @endif --}}
        </button>

        {{-- User chip --}}
        <div class="fz-user-chip">
            <div class="fz-user-avatar">
                {{ strtoupper(substr(auth()->user()->display_name ?? auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <span class="fz-user-name d-hide-sm">
                {{ auth()->user()->display_name ?? auth()->user()->name ?? 'User' }}
            </span>
        </div>

        {{-- Logout --}}
        <!-- <form method="POST" action="{{ route('logout') }}" class="fz-logout-form">
            @csrf
            <button type="submit" class="fz-logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-hide-sm">Logout</span>
            </button>
        </form> -->

    </div>

</header>

<style>
    /* ============================================
       TOPBAR COMPONENTS
    ============================================ */
    .fz-topbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .fz-topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .fz-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--fz-text);
        letter-spacing: 0.2px;
    }

    /* ============================================
       SIDEBAR TOGGLE BUTTON
    ============================================ */
    .fz-sidebar-toggle {
        width: 34px;
        height: 34px;
        background: transparent;
        border: 1px solid var(--fz-border);
        border-radius: var(--fz-radius-sm);
        color: var(--fz-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
        flex-shrink: 0;
    }

    .fz-sidebar-toggle:hover {
        background: var(--fz-surface-2);
        color: var(--fz-text);
        border-color: var(--fz-border-md);
    }

    /* ============================================
       ICON BUTTON BASE
    ============================================ */
    .fz-icon-btn {
        width: 34px;
        height: 34px;
        background: var(--fz-surface-2);
        border: 1px solid var(--fz-border);
        border-radius: var(--fz-radius-sm);
        color: var(--fz-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: background 0.15s, color 0.15s;
        flex-shrink: 0;
    }

    .fz-icon-btn:hover {
        background: var(--fz-surface-3);
        color: var(--fz-text);
    }

    /* ============================================
       NOTIFICATION DOT
    ============================================ */
    .fz-notif-dot {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 7px;
        height: 7px;
        background: var(--fz-red);
        border-radius: 50%;
        border: 1.5px solid var(--fz-surface);
    }

    /* ============================================
       USER CHIP
    ============================================ */
    .fz-user-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--fz-surface-2);
        border: 1px solid var(--fz-border);
        border-radius: var(--fz-radius-sm);
        padding: 4px 10px 4px 5px;
    }

    .fz-user-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--fz-green-dim);
        border: 1.5px solid var(--fz-green-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: var(--fz-green);
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }

    .fz-user-name {
        font-size: 12px;
        color: var(--fz-muted);
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ============================================
       LOGOUT BUTTON
    ============================================ */
    .fz-logout-form { margin: 0; }

    .fz-logout-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        border: 1px solid rgba(255, 61, 61, 0.25);
        border-radius: var(--fz-radius-sm);
        color: var(--fz-red);
        font-size: 12px;
        padding: 5px 10px;
        transition: background 0.15s, border-color 0.15s;
    }

    .fz-logout-btn:hover {
        background: rgba(255, 61, 61, 0.08);
        border-color: rgba(255, 61, 61, 0.45);
    }

    /* Show only on mobile */
.d-mobile-only {
    display: none;
}



    /* ============================================
       RESPONSIVE — hide labels on small screens
    ============================================ */
    @media (max-width: 480px) {
        .d-hide-sm { display: none; }

        .fz-user-chip {
            padding: 4px 5px;
        }

        .fz-logout-btn {
            padding: 5px 8px;
        }
    }

           @media (max-width: 768px) {
    .d-mobile-only {
        display: block;
    }
}
</style>