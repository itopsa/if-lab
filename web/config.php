<?php
// Database configuration
$host = 'localhost';
$dbname = 'bowling_db';
$username = 'root'; // Change this to your MySQL username
$password = 'MySecureP@ssw0rd2024!'; // Change this to your MySQL password

function getDBConnection() {
    global $host, $dbname, $username, $password;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatScore($score) {
    return number_format($score);
}

function formatAverage($average) {
    return number_format($average, 1);
}

function getSeriesTypeColor($type) {
    switch($type) {
        case 'Tour Stop': return 'primary';
        case 'Playoffs': return 'danger';
        case 'House History': return 'success';
        default: return 'secondary';
    }
}

function getScoreClass($score) {
    if ($score >= 300) return 'text-warning';
    if ($score >= 250) return 'text-danger';
    if ($score >= 200) return 'text-success';
    if ($score >= 150) return 'text-info';
    return 'text-muted';
}

function getTotalScoreClass($total) {
    if ($total >= 800) return 'text-warning';
    if ($total >= 700) return 'text-danger';
    if ($total >= 600) return 'text-success';
    if ($total >= 500) return 'text-info';
    return 'text-muted';
}

function getDexterityColor($dexterity) {
    switch($dexterity) {
        case 'Right': return 'primary';
        case 'Left': return 'success';
        case 'Ambidextrous': return 'warning';
        default: return 'secondary';
    }
}

function getStyleColor($style) {
    switch($style) {
        case '1 Handed': return 'info';
        case '2 Handed': return 'danger';
        default: return 'secondary';
    }
}

function getAverageColor($average) {
    if ($average >= 250) return 'text-danger';
    if ($average >= 220) return 'text-warning';
    if ($average >= 200) return 'text-success';
    if ($average >= 180) return 'text-info';
    return 'text-muted';
}

function getRankColor($rank) {
    if ($rank <= 3) return 'warning';
    if ($rank <= 10) return 'info';
    return 'secondary';
}
?>
