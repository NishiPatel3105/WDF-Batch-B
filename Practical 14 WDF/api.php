<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "StudentHub");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $result = $conn->query(
        "SELECT id, name, email, course FROM students"
    );

    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $students
    ]);
}

elseif ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $course = trim($input["course"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $course === "") {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Enter a valid name, email and course"
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $email, $course);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Student added successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Unable to add student"
        ]);
    }

    $stmt->close();
}

elseif ($method === "PUT") {
    $input = json_decode(file_get_contents("php://input"), true);

    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $course = trim($input["course"] ?? "");

    if (!$id || $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) || $course === "") {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid student details"
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE students SET name=?, email=?, course=? WHERE id=?"
    );
    $stmt->bind_param("sssi", $name, $email, $course, $id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode([
            "success" => true,
            "message" => "Student updated successfully"
        ]);
    }

    $stmt->close();
}

elseif ($method === "DELETE") {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid student ID"
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Student deleted successfully"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
    }

    $stmt->close();
}

else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
}

$conn->close();
?>

