<?php
session_start();
include 'log1.php'; // ✅ Include log1.php

$connection = new mysqli("localhost", "root", "", "projectmanagement");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ass_id'], $_POST['status'])) {
    $ass_id = (int)$_POST['ass_id'];
    $new_status = $_POST['status'];
    $username = $_SESSION['username'];

    // Fetch userinfo_ID and old status
    $stmt = $connection->prepare("SELECT userinfo_ID, status FROM assignment_students WHERE assigned_id = ? AND username = ?");
    $stmt->bind_param("is", $ass_id, $username);
    $stmt->execute();
    $stmt->bind_result($userinfo_id, $old_status);
    $stmt->fetch();
    $stmt->close();

    if ($old_status !== $new_status) {
        // Update the status
        $stmt = $connection->prepare("UPDATE assignment_students SET status = ? WHERE assigned_id = ? AND username = ?");
        $stmt->bind_param("sis", $new_status, $ass_id, $username);
        $stmt->execute();
        $stmt->close();

        // Insert into status_logs
        $log = $connection->prepare("INSERT INTO status_logs (assigned_id, userinfo_id, old_status, new_status) VALUES (?, ?, ?, ?)");
        $log->bind_param("iiss", $ass_id, $userinfo_id, $old_status, $new_status);
        $log->execute();
        $log->close();

        // ✅ Log the action using log1.php
        logTransaction(
            'student',
            $username,
            'Status Change',
            "Changed status for assignment ID $ass_id from '$old_status' to '$new_status'"
        );
    }
}

header("Location: Projects.php");
exit;

?>
