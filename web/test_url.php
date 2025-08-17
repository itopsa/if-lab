<?php
echo "<h1>URL Test Page</h1>";
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Page parameter: " . (isset($_GET['page']) ? $_GET['page'] : 'not set') . "</p>";
echo "<p>All GET parameters:</p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";
?>
