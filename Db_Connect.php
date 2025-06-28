<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'group_assignment';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set timezone to Asia/Kuala_Lumpur
$conn->query("SET time_zone = '+08:00'");
?>