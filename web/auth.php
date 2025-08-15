<?php
session_start();

// Simple authentication configuration
$admin_username = 'admin';
$admin_password = 'bowling2024!'; // Change this to a secure password

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Login function
function login($username, $password) {
    global $admin_username, $admin_password;
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }
    return false;
}

// Logout function
function logout() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    session_destroy();
}

// Handle login form submission
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: ?page=admin');
        exit;
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    logout();
    header('Location: ?page=login');
    exit;
}
?>
