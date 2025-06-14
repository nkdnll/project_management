<?php
$conn = new mysqli('localhost', 'root', '', 'projectmanagement');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("FLUSH TABLES;"); // This flushes all changes to disk
echo "✅ Checkpoint created: all tables flushed.";
?>
