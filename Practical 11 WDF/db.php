<?php
$conn = mysqli_connect("localhost", "root", "", "studenthub");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>