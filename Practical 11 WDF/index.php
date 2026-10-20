<?php
include 'db.php';

$search = trim($_GET['search'] ?? '');
$course = trim($_GET['course'] ?? '');

$sql = "SELECT id, name, email, course FROM students
        WHERE (name LIKE ? OR email LIKE ? OR course LIKE ?)";

if ($course != '') {
    $sql .= " AND course = ?";
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

$searchValue = "%$search%";

if ($course != '') {
    mysqli_stmt_bind_param(
        $stmt, "ssss",
        $searchValue, $searchValue, $searchValue, $course
    );
} else {
    mysqli_stmt_bind_param(
        $stmt, "sss",
        $searchValue, $searchValue, $searchValue
    );
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Student Management System</h1>

    <?php if (isset($_GET['msg'])): ?>
        <p class="success">
            <?= htmlspecialchars($_GET['msg']) ?>
        </p>
    <?php endif; ?>

    <a class="btn" href="add.php">+ Add Student</a>

    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search name, email or course"
               value="<?= htmlspecialchars($search) ?>">

        <select name="course">
            <option value="">All Courses</option>
            <?php
            $courses = ['BCA', 'BTech', 'BSc IT'];
            foreach ($courses as $c):
            ?>
                <option value="<?= $c ?>"
                    <?= $course == $c ? 'selected' : '' ?>>
                    <?= $c ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
        <a class="btn secondary" href="index.php">Reset</a>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td>
                        <a class="edit"
                           href="edit.php?id=<?= $row['id'] ?>">
                            Edit
                        </a>

                        <a class="delete"
                           href="delete.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('Are you sure you want to delete this student?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No students found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>