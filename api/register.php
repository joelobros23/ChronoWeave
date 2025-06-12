<?php
header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (empty($username) || empty($password) || empty($email)) {
        http_response_code(400);
        echo json_encode(['message' => 'Please fill all fields.']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid email format.']);
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['message' => 'Database connection error: ' . $conn->connect_error]);
        exit;
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'Username or email already exists.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to register user: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed.']);
}
?>