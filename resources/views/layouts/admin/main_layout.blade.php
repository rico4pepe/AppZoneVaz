<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sports Platform Admin Dashboard</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #0d6efd;
      --secondary: #6c757d;
      --sidebar-width: 250px;
    }
    
    body {
      overflow-x: hidden;
      background-color: #f8f9fa;
    }
    
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: var(--sidebar-width);
      background-color: #212529;
      padding-top: 20px;
      z-index: 1000;
      transition: all 0.3s;
    }
    
    .sidebar-header {
      padding: 0 15px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      color: white;
    }
    
    .sidebar .nav-link {
      color: rgba(255,255,255,0.8);
      border-left: 4px solid transparent;
      padding: 12px 15px;
    }
    
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      color: white;
      background-color: rgba(255,255,255,0.1);
      border-left-color: var(--primary);
    }
    
    .sidebar .nav-link i {
      width: 24px;
      text-align: center;
      margin-right: 10px;
    }
    
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 20px;
      transition: all 0.3s;
    }
    
    .card {
      margin-bottom: 20px;
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .sidebar {
        margin-left: -var(--sidebar-width);
      }
      
      .sidebar.show {
        margin-left: 0;
      }
      
      .main-content {
        margin-left: 0;
      }
      
      body.sidebar-open .main-content {
        margin-left: var(--sidebar-width);
      }
      
      .navbar-toggler {
        display: block;
      }
    }
    
    .option-item {
      margin-bottom: 10px;
    }
    
    .admin-avatar {
      width: 40px;
      height: 40px;
      background-color: var(--primary);
      color: white;
    }
  </style>
  @livewireStyles
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <h3 class="d-flex align-items-center">
        <i class="fas fa-futbol me-2"></i> SportsFan
      </h3>
    </div>
    
    <ul class="nav flex-column mt-3">
      <li class="nav-item">
            <a href="#" class="nav-link">
        
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
           <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="fas fa-question-circle"></i> Content
        </a>
      </li>

       <li class="nav-item">
        <a href="{{ route('admin.contentlist') }}" class="nav-link {{ request()->routeIs('admin.contentlist') ? 'active' : '' }}">
          <i class="fas fa-question-circle"></i> Content List
        </a>
      </li>
      <li class="nav-item">
     <a href="{{ route('admin.leaderboard') }}" class="nav-link {{ request()->routeIs('admin.leaderboard') ? 'active' : '' }}">
          <i class="fas fa-trophy"></i> Leaderboards
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-comments"></i> Chat Rooms
        </a>
      </li>
      <li class="nav-item">
       <a href="{{ route('admin.chatmoderation') }}" class="nav-link {{ request()->routeIs('admin.chatmoderation') ? 'active' : '' }}">
          <i class="fas fa-user-shield"></i> Moderation
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-users"></i> Users
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-cog"></i> Settings
        </a>
      </li>
    </ul>
  </div>
  
  @yield('content')
  <!-- Main Content -->
  {{-- Main Content Area --}}

  <!-- Bootstrap JS Bundle with Popper -->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

  
  <!-- Custom JS -->

@livewireScripts

</body>
</html>
