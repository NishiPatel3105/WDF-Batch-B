<?php
$conn = new mysqli("localhost", "root", "", "secure_login_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";
?>
