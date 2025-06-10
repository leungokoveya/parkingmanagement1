<?php
$host = 'localhost';
$port = '3307'; // Your MariaDB port
$dbname = 'carpark';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
