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
            transition: all 0.3s;
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
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="sidebar-header text-center py-3">
                <h4 class="m-0 text-white">
                    <i class="fas fa-futbol me-2"></i> FansZone
                </h4>
            </div>
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-trophy"></i> Leagues</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-futbol"></i> Live Matches</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-newspaper"></i> News</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-poll"></i> Polls & Quizzes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-users"></i> Fan Zone</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-calendar"></i> Team Matches</a></li>
                </ul>
            </div>
        </div>
        <div class="subscription-badge">
            Subscription: <strong>Daily</strong><br>
            Expires in: <strong>7 Days</strong>
        </div>
    </div>
    
    @yield('content')


    
    
    <footer class="bg-dark text-white text-center py-3 mt-5 footer">
        <p class="mb-0">&copy; 2025 MTN Football FansZone. All Rights Reserved.</p>
    </footer>


        <!-- Custom Modal for Club Selection -->
        <div class="modal fade" id="clubModal" tabindex="-1" aria-labelledby="clubModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clubModalLabel">Select Your Favorite Club</h5>
                    </div>
                    <div class="modal-body">
                        <label for="clubSelect" class="form-label">Which football club do you support?</label>
                        <input type="text" class="form-control" id="clubSelect" placeholder="Enter your favorite club">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveClub">Save</button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

            // Close sidebar when clicking outside it
        document.addEventListener('click', function(event) {
            let sidebar = document.getElementById('sidebar');
            let toggleButton = document.getElementById('sidebarToggle');
            
            if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
