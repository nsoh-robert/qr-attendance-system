
<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../config/attendance.php";

class AttendanceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            "mysql:host=localhost;dbname=qr_attendance_test_db;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS attendance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                student_name VARCHAR(100) NOT NULL,
                index_number VARCHAR(50) NOT NULL,
                attendance_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE (session_id, index_number)
            )
        ");

        $this->pdo->exec("DELETE FROM attendance");
    }

    // Test 1: Accept new student attendance
    public function testNewStudentAttendanceIsAccepted(): void
    {
        $result = recordAttendance(
            $this->pdo,
            1,
            "open",
            "Test Student",
            "TEST001"
        );

        $this->assertSame(
            "Attendance recorded successfully!",
            $result
        );
    }

    // Test 2: Reject duplicate attendance
    public function testDuplicateAttendanceIsRejected(): void
    {
        recordAttendance(
            $this->pdo,
            1,
            "open",
            "Test Student",
            "TEST001"
        );

        $result = recordAttendance(
            $this->pdo,
            1,
            "open",
            "Test Student",
            "TEST001"
        );

        $this->assertSame(
            "You have already recorded attendance.",
            $result
        );
    }

    // Test 3: Reject attendance when session is frozen
    public function testFrozenSessionRejectsAttendance(): void
    {
        $result = recordAttendance(
            $this->pdo,
            1,
            "frozen",
            "Test Student",
            "TEST002"
        );

        $this->assertSame(
            "Attendance is closed.",
            $result
        );
    }

    // Test 4: Reject empty student details
    public function testEmptyStudentDetailsAreRejected(): void
    {
        $result = recordAttendance(
            $this->pdo,
            1,
            "open",
            "",
            ""
        );

        $this->assertSame(
            "Please complete all fields.",
            $result
        );
    }

    // Test 5: Reject empty index number
    public function testEmptyIndexNumberIsRejected(): void
    {
        $result = recordAttendance(
            $this->pdo,
            1,
            "open",
            "Test Student",
            ""
        );

        $this->assertSame(
            "Please complete all fields.",
            $result
        );
    }

    // Test 6: Confirm attendance is saved in the database
    public function testAttendanceIsSavedInDatabase(): void
    {
        recordAttendance(
            $this->pdo,
            1,
            "open",
            "Database Test Student",
            "DBTEST001"
        );

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM attendance
            WHERE session_id = ? AND index_number = ?
        ");

        $stmt->execute([1, "DBTEST001"]);

        $count = (int) $stmt->fetchColumn();

        $this->assertSame(1, $count);
    }
}
