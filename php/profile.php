<?php
require 'db_conn.php';
require 'session.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if token is provided
if (!isset($_POST['token']) || empty($_POST['token'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized - No token provided"]);
    exit;
}

$token = $_POST['token'];
$userId = getSessionUser($token); // Fetch userId from Redis

// Validate user ID
if (!$userId || $userId == "0") {
    echo json_encode(["success" => false, "message" => "Session expired"]);
    exit;
}

// Fetch user email from MySQL
$stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$email = $user['email'];

// MongoDB Collection
$collection = $mongoDB->profiles;

// Profile Update - Check if POST data exists for update

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["age"], $_POST["dob"], $_POST["contact"])) {
    // Ensure the fields are not empty
    if (empty($_POST["age"]) || empty($_POST["dob"]) || empty($_POST["contact"])) {
        echo json_encode(["success" => false, "message" => "All profile fields are required."]);
        exit;
    }

    // Update or insert the profile in MongoDB
    $updateResult = $collection->updateOne(
        ["email" => $email],
        ['$set' => [
            "age" => $_POST["age"],
            "dob" => $_POST["dob"],
            "contact" => $_POST["contact"]
        ]],
        ['upsert' => true] // Insert if not found
    );

    if ($updateResult) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating profile"]);
    }
    exit;
}

// Fetch Profile from MongoDB
$userProfile = $collection->findOne(["email" => $email]);

if ($userProfile) {
    echo json_encode(["success" => true, "profile" => $userProfile]);
} else {
    echo json_encode(["success" => false, "message" => "Profile not found"]);
}
?>
