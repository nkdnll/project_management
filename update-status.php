<?php
session_start();
include 'log1.php';

$connection = new mysqli("localhost", "root", "", "projectmanagement");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ass_id'], $_POST['status'])) {
    $ass_id = (int)$_POST['ass_id'];
    $new_status = trim($_POST['status']);
    $userinfo_id = $_SESSION['userinfo_ID']; // assuming it's a student updating

    if ($new_status !== '') {
        // update status
        $stmt = $connection->prepare("UPDATE assignment_students SET status = ? WHERE assigned_id = ? AND userinfo_ID = ?");
        $stmt->bind_param("sii", $new_status, $ass_id, $userinfo_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: Projects.php");
exit();
?>
