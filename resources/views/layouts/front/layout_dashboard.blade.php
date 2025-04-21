<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTN Football FansZone - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
       :root {
    --primary: #ffcc00; /* MTN Yellow */
    --secondary: #000000; /* Black */
    --success: #198754;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #0dcaf0;
    --light: #f8f9fa;
    --dark: #212529;
    --sidebar-width: 250px;
}

body {
    background-color: #fffbea;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.sidebar {
    width: var(--sidebar-width);
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    background: var(--secondary);
    color: white;
    z-index: 1000;
    transition: transform 0.3s ease;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    color: black;
    background-color: var(--primary);
}

.subscription-badge {
    background: var(--primary);
    color: black;
    font-weight: bold;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    margin: 15px;
}

.content {
    margin-left: var(--sidebar-width);
    padding: 20px;
}

.navbar {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, background-color 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    background-color: rgba(255, 204, 0, 0.3); /* Slight Yellow Background */
}

.footer {
    position: relative;
    bottom: 0;
    width: 100%;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .content {
        margin-left: 0;
    }
}

    </style>
</head>
<body>


    
    @yield('content')


  {{-- Load Chat React Component --}}
    @viteReactRefresh
    @vite('resources/js/app.js')
    @vite('resources/js/chat.jsx')
    @vite('resources/js/poll.jsx')
    @vite('resources/js/quizz.jsx')
    
    


    


       @stack('scripts')
          <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
