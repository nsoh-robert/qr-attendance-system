
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "config/database.php";
require_once "config/registration.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $result = registerLecturer(
        $pdo,
        $full_name,
        $email,
        $password
    );

    if ($result === "Registration successful.") {

        header("Location: login.php?registered=1");
        exit;

    } else {

        $message = $result;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Registration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="auth-container">

    <h2>Lecturer Registration</h2>

    <?php if ($message): ?>
        <p style="color:red;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="full_name"
               placeholder="Full Name" required>

        <br><br>

        <input type="email" name="email"
               placeholder="Email Address" required>

        <br><br>

        <input type="password" name="password"
               placeholder="Password (minimum 8 characters)"
               minlength="8" required>

        <br><br>

        <button type="submit">Register</button>

    </form>

    <p>Already registered?
        <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>
