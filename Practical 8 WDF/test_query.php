
<?php
require 'db.php';

try {
    $sql = "SELECT * FROM students";
    $stmt = $conn->query($sql);

    echo "<h2>Student Records</h2>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . htmlspecialchars($row['id']) . "<br>";
        echo "Name: " . htmlspecialchars($row['name']) . "<br>";
        echo "Email: " . htmlspecialchars($row['email']) . "<br><br>";
    }

} catch (PDOException $e) {
    echo "Query failed!";
}
?>

