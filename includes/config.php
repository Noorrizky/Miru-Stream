<?php
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_USER', 'root'); // Your DB username (e.g., 'root')
DEFINE('DB_PASS', '');     // Your DB password (e.g., '' for root, or your password)
DEFINE('DB_NAME', 'video_streamer_db');

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset for proper encoding
$conn->set_charset("utf8mb4");
