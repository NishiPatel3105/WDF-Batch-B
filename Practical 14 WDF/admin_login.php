<?php
session_start();
require_once "connection.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ? AND status = 'active'"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        if ($user["role"] !== "admin") {
            $error = "Access denied. Admins only.";
        } else {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            header("Location: admin_dashboard.php");
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: aliceblue;
            margin: 0;
            padding: 30px;
        }

        .login-box {
            max-width: 380px;
            margin: 70px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px lightgray;
        }

        h2 {
            color: midnightblue;
            text-align: center;
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin: 9px 0;
            box-sizing: border-box;
        }

        button {
            background: steelblue;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if ($error !== ""): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email"
               placeholder="Admin email" required>

        <input type="password" name="password"
               placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
