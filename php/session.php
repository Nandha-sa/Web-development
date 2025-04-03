<?php
require __DIR__ . '/vendor/autoload.php';

$redis = new Predis\Client([
    "scheme" => "tcp",
    "host" => "127.0.0.1",
    "port" => 6380
]);

function storeSession($userId, $token) {
    global $redis;
    $redis->setex("session:$token", 3600, $userId);  // Stores session for 1 hour
    error_log("Session stored: Token - $token, UserID - $userId"); // Debugging
}

function getSessionUser($token) {
    global $redis;
    $userId = $redis->get("session:$token");  // Retrieve userId from Redis

    if (!$userId) {
        error_log("Invalid or expired session for token: $token"); // Logs the issue
    }

    return $userId;
}
?>