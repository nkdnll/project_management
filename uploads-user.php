<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "projectmanagement");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['userinfo_ID'])) {
    echo "No user is logged in.";
    exit();
}

$userID = $_SESSION['userinfo_ID'];

// Check for uploaded file
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $file = $_FILES['profile_pic'];
    $targetDir = "uploads/";

    // Create the directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = time() . '_' . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;

    // Check file type
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit();
    }

    // Upload and update database
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        $update = "UPDATE userinfo SET PROFILE_PIC = ? WHERE userinfo_ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("si", $targetFilePath, $userID);
        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Failed to update profile picture.";
        }
    } else {
        echo "Upload failed.";
    }
} else {
    echo "No file selected or error during upload.";
}
?>
