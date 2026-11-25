
<?php
session_start();
require_once "config/database.php";

// Check lecturer login
if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

$lecturer_id = (int) $_SESSION["lecturer_id"];
$session_id = (int) ($_GET["id"] ?? 0);

if ($session_id <= 0) {
    die("Invalid attendance session.");
}

// Freeze only a session belonging to this lecturer
$stmt = $pdo->prepare("
    UPDATE sessions
    SET status = 'frozen'
    WHERE id = ? AND lecturer_id = ?
");

$stmt->execute([$session_id, $lecturer_id]);

header("Location: dashboard.php");
exit;
