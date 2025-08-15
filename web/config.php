<?php
// Database configuration
// Update these values to match your MySQL setup

$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',  // Change this to your MySQL username
    'password' => '',      // Change this to your MySQL password
    'charset' => 'utf8mb4'
];

// PDO connection options
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Create PDO connection
function getDBConnection() {
    global $config, $pdo_options;
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], $pdo_options);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function formatScore($score) {
    return number_format($score, 1);
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function getScoreClass($score) {
    if ($score >= 250) return 'text-success';
    if ($score >= 200) return 'text-primary';
    if ($score >= 150) return 'text-warning';
    return 'text-danger';
}

function getScoreBadge($score) {
    if ($score >= 300) return 'bg-danger';
    if ($score >= 250) return 'bg-success';
    if ($score >= 200) return 'bg-primary';
    if ($score >= 150) return 'bg-warning';
    return 'bg-secondary';
}
?>
