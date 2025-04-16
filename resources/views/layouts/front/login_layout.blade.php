<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approot Football FansZone</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="css/style.css">  Custom CSS -->
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
    <!-- LANDING PAGE -->
      @yield('content')

 
    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
