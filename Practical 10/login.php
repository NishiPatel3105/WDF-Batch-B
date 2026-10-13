<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["last_activity"] = time();

        $update = $conn->prepare(
            "UPDATE users SET last_login = NOW() WHERE id = ?"
        );
        $update->bind_param("i", $user["id"]);
        $update->execute();

        if ($user["role"] == "admin") {
            header("Location: admin.php");
        } else {
            header("Location: student.php");
        }
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Login</title>
</head>
<body>
    <h2>Login Form</h2>

    <p style="color:red">
        <?= htmlspecialchars($error) ?>
    </p>

    <form method="POST">
        Email:
        <input type="email" name="email" required>
        <br><br>

        Password:
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
