<?php
// MySQL Connection
$severname = "localhost";
$username = "guvi_user";
$password = " ";
$dbname = "guvi_db;

$mysqli = new mysqli($servername, $username , $password, $dbname);
// Check MySQL connection
if ($mysqli->connect_error) {
    die(json_encode(["success" => false, "message" => "MySQL Connection Failed: " . $mysqli->connect_error]));
}

// MongoDB Connection
require __DIR__ . '/vendor/autoload.php';

try {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $mongoDB = $mongoClient->selectDatabase('user_profiles');
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => "MongoDB Connection Failed: " . $e->getMessage()]));
}

?>
