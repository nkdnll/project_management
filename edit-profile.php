<?php
session_start();
include 'log1.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["email"])) {
    $email = $_SESSION["email"];

    // Start preparing updates
    $updates = [];
    $params = [];
    $types = "";

    // List of updatable fields
    $fields = [
        "FIRSTNAME", "MIDDLENAME", "LASTNAME", "CITIZENSHIP",
        "SUFFIX", "SEX", "BIRTHDAY", "CURRENT_SCHOOL", "RELIGION"
    ];

    // Loop over the fields and only add non-empty ones to the update
    foreach ($fields as $field) {
        if (!empty($_POST[$field])) {
            $updates[] = "$field=?";
            $params[] = $_POST[$field];
            $types .= "s";
        }
    }

    if (!empty($updates)) {
        $sql = "UPDATE userinfo SET " . implode(", ", $updates) . " WHERE EMAIL=?";
        $params[] = $email;
        $types .= "s";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Update successful!";
            } else {
                echo "No changes made.";
            }
            $stmt->close();
        } else {
            echo "SQL error: " . $conn->error;
        }
    } else {
        echo "No fields provided to update.";
    }
} else {
    echo "Unauthorized request.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profile</title>
  <link rel="stylesheet" href="" /> <!-- Replace with your actual CSS file -->
  <style>
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        padding: 30px;
    }
    .edit-container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    form label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
    }
    form input {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
    .done-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
    .done-btn:hover {
        background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="edit-container">
    <h2>Edit Profile</h2>
    <form method="post" action="edit-profile.php">
      <label>First Name</label>
      <input type="text" name="FIRSTNAME" value="<?= htmlspecialchars($user['FIRSTNAME'] ?? '') ?>"  />

      <label>Middle Name</label>
      <input type="text" name="MIDDLENAME" value="<?= htmlspecialchars($user['MIDDLENAME'] ?? '') ?>" />

      <label>Last Name</label>
      <input type="text" name="LASTNAME" value="<?= htmlspecialchars($user['LASTNAME'] ?? '') ?>" />

      <label>Suffix</label>
      <input type="text" name="SUFFIX" value="<?= htmlspecialchars($user['SUFFIX'] ?? '') ?>" />

      <label>Citizenship</label>
      <input type="text" name="CITIZENSHIP" value="<?= htmlspecialchars($user['CITIZENSHIP'] ?? '') ?>"  />

      <label>Sex</label>
      <input type="text" name="SEX" value="<?= htmlspecialchars($user['SEX'] ?? '') ?>" />

      <label>Birthday</label>
      <input type="date" name="BIRTHDAY" value="<?= htmlspecialchars($user['BIRTHDAY'] ?? '') ?>"  />


      <label>Current School</label>
      <input type="text" name="CURRENT_SCHOOL" value="<?= htmlspecialchars($user['CURRENT_SCHOOL'] ?? '') ?>"  />

      <button type="submit" name="update" class="done-btn">Save Changes</button>
    </form>
  </div>
</body>
</html>
