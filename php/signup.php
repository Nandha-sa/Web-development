<?php
require 'vendor/autoload.php';  // Include MongoDB library (via Composer)
require 'db_conn.php';  // MongoDB connection setup (to be defined in 'db_conn.php')
session_start();

header('Content-Type: application/json');

// Ensure MongoDB connection is available
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");  // MongoDB URI
$database = $client->user_profiles;  // MongoDB database name
$profilesCollection = $database->profiles;  // Collection to store user profiles

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    // Sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit;
    }

    // Validate password strength
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long, with one uppercase letter and one number."]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prevent duplicate email registration in MySQL
    $checkStmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already registered"]);
        exit;
    }
    $checkStmt->close();

    // Insert user data into MySQL
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $mysqli->error]);
        exit;
    }

    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        // After MySQL insertion, create a default profile in MongoDB
        // Get the user ID from MySQL 
        $userId = $mysqli->insert_id;

        // Insert a default profile into MongoDB (empty profile for now)
        $profileData = [
            'user_id' => $userId,
            'email' => $email,
            'username' => $username,
            'age' => null,  
            'dob' => null,
            'contact' => null,
            'created_at' => new MongoDB\BSON\UTCDateTime()  // Store the creation date in MongoDB format
        ];

        // Insert the profile into MongoDB
        $insertProfileResult = $profilesCollection->insertOne($profileData);

        if ($insertProfileResult->getInsertedCount() == 1) {
            echo json_encode(["success" => true, "message" => "Registration successful! Profile created."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error creating profile in MongoDB"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }
    
    $stmt->close();
}
?>
