<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'menu';

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
?>