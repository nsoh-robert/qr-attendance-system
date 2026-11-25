
<?php
session_start();
require_once "config/database.php";

header("Content-Type: application/json");

if (!isset($_SESSION["lecturer_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$lecturer_id = (int) $_SESSION["lecturer_id"];
$session_id = (int) ($_GET["id"] ?? 0);

if ($session_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid session"]);
    exit;
}

// Confirm that this session belongs to the lecturer
$stmt = $pdo->prepare("
    SELECT id
    FROM sessions
    WHERE id = ? AND lecturer_id = ?
");
$stmt->execute([$session_id, $lecturer_id]);

if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Session not found"]);
    exit;
}

// Count attendance records
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM attendance
    WHERE session_id = ?
");
$stmt->execute([$session_id]);

$total = (int) $stmt->fetchColumn();

echo json_encode([
    "total_students" => $total
]);
