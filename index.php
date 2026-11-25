
<?php

session_start();

// Check whether the lecturer is logged in
if (isset($_SESSION["lecturer_id"])) {

    // Send logged-in lecturers to the dashboard
    header("Location: dashboard.php");
    exit;

} else {

    // Send visitors to the login page
    header("Location: login.php");
    exit;
}
