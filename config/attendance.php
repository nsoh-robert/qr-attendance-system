
<?php

function recordAttendance(
    PDO $pdo,
    int $sessionId,
    string $sessionStatus,
    string $studentName,
    string $indexNumber
): string {

    if ($sessionStatus !== "open") {
        return "Attendance is closed.";
    }

    $studentName = trim($studentName);
    $indexNumber = trim($indexNumber);

    if ($studentName === "" || $indexNumber === "") {
        return "Please complete all fields.";
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO attendance
            (session_id, student_name, index_number)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $sessionId,
            $studentName,
            $indexNumber
        ]);

        return "Attendance recorded successfully!";

    } catch (PDOException $e) {

        if ($e->getCode() === "23000") {
            return "You have already recorded attendance.";
        }

        return "Something went wrong. Please try again.";
    }
}
