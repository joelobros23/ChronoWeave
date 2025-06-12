<?php
header("Content-Type: application/json");
require_once 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $bio = $_POST['bio'] ?? null;
    
    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $file_name;

        // Basic security checks (e.g., file type, size) are highly recommended here!
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo json_encode(["message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
            http_response_code(400);
            exit();
        }
        if ($_FILES["profile_picture"]["size"] > 500000) {
            echo json_encode(["message" => "Sorry, your file is too large."]);
            http_response_code(400);
            exit();
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = 'assets/uploads/' . $file_name; // Store relative path in database
        } else {
            echo json_encode(["message" => "Sorry, there was an error uploading your file."]);
            http_response_code(500);
            exit();
        }
    }
    

    $sql = "UPDATE users SET ";
    $updates = [];

    if ($username !== null) {
        $updates[] = "username = :username";
    }
    if ($email !== null) {
        $updates[] = "email = :email";
    }
    if ($bio !== null) {
        $updates[] = "bio = :bio";
    }
    if ($profile_picture !== null) {
        $updates[] = "profile_picture = :profile_picture";
    }

    if (empty($updates)) {
        echo json_encode(["message" => "No updates provided."]);
        http_response_code(400);
        exit();
    }

    $sql .= implode(", ", $updates);
    $sql .= " WHERE id = :user_id";

    try {
        $stmt = $pdo->prepare($sql);
        
        if ($username !== null) {
            $stmt->bindParam(':username', $username);
        }
        if ($email !== null) {
            $stmt->bindParam(':email', $email);
        }
        if ($bio !== null) {
            $stmt->bindParam(':bio', $bio);
        }
        if ($profile_picture !== null) {
            $stmt->bindParam(':profile_picture', $profile_picture);
        }

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Profile updated successfully."]);
        } else {
            echo json_encode(["message" => "No changes made."]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error updating profile: " . $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>