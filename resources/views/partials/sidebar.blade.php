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

    {{-- CORE --}}
    <li class="nav-item text-uppercase text-secondary small px-3 mt-3">
        Core
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           href="{{ route('dashboard') }}">
            <i class="fas fa-home me-2"></i> Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('my-matches') ? 'active' : '' }}"
           href="#">
            <i class="fas fa-calendar-alt me-2"></i> My Matches
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('leagues') ? 'active' : '' }}"
           href="#">
            <i class="fas fa-trophy me-2"></i> Leagues
        </a>
    </li>


    {{-- COMMUNITY --}}
    <li class="nav-item text-uppercase text-secondary small px-3 mt-4">
        Community
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('fan-zone') ? 'active' : '' }}"
           href="#">
            <i class="fas fa-users me-2"></i> Fan Zone
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('notifications') ? 'active' : '' }}"
           href="#">
            <i class="fas fa-bell me-2"></i> Notifications
        </a>
    </li>


    {{-- EXPLORE --}}
    <li class="nav-item text-uppercase text-secondary small px-3 mt-4">
        Explore
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('matches/live') ? 'active' : '' }}"
           href="#">
            <i class="fas fa-futbol me-2"></i> Live Matches
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-newspaper me-2"></i> News
        </a>
    </li>


    {{-- ENGAGEMENT (Future Features) --}}
    <li class="nav-item text-uppercase text-secondary small px-3 mt-4">
        Engagement
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('polls') ? 'active' : '' }}"
           href="{{ route('polls') }}">
            <i class="fas fa-poll me-2"></i> Polls
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('quizzes') ? 'active' : '' }}"
           href="{{ route('quizzes') }}">
            <i class="fas fa-brain me-2"></i> Quizzes
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-lightbulb me-2"></i> Trivia
        </a>
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
