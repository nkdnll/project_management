<?php
session_start();
include 'log1.php';
require 'db.php';

if (!isset($_SESSION['Email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $proj_id = intval($_POST['proj_id']);
    $inputUsernames = array_filter(array_map('trim', explode(',', $_POST['usernames'])));

    // Step 1: Fetch current assigned_students
    $stmt = $conn->prepare("SELECT ass_id, assigned_students FROM assigned WHERE proj_id = ?");
    $stmt->bind_param("i", $proj_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $assignments = [];
    $existingUsernames = [];

    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row['ass_id'];
        $existingUsernames = array_merge($existingUsernames, array_map('trim', explode(',', $row['assigned_students'])));
    }

    $existingUsernames = array_unique($existingUsernames);
    $newUsernames = [];

    // Step 2: Filter only new usernames
    foreach ($inputUsernames as $username) {
        if (!in_array($username, $existingUsernames)) {
            $newUsernames[] = $username;
        }
    }

    if (!empty($newUsernames)) {
        // Step 3: Append new usernames to each assignment
        foreach ($assignments as $ass_id) {
            // Get current assigned_students for this assignment
            $stmt = $conn->prepare("SELECT assigned_students FROM assigned WHERE ass_id = ?");
            $stmt->bind_param("i", $ass_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $currentList = array_map('trim', explode(',', $row['assigned_students']));

            // Merge and save updated list
            $updatedList = implode(', ', array_unique(array_merge($currentList, $newUsernames)));

            $stmt = $conn->prepare("UPDATE assigned SET assigned_students = ? WHERE ass_id = ?");
            $stmt->bind_param("si", $updatedList, $ass_id);
            $stmt->execute();
        }

        // Step 4: Update projects table with new usernames
        $stmt = $conn->prepare("SELECT usernames FROM projects WHERE proj_id = ?");
        $stmt->bind_param("i", $proj_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingProjectUsernames = [];

        if ($row = $result->fetch_assoc()) {
            $existingProjectUsernames = array_map('trim', explode(',', $row['usernames']));
        }

        $updatedProjectList = implode(', ', array_unique(array_merge($existingProjectUsernames, $newUsernames)));
        $stmt = $conn->prepare("UPDATE projects SET usernames = ? WHERE proj_id = ?");
        $stmt->bind_param("si", $updatedProjectList, $proj_id);
        $stmt->execute();

        // Step 5: Insert new rows into assignment_students
        foreach ($assignments as $ass_id) {
            foreach ($newUsernames as $username) {
                // Find userinfo_ID from userinfo
                $stmt = $conn->prepare("SELECT userinfo_ID FROM userinfo WHERE CONCAT(FIRSTNAME, ' ', MIDDLENAME, ' ', LASTNAME) = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($user = $result->fetch_assoc()) {
                    $userinfo_ID = $user['userinfo_ID'];

                    // Check if already exists
                    $stmtCheck = $conn->prepare("SELECT id FROM assignment_students WHERE assigned_id = ? AND userinfo_ID = ?");
                    $stmtCheck->bind_param("ii", $ass_id, $userinfo_ID);
                    $stmtCheck->execute();
                    $checkResult = $stmtCheck->get_result();

                    if ($checkResult->num_rows === 0) {
                        // Insert new
                        $stmtInsert = $conn->prepare("INSERT INTO assignment_students (assigned_id, username, userinfo_ID, status) VALUES (?, ?, ?, 'Not Started')");
                        $stmtInsert->bind_param("isi", $ass_id, $username, $userinfo_ID);
                        $stmtInsert->execute();
                    }
                }
            }
        }
    }

    header("Location: Admin-project.php");
    exit();
}
