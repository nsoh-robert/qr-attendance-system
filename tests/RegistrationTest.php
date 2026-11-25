
<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../config/registration.php";

class RegistrationTest extends TestCase
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
            CREATE TABLE IF NOT EXISTS lecturers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $this->pdo->exec("DELETE FROM lecturers");
    }

    // Test 1: Register a new lecturer
    public function testNewLecturerCanRegister(): void
    {
        $result = registerLecturer(
            $this->pdo,
            "Test Lecturer",
            "test@example.com",
            "password123"
        );

        $this->assertSame("Registration successful.", $result);
    }

    // Test 2: Confirm password is hashed
    public function testLecturerPasswordIsHashed(): void
    {
        registerLecturer(
            $this->pdo,
            "Test Lecturer",
            "test@example.com",
            "password123"
        );

        $stmt = $this->pdo->prepare("
            SELECT password FROM lecturers WHERE email = ?
        ");

        $stmt->execute(["test@example.com"]);

        $hashedPassword = $stmt->fetchColumn();

        $this->assertNotSame("password123", $hashedPassword);

        $this->assertTrue(
            password_verify("password123", $hashedPassword)
        );
    }

    // Test 3: Reject duplicate email
    public function testDuplicateEmailIsRejected(): void
    {
        registerLecturer(
            $this->pdo,
            "First Lecturer",
            "test@example.com",
            "password123"
        );

        $result = registerLecturer(
            $this->pdo,
            "Second Lecturer",
            "test@example.com",
            "password456"
        );

        $this->assertSame(
            "Email address is already registered.",
            $result
        );
    }

    // Test 4: Reject empty registration details
    public function testEmptyRegistrationDetailsAreRejected(): void
    {
        $result = registerLecturer(
            $this->pdo,
            "",
            "",
            ""
        );

        $this->assertSame(
            "Please complete all fields. Password must be at least 8 characters.",
            $result
        );
    }

    // Test 5: Reject short password
    public function testShortPasswordIsRejected(): void
    {
        $result = registerLecturer(
            $this->pdo,
            "Test Lecturer",
            "test@example.com",
            "12345"
        );

        $this->assertSame(
            "Please complete all fields. Password must be at least 8 characters.",
            $result
        );
    }

    // Test 6: Reject invalid email
    public function testInvalidEmailIsRejected(): void
    {
        $result = registerLecturer(
            $this->pdo,
            "Test Lecturer",
            "invalid-email",
            "password123"
        );

        $this->assertSame(
            "Please complete all fields. Password must be at least 8 characters.",
            $result
        );
    }
}
