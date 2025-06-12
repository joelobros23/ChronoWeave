<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set a success message (optional)
$response = array('status' => 'success', 'message' => 'Logged out successfully.');
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>