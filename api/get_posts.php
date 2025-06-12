<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get user ID (if available from session or request)
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['user_id']) ? $_GET['user_id'] : null);

// Determine if we should fetch only friends' posts or all posts
$friends_only = isset($_GET['friends_only']) && $_GET['friends_only'] === 'true';

try {
    if ($friends_only && $user_id !== null) {
        // Fetch posts from the user and their friends
        $stmt = $pdo->prepare("
            SELECT p.*, u.username, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id IN (
                SELECT friend_id FROM friendships WHERE user_id = :user_id AND status = 'accepted'
                UNION
                SELECT user_id FROM friendships WHERE friend_id = :user_id AND status = 'accepted'
                UNION
                SELECT :user_id
            )
            ORDER BY p.created_at DESC
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fetch all posts
        $stmt = $pdo->prepare("
            SELECT p.*, u.username, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    echo json_encode(['posts' => $posts]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to get posts: ' . $e->getMessage()]);
}
?>