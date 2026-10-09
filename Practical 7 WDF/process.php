<?php

// Check whether the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Please submit the registration form first.");
}

// Function to display safe messages
function showMessage($title, $message, $success = false)
{
    $safeTitle = htmlspecialchars(
        $title,
        ENT_QUOTES,
        "UTF-8"
    );

    $safeMessage = htmlspecialchars(
        $message,
        ENT_QUOTES,
        "UTF-8"
    );

    $color = $success ? "green" : "red";

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Result</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <h1>' . $safeTitle . '</h1>
            <p style="color:' . $color . ';">' . $safeMessage . '</p>
            <a class="view-link" href="register.html">Back to Registration</a>
            <a class="view-link" href="view.php">View Registered Students</a>
        </div>
    </body>
    </html>';
}

// Read and sanitize input
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$course = trim($_POST["course"] ?? "");

// Store all validation errors here
$errors = [];

// Validate name
if ($name === "" || strlen($name) > 80) {
    $errors[] = "Name is required and must not exceed 80 characters.";
} elseif (!preg_match("/^[\p{L}\p{M} .'-]+$/u", $name)) {
    $errors[] = "Name contains invalid characters.";
}

// Validate email
if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// Validate mobile number
if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}

// Validate course
$allowedCourses = [
    "BTech IT",
    "BTech CSE",
    "BCA",
    "MCA"
];

if (!in_array($course, $allowedCourses, true)) {
    $errors[] = "Please select a valid course.";
}

// Display errors if validation fails
if (!empty($errors)) {
    showMessage(
        "Registration Failed",
        implode(" ", $errors)
    );
    exit;
}

// Escape values before displaying them in HTML.
// CSV storage itself uses fputcsv() below.
$name = trim($name);
$email = trim($email);

// Open CSV file safely
$file = __DIR__ . "/students.csv";

$handle = fopen($file, "c+");

if ($handle === false) {
    showMessage(
        "Storage Error",
        "Unable to open the CSV file. Check folder permissions."
    );
    exit;
}

// Lock file to prevent simultaneous writes
if (!flock($handle, LOCK_EX)) {
    fclose($handle);

    showMessage(
        "Storage Error",
        "Unable to lock the CSV file."
    );
    exit;
}

// Move to the end of the file
fseek($handle, 0, SEEK_END);

// Add header if file is empty
if (ftell($handle) === 0) {
    fputcsv($handle, [
        "Name",
        "Email",
        "Mobile",
        "Course"
    ]);
}

// Save the record
$saved = fputcsv($handle, [
    $name,
    $email,
    $mobile,
    $course
]);

// Ensure the data is written
fflush($handle);

// Release lock and close file
flock($handle, LOCK_UN);
fclose($handle);

// Display the result
if ($saved !== false) {
    showMessage(
        "Registration Successful!",
        "Your student information has been saved successfully.",
        true
    );
} else {
    showMessage(
        "Registration Failed",
        "Unable to save your information."
    );
}

?>