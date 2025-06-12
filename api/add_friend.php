<?php
header('Content-Type: application/json');
require_once 'config.php';

// Check if user is logged in (e.g., via session or token)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Validate friend_id (check if it exists in the users table)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$friend_id]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid friend ID']);
        exit;
    }

    if ($user_id == $friend_id) {
        http_response_code(400);
        echo json_encode(['message' => 'Cannot add yourself as a friend.']);
        exit;
    }
    

    try {
        // Check if a friendship already exists (either way)
        $stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
        $existing_friendship = $stmt->fetch();

        if ($existing_friendship) {
            http_response_code(409); // Conflict
            echo json_encode(['message' => 'Friend request already sent or friendship already exists.']);
            exit;
        }

        // Insert a new friend request
        $stmt = $pdo->prepare("INSERT INTO friendships (user_id, friend_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $friend_id]);

        echo json_encode(['message' => 'Friend request sent successfully.']);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to send friend request.', 'error' => $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>