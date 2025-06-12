<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once("config.php");

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($data->post_id) && isset($data->user_id) && isset($data->content)) {
        $post_id = mysqli_real_escape_string($conn, $data->post_id);
        $user_id = mysqli_real_escape_string($conn, $data->user_id);
        $content = mysqli_real_escape_string($conn, $data->content);

        if (empty($post_id) || empty($user_id) || empty($content)) {
            echo json_encode(array("message" => "All fields are required", "status" => false));
            exit();
        }

        $sql = "INSERT INTO comments (post_id, user_id, content) VALUES ('$post_id', '$user_id', '$content')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(array("message" => "Comment created successfully", "status" => true));
        } else {
            echo json_encode(array("message" => "Comment creation failed: " . mysqli_error($conn), "status" => false));
        }
    } else {
        echo json_encode(array("message" => "Missing required fields (post_id, user_id, content)", "status" => false));
    }
} else {
    echo json_encode(array("message" => "Invalid request method", "status" => false));
}

mysqli_close($conn);
?>