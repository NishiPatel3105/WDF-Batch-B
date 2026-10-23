<?php
session_start();
require_once "connection.php";

// Allow only logged-in admins
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get dashboard statistics
$studentCount = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$eventCount = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()["total"];
$registrationCount = $conn->query("SELECT COUNT(*) AS total FROM registrations")->fetch_assoc()["total"];
$userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];

// Get latest registrations
$latest = $conn->query("SELECT * FROM registrations ORDER BY id DESC LIMIT 5");

function logAction($conn, $userId, $action, $details) {
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Record dashboard access
if (!isset($_SESSION["dashboard_logged"])) {
    logAction($conn, $_SESSION["user_id"], "VIEW_DASHBOARD", "Admin opened dashboard");
    $_SESSION["dashboard_logged"] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<div class="sidebar">
    <h2>StudentHub</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="index.php">Home</a>
    <a href="admin_dashboard.php#registrations">Registrations</a>
    <a href="admin_dashboard.php#audit">Audit Logs</a>
    <a href="logout.php">Logout</a>
</div>

<main class="content">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION["user_name"] ?? "Admin") ?>!</p>

    <div class="cards">
        <div class="card">
            <h3>Total Users</h3>
            <p><?= $userCount ?></p>
        </div>

        <div class="card">
            <h3>Total Students</h3>
            <p><?= $studentCount ?></p>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <p><?= $eventCount ?></p>
        </div>

        <div class="card">
            <h3>Total Registrations</h3>
            <p><?= $registrationCount ?></p>
        </div>
    </div>

    <section id="registrations">
        <h2>Latest Registrations</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <?php
                        if ($latest && $latest->num_rows > 0) {
                            $fields = $latest->fetch_fields();

                            foreach ($fields as $field) {
                                echo "<th>" .
                                    htmlspecialchars($field->name) .
                                    "</th>";
                            }
                            echo "<th>Details</th>";
                        }
                        ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($latest && $latest->num_rows > 0): ?>
                        <?php while ($row = $latest->fetch_assoc()): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?= htmlspecialchars((string)($value ?? "")) ?></td>
                                <?php endforeach; ?>
                                <td>Registration record</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No registrations available yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="audit">
        <h2>Recent Audit Logs</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs = $conn->query(
                        "SELECT id, user_id, action, details, created_at
                         FROM audit_logs ORDER BY id DESC LIMIT 10"
                    );

                    while ($log = $logs->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= (int)$log["id"] ?></td>
                            <td><?= htmlspecialchars((string)($log["user_id"] ?? "")) ?></td>
                            <td><?= htmlspecialchars($log["action"]) ?></td>
                            <td><?= htmlspecialchars($log["details"] ?? "") ?></td>
                            <td><?= htmlspecialchars($log["created_at"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
