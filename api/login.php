<?php
    // Enable CORS for all origins
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit; // Just exit for OPTIONS requests
    }

    // Database configuration
    require_once 'config.php';

    // Start session (if not already started)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get username and password from request
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate input
        if (empty($username) || empty($password)) {
            $response = ['status' => 'error', 'message' => 'Username and password are required.'];
            echo json_encode($response);
            exit;
        }

        try {
            // Connect to database
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL query to fetch user by username
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Check if user exists
            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, create session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    // Return success response
                    $response = ['status' => 'success', 'message' => 'Login successful.'];
                    echo json_encode($response);
                    exit;
                } else {
                    // Invalid password
                    $response = ['status' => 'error', 'message' => 'Invalid username or password.'];
                    echo json_encode($response);
                    exit;
                }
            } else {
                // User not found
                $response = ['status' => 'error', 'message' => 'Invalid username or password.'];
                echo json_encode($response);
                exit;
            }
        } catch (PDOException $e) {
            // Database error
            $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
            echo json_encode($response);
            exit;
        }
    } else {
        // Invalid request method
        $response = ['status' => 'error', 'message' => 'Invalid request method.'];
        echo json_encode($response);
        exit;
    }
?>