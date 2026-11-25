<?php
session_start();

require_once "config/database.php";
require_once "config/authentication.php";

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful. Please log in.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $lecturer = authenticateLecturer(
        $pdo,
        $email,
        $password
    );

    if ($lecturer) {

        session_regenerate_id(true);

        $_SESSION["lecturer_id"] = $lecturer["id"];
        $_SESSION["lecturer_name"] = $lecturer["full_name"];

        header("Location: dashboard.php");
        exit;

    } else {

        $message = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">

    <h2>Lecturer Login</h2>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email"
               placeholder="Email Address" required>

        <input type="password" name="password"
               placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

    <p>Don't have an account?
        <a href="register.php">Register</a>
    </p>

</div>

</body>
</html>
