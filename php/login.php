<?php
require 'db_conn.php';
require 'session.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        echo json_encode(["success" => false, "message" => "Email and password are required"]);
        exit;
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!$mysqli) {
        echo json_encode(["success" => false, "message" => "Database connection error"]);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error: " . $mysqli->error]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $token = bin2hex(random_bytes(16));
            storeSession($id, $token);

            //  Include email in the response
            echo json_encode([
                "success" => true,
                "token" => $token,
                "email" => $email
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
}
?>
