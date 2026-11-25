
<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../config/authentication.php";

class AuthenticationTest extends TestCase
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

        $stmt = $this->pdo->prepare("
            INSERT INTO lecturers (full_name, email, password)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            "Test Lecturer",
            "lecturer@test.com",
            password_hash("password123", PASSWORD_DEFAULT)
        ]);
    }

    // Test 1: Successful lecturer login
    public function testValidLecturerCanLogin(): void
    {
        $result = authenticateLecturer(
            $this->pdo,
            "lecturer@test.com",
            "password123"
        );

        $this->assertIsArray($result);
        $this->assertSame("Test Lecturer", $result["full_name"]);
    }

    // Test 2: Reject incorrect password
    public function testIncorrectPasswordIsRejected(): void
    {
        $result = authenticateLecturer(
            $this->pdo,
            "lecturer@test.com",
            "wrongpassword"
        );

        $this->assertFalse($result);
    }

    // Test 3: Reject unknown email
    public function testUnknownEmailIsRejected(): void
    {
        $result = authenticateLecturer(
            $this->pdo,
            "unknown@test.com",
            "password123"
        );

        $this->assertFalse($result);
    }

    // Test 4: Reject empty login details
    public function testEmptyLoginDetailsAreRejected(): void
    {
        $result = authenticateLecturer(
            $this->pdo,
            "",
            ""
        );

        $this->assertFalse($result);
    }
}
