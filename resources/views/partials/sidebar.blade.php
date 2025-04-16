<div 
    class="sidebar bg-dark text-white" 
    x-data="{ open: false }" 
    :class="{ 'show': open }"
    x-init="$watch('open', val => document.body.classList.toggle('sidebar-open', val))"
>
    <!-- Toggle Button for Mobile -->
    <button class="btn btn-outline-light d-md-none m-3" @click="open = !open">
        <i class="fas fa-bars"></i> Menu
    </button>

    <div>
        <div class="sidebar-header text-center py-3">
            <h4 class="m-0 text-white">
                <i class="fas fa-futbol me-2"></i> FansZone
            </h4>
        </div>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('leagues') ? 'active' : '' }}" href="">
                        <i class="fas fa-trophy"></i> Leagues
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('matches/live') ? 'active' : '' }}" href="#">
                        <i class="fas fa-futbol"></i> Live Matches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-bell"></i> Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-newspaper"></i> News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-poll"></i> Polls & Quizzes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-users"></i> Fan Zone</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-calendar"></i> Team Matches</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="subscription-badge mt-auto text-center">
        Subscription: <strong>{{ auth()->user()->plan ?? 'N/A' }}</strong><br>
        Expires in:
        <strong>
            @if(auth()->user()->expires_at)
                {{ now()->diffInDays(auth()->user()->expires_at) }} Days
            @else
                N/A
            @endif
        </strong>
    </div>
</div>
