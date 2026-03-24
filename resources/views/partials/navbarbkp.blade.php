<nav class="navbar navbar-light bg-white shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <span class="fw-bold">MTN FansZone Dashboard</span>

        <div class="d-flex align-items-center">
            <span class="me-3">{{ auth()->user()->name ?? 'User' }}  </span>
             <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>