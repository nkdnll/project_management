<?php
session_start();
$connection = new mysqli("localhost", "root", "", "projectmanagement");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ass_id = $_POST['ass_id'];
    $status = $_POST['status'];

    // Update all students for this assignment with new status
    $sql = "UPDATE assignment_students SET status = ? WHERE assigned_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("si", $status, $ass_id);
    $stmt->execute();

    // Redirect back to the completed page
    header("Location: completed.php");
    exit();
}
?>
