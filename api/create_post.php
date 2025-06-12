<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $timestamp = $_POST['timestamp']; // Expected format: YYYY-MM-DD HH:MM:SS

    if (empty($content) || empty($timestamp)) {
        http_response_code(400);
        echo json_encode(['message' => 'Content and timestamp are required']);
        exit;
    }

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['message' => 'Database connection error: ' . $conn->connect_error]);
        exit;
    }

    $content = $conn->real_escape_string($content);
    $timestamp = $conn->real_escape_string($timestamp);

    $sql = "INSERT INTO posts (user_id, content, timestamp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Error preparing statement: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("iss", $user_id, $content, $timestamp);

    if ($stmt->execute()) {
        $post_id = $conn->insert_id; // Get the ID of the newly inserted post

        // Fetch the newly created post for response
        $sql = "SELECT p.*, u.username, u.profile_picture FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $post_id);
        $stmt2->execute();
        $result = $stmt2->get_result();
        $post = $result->fetch_assoc();

        http_response_code(201);
        echo json_encode(['message' => 'Post created successfully', 'post' => $post]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error creating post: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>