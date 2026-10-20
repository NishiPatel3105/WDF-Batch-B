<?php
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    if ($name == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $course == '') {
        $error = "Please enter valid student details.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO students (name, email, course)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $course);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?msg=" . urlencode("Student added successfully!"));
            exit;
        } else {
            $error = mysqli_stmt_errno($stmt) == 1062
                ? "This email already exists."
                : "Unable to add student.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-box">
    <h2>Add Student</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <option>BCA</option>
            <option>BTech</option>
            <option>BSc IT</option>
        </select>

        <button type="submit">Add Student</button>
    </form>

    <a href="index.php">Back to Students</a>
</div>
</body>
</html>