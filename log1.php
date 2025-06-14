<?php
function logTransaction($userType, $username, $action, $query) {
    $timestamp = date("Y-m-d H:i:s");
    $log = "[$timestamp] [$userType:$username] [$action] $query" . PHP_EOL;
    file_put_contents('logs/log_file.txt', $log, FILE_APPEND);
}

?>
