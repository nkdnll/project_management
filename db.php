<?php
$host = 'localhost';
$username = 'root';     // or your database username
$password = '';         // or your password
$database = 'projectmanagement'; // your actual database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
