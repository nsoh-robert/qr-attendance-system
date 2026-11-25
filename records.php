
<?php
session_start();
require_once "config/database.php";

// Protect the page
if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

$lecturer_id = (int) $_SESSION["lecturer_id"];
$session_id = (int) ($_GET["id"] ?? 0);

// Get the lecturer's attendance session
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

// Get students who recorded attendance
$stmt = $pdo->prepare("
    SELECT student_name, index_number, attendance_time
    FROM attendance
    WHERE session_id = ?
    ORDER BY attendance_time DESC
");
$stmt->execute([$session_id]);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .records-container {
            width: 95%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .records-container h2 {
            margin-bottom: 10px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1d4ed8;
            color: white;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="records-container">

    <h2>Attendance Records</h2>

    <p>
        Course: <strong>
            <?= htmlspecialchars($session["course_name"]) ?>
        </strong>
    </p>

    <p>Total Students Present: <strong><?= count($students) ?></strong></p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Student Name</th>
                    <th>Index Number</th>
                    <th>Attendance Time</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($students) > 0): ?>

                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($student["student_name"]) ?></td>
                            <td><?= htmlspecialchars($student["index_number"]) ?></td>
                            <td><?= htmlspecialchars($student["attendance_time"]) ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4">No attendance records found.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a class="back-link" href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>
