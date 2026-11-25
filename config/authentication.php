
<?php

function authenticateLecturer(
    PDO $pdo,
    string $email,
    string $password
): array|false {

    $email = trim($email);

    if ($email === "" || $password === "") {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT id, full_name, email, password
        FROM lecturers
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $lecturer = $stmt->fetch();

    if (
        $lecturer &&
        password_verify($password, $lecturer["password"])
    ) {
        return $lecturer;
    }

    return false;
}
