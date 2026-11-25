
<?php
session_start();
require_once "config/database.php";

// Ensure lecturer is logged in
if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$course_name = trim($_POST["course_name"] ?? "");
$lecturer_id = (int) $_SESSION["lecturer_id"];

if ($course_name === "") {
    die("Please enter a course name.");
}

// Generate a unique attendance token
$session_token = bin2hex(random_bytes(24));

try {
    $stmt = $pdo->prepare("
        INSERT INTO sessions
        (lecturer_id, course_name, session_token, status)
        VALUES (?, ?, ?, 'open')
    ");

    $stmt->execute([
        $lecturer_id,
        $course_name,
        $session_token
    ]);

    $session_id = (int) $pdo->lastInsertId();

    header("Location: qr-code.php?id=" . $session_id);
    exit;

} catch (PDOException $e) {
    die("Unable to create attendance session. Please try again.");
}
