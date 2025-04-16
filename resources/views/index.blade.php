<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approot Football FansZone</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> <!-- Custom CSS -->
    <style> 
    :root {
    --primary: #ffcc00; /* MTN Yellow */
    --secondary: #000000; /* Black */
    --success: #198754;
    --accent: #ff5722;
    --light-bg: #f8f9fa;
    --dark-bg: #212529;
}

body {
    background-color: #fffbea;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.navbar-brand {
    font-weight: 700;
    color: var(--primary);
}

.football-header {
    background: linear-gradient(135deg, #ffcc00 0%, #e6b800 100%);
    color: white;
    height: 240px;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.login-card {
    border-radius: 12px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    background-color: white;
    border: none;
    overflow: hidden;
}

.login-card .card-header {
    background-color: var(--primary);
    color: fff;
    font-weight: 600;
    border: none;
    padding: 1rem;
}

.feature-card {
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.feature-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.match-card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    margin-bottom: 1rem;
}

.match-card .card-header {
    background-color: var(--light-bg);
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    font-weight: 600;
    color: var(--secondary);
}

.live-badge {
    background-color: #dc3545;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.sidebar {
    background-color: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.sidebar-heading {
    font-weight: 600;
    color: var(--dark-bg);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary);
}

.nav-pills .nav-link.active {
    background-color: var(--primary);
    color: var(--secondary);
}

.nav-pills .nav-link {
    color: var(--dark-bg);
    border-radius: 6px;
}

.nav-pills .nav-link:hover:not(.active) {
    background-color: var(--light-bg);
}

.news-card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    margin-bottom: 1rem;
    transition: transform 0.3s;
}

.news-card:hover {
    transform: translateY(-5px);
}

    </style>
</head>
<body>

    <div id="landing-page">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-futbol me-2"></i>
                    Approot Football FansZone
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="football-header mb-5">
            <div class="football-pattern"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold">Football Updates Like Never Before</h1>
                        <p class="lead">Join the ultimate football fan community to get real-time updates, chat with fellow fans, and never miss a goal!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card login-card">
                        <div class="card-header">
                            <i class="fas fa-sign-in-alt me-2"></i> Login to FansZone
                        </div>
                        <div class="card-body p-4">

                                                    @if (session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                            <form method="POST" action="{{ route('login.custom') }}">
                             @csrf
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" name="phone_number" id="phone_number"  placeholder="Enter your phone number">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="token" class="form-label">Login Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control"  name="token" id="token" placeholder="Only needed if you're on WiFi">
                                    </div>
                                    <div class="form-text">Not yet subscribed? Send "JOIN" to *12345* to get started!</div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card login-card">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-2"></i> How It Works
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary text-white rounded-circle p-2 me-3 text-center" style="width: 40px; height: 40px;">1</div>
                                <div>
                                    <h5 class="mb-1">Subscribe via SMS</h5>
                                    <p class="text-muted">Send "JOIN" to *12345* to activate your account</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary text-white rounded-circle p-2 me-3 text-center" style="width: 40px; height: 40px;">2</div>
                                <div>
                                    <h5 class="mb-1">Login to Portal</h5>
                                    <p class="text-muted">Use your phone number and token to access the platform if you not browsing with your data - wifi, hotspot</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     

        <footer class="bg-dark text-white text-center py-3 mt-5">
        
            <p class="mb-0">&copy; {{ date('Y') }}  Approot Football FansZone. All Rights Reserved.</p>
        </footer>
    </div>


      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
   

