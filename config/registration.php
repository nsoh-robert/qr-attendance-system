
<?php

function registerLecturer(
    PDO $pdo,
    string $fullName,
    string $email,
    string $password
): string {

    $fullName = trim($fullName);
    $email = trim($email);

    // Validate required fields
    if (
        $fullName === "" ||
        $email === "" ||
        strlen($password) < 8 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        return "Please complete all fields. Password must be at least 8 characters.";
    }

    // Hash the password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    try {
        $stmt = $pdo->prepare("
            INSERT INTO lecturers (full_name, email, password)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $fullName,
            $email,
            $hashedPassword
        ]);

        return "Registration successful.";

    } catch (PDOException $e) {

        if ($e->getCode() === "23000") {
            return "Email address is already registered.";
        }

        return "Registration failed. Please try again.";
    }
}
