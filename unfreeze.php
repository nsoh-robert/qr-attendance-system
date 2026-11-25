
<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

$lecturer_id = (int) $_SESSION["lecturer_id"];
$session_id = (int) ($_GET["id"] ?? 0);

if ($session_id <= 0) {
    die("Invalid attendance session.");
}

$stmt = $pdo->prepare("
    UPDATE sessions
    SET status = 'open'
    WHERE id = ? AND lecturer_id = ?
");

$stmt->execute([$session_id, $lecturer_id]);

header("Location: dashboard.php");
exit;
