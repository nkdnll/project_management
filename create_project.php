<?php
include 'log1.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['project_name']) ? trim($_POST['project_name']) : '';
    $due_date = isset($_POST['due_date']) ? trim($_POST['due_date']) : '';

    if (!empty($title) && !empty($due_date)) {
        // Retrieve existing projects or start a new list
        $projects = isset($_SESSION['projects']) ? $_SESSION['projects'] : [];

        // Add the new project
        $projects[] = [
            'title' => $title,
            'due_date' => $due_date
        ];

        // Save the updated list to session
        $_SESSION['projects'] = $projects;
    }

    // Redirect back to the team project page
    header("Location: team_proj.php");
    exit();
}
