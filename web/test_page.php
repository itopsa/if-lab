<?php
// Simple test to check page routing
echo "<h1>Test Page</h1>";
echo "<p>This is a test page to check if routing is working.</p>";
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Page parameter: " . (isset($_GET['page']) ? $_GET['page'] : 'not set') . "</p>";
echo "<p>All GET parameters:</p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";
?>
