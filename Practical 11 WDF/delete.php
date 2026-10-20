<?php
include 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?msg=" . urlencode("Invalid student ID."));
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $message = mysqli_stmt_affected_rows($stmt) > 0
        ? "Student deleted successfully!"
        : "Student not found.";
} else {
    $message = "Unable to delete student.";
}

header("Location: index.php?msg=" . urlencode($message));
exit;
?>