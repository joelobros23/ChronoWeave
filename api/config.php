<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chronoweave";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set the Content-Type header to JSON for API responses
header('Content-Type: application/json');
?>