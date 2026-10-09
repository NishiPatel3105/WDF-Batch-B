<?php
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO students (name, email, password)
                VALUES (:name, :email, :password)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);

        $message = "Student registered successfully!";
    } catch (PDOException $e) {
        $message = "Registration failed. Please check the email or database.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>
<body>

<h2>Student Registration Form</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register Student</button>
</form>

</body>
</html>

