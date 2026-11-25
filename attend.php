
<?php

require_once "config/database.php";
require_once "config/attendance.php";

$message = "";

$token = trim($_GET["token"] ?? $_POST["token"] ?? "");

if ($token === "") {
    die("Invalid attendance link.");
}

// Find the attendance session
$stmt = $pdo->prepare("
    SELECT id, course_name, status
    FROM sessions
    WHERE session_token = ?
    LIMIT 1
");

$stmt->execute([$token]);
$session = $stmt->fetch();

if (!$session) {
    die("Attendance session not found.");
}

// Process attendance submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_name = $_POST["student_name"] ?? "";
    $index_number = $_POST["index_number"] ?? "";

    $message = recordAttendance(
        $pdo,
        (int) $session["id"],
        $session["status"],
        $student_name,
        $index_number
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Attendance</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="auth-container">

    <h2>Student Attendance</h2>

    <h3>
        <?= htmlspecialchars($session["course_name"]) ?>
    </h3>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($session["status"] === "frozen"): ?>

        <p>Attendance is currently closed by the lecturer.</p>

    <?php elseif (
        $message !== "Attendance recorded successfully!" &&
        $message !== "You have already recorded attendance."
    ): ?>

        <form method="POST">

            <input
                type="hidden"
                name="token"
                value="<?= htmlspecialchars($token) ?>"
            >

            <input
                type="text"
                name="student_name"
                placeholder="Your Full Name"
                required
            >

            <input
                type="text"
                name="index_number"
                placeholder="Your Index Number"
                required
            >

            <button type="submit">
                Submit Attendance
            </button>

        </form>

    <?php endif; ?>

</div>

</body>
</html>
