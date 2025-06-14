<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'your_password';
$database = 'projectmanagement';

// Backup file name
$backupFile = 'backups/' . $database . '_' . date("Y-m-d_H-i-s") . '.sql';

// Create backups folder if not exists
if (!file_exists('backups')) {
    mkdir('backups', 0777, true);
}

// Use mysqldump to export database
$command = "mysqldump --user={$username} --password={$password} --host={$host} {$database} > {$backupFile}";
system($command, $output);

if ($output === 0) {
    echo "✅ Backup completed: $backupFile";
} else {
    echo "❌ Backup failed!";
}
?>
