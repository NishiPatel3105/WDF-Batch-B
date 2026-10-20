<?php
include 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, course FROM students WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    exit("Student not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    if ($name == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $course == '') {
        $error = "Please enter valid details.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE students SET name=?, email=?, course=? WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt, "sssi", $name, $email, $course, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?msg=" . urlencode("Student updated successfully!"));
            exit;
        } else {
            $error = mysqli_stmt_errno($stmt) == 1062
                ? "This email belongs to another student."
                : "Unable to update student.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-box">
    <h2>Edit Student</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name"
               value="<?= htmlspecialchars($student['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($student['email']) ?>" required>

        <label>Course</label>
        <select name="course" required>
            <?php foreach (['BCA', 'BTech', 'BSc IT'] as $c): ?>
                <option value="<?= $c ?>"
                    <?= $student['course'] == $c ? 'selected' : '' ?>>
                    <?= $c ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update Student</button>
    </form>

    <a href="index.php">Back to Students</a>
</div>
</body>
</html>