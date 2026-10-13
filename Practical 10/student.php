<?php
require "auth.php";
requireRole("student");
?>
<h1>Student Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?></p>
<a href="logout.php">Logout</a>
