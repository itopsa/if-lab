<?php
// Database configuration
$host = 'localhost';
$dbname = 'bowling_db';
$username = 'root'; // Change this to your MySQL username
$password = 'MySecureP@ssw0rd2024!'; // Change this to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Database Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
                        IF Bowling
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
                        <a class="nav-link <?php echo $page == 'games' ? 'active' : ''; ?>" href="?page=games">
                            <i class="fas fa-gamepad me-2"></i>Game Details
                        </a>
                        <a class="nav-link <?php echo $page == 'tournaments' ? 'active' : ''; ?>" href="?page=tournaments">
                            <i class="fas fa-trophy me-2"></i>Tournaments
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
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
                    case 'games':
                        include 'pages/games.php';
                        break;
                    case 'tournaments':
                        include 'pages/tournaments.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</body>
</html>
