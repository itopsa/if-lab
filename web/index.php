<?php
require_once 'config.php';
require_once 'auth.php';

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Database Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateX(5px);
        }
        
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.8em;
            padding: 6px 12px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-bowling-ball me-2"></i>
                        IF Bowling Club
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link <?php echo $page == 'bowlers' ? 'active' : ''; ?>" href="?page=bowlers">
                            <i class="fas fa-users me-2"></i>Bowlers
                        </a>
                        <a class="nav-link <?php echo $page == 'series' ? 'active' : ''; ?>" href="?page=series">
                            <i class="fas fa-list me-2"></i>Series Details
                        </a>
                        <a class="nav-link <?php echo $page == 'locations' ? 'active' : ''; ?>" href="?page=locations">
                            <i class="fas fa-map-marker-alt me-2"></i>Locations
                        </a>
                        <a class="nav-link <?php echo $page == 'recent' ? 'active' : ''; ?>" href="?page=recent">
                            <i class="fas fa-clock me-2"></i>Recent Performance
                        </a>
                        <a class="nav-link <?php echo $page == 'tournaments' ? 'active' : ''; ?>" href="?page=tournaments">
                            <i class="fas fa-trophy me-2"></i>Tournaments
                        </a>
                        <a class="nav-link <?php echo $page == 'admin' ? 'active' : ''; ?>" href="?page=admin">
                            <i class="fas fa-cog me-2"></i>Admin
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <?php
                    switch($page) {
                        case 'dashboard':
                            include 'pages/dashboard.php';
                            break;
                        case 'bowlers':
                            include 'pages/bowlers.php';
                            break;
                        case 'series':
                            include 'pages/series.php';
                            break;
                        case 'locations':
                            include 'pages/locations.php';
                            break;
                        case 'recent':
                            include 'pages/recent.php';
                            break;
                        case 'tournaments':
                            include 'pages/tournaments.php';
                            break;
                        case 'admin':
                            include 'pages/admin.php';
                            break;
                        case 'login':
                            include 'pages/login.php';
                            exit; // Exit to prevent main layout from loading
                            break;
                        default:
                            include 'pages/dashboard.php';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
