<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$dbHost     = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName     = 'menu';

// Dynamically determine the base URL based on the folder location relative to Document Root.
// This allows you to rename the project folder to anything (e.g. 'menu') without breaking links.
$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$projectRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));

// Standardize casing for comparison (especially on Windows)
$docRootLower = strtolower($docRoot);
$projectRootLower = strtolower($projectRoot);

if (strpos($projectRootLower, $docRootLower) === 0) {
    $relativePath = substr($projectRoot, strlen($docRoot));
    $BASE_URL = '/' . trim($relativePath, '/') . '/';
    if ($BASE_URL === '//') {
        $BASE_URL = '/';
    }
} else {
    // Safe fallback using the immediate directory name
    $BASE_URL = '/' . basename($projectRoot) . '/';
}
// Set connection timeout to 5 seconds
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage() . "<br>Make sure MySQL is running in XAMPP.");
}

// Include core helpers
include_once __DIR__ . '/auth.php';
?>


