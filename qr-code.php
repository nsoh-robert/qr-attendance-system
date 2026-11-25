
<?php
session_start();
require_once "config/database.php";

// Check lecturer login
if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

$session_id = (int) ($_GET["id"] ?? 0);
$lecturer_id = (int) $_SESSION["lecturer_id"];

if ($session_id <= 0) {
    die("Invalid attendance session.");
}

// Get attendance session belonging to this lecturer
$stmt = $pdo->prepare("
    SELECT *
    FROM sessions
    WHERE id = ? AND lecturer_id = ?
    LIMIT 1
");

$stmt->execute([$session_id, $lecturer_id]);
$session = $stmt->fetch();

if (!$session) {
    die("Attendance session not found.");
}

// Automatically generate attendance URL
$protocol = (
    !empty($_SERVER["HTTPS"]) &&
    $_SERVER["HTTPS"] !== "off"
) ? "https" : "http";

$host = $_SERVER["HTTP_HOST"];
$project_path = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\");

$attendance_url = $protocol . "://" . $host
    . $project_path . "/attend.php?token="
    . urlencode($session["session_token"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance QR Code</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            text-align: center;
            padding: 30px 15px;
            margin: 0;
        }

        .container {
            background: white;
            max-width: 450px;
            width: 100%;
            margin: 30px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        #qrcode {
            display: flex;
            justify-content: center;
            margin: 25px auto;
        }

        .status {
            font-weight: bold;
            padding: 10px;
            border-radius: 6px;
        }

        .open {
            color: #15803d;
            background: #dcfce7;
        }

        .frozen {
            color: #991b1b;
            background: #fee2e2;
        }

        .back {
            display: inline-block;
            background: #1d4ed8;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
        }

        .url {
            word-break: break-all;
            font-size: 13px;
            color: #64748b;
            margin-top: 20px;
        }

        .copy-button {
            background: #15803d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        .copy-button:hover {
            background: #166534;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Attendance QR Code</h2>

    <h3>
        <?= htmlspecialchars($session["course_name"], ENT_QUOTES, "UTF-8") ?>
    </h3>

    <p class="status <?= $session["status"] === "open" ? "open" : "frozen" ?>">
        Attendance is <?= strtoupper(htmlspecialchars($session["status"])) ?>
    </p>

    <div id="qrcode"></div>

    <p>
        Ask students to scan this QR code to register attendance.
    </p>

    <p class="url" id="attendanceUrl">
        <?= htmlspecialchars($attendance_url, ENT_QUOTES, "UTF-8") ?>
    </p>

    <button type="button" class="copy-button" onclick="copyAttendanceLink()">
        Copy Attendance Link
    </button>

    <br>

    <a href="dashboard.php" class="back">
        Back to Dashboard
    </a>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
const attendanceUrl = <?= json_encode($attendance_url) ?>;

new QRCode(document.getElementById("qrcode"), {
    text: attendanceUrl,
    width: 220,
    height: 220
});

function copyAttendanceLink() {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(attendanceUrl)
            .then(function() {
                alert("Attendance link copied successfully!");
            })
            .catch(function() {
                alert("Unable to copy link. Please copy it manually.");
            });
    } else {
        alert("Please copy the attendance link displayed on the page.");
    }
}
</script>

</body>
</html>
