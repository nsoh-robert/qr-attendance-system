<?php
session_start();
require_once "config/database.php";

// Protect dashboard
if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.php");
    exit;
}

$lecturer_id = (int) $_SESSION["lecturer_id"];
$lecturer_name = $_SESSION["lecturer_name"];

// Get lecturer sessions
$stmt = $pdo->prepare("
    SELECT
        s.*,
        COUNT(a.id) AS total_students
    FROM sessions s
    LEFT JOIN attendance a ON s.id = a.session_id
    WHERE s.lecturer_id = ?
    GROUP BY s.id
    ORDER BY s.id DESC
");

$stmt->execute([$lecturer_id]);
$sessions = $stmt->fetchAll();

$total_sessions = count($sessions);

$total_students = 0;
foreach ($sessions as $session) {
    $total_students += (int) $session["total_students"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .header {
            background: #172554;
            color: white;
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: white;
            text-decoration: none;
            background: #dc2626;
            padding: 10px 15px;
            border-radius: 6px;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 35px auto;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }

        .card h3 {
            color: #64748b;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 28px;
            font-weight: bold;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }

        .section h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        button {
            background: #1d4ed8;
            color: white;
            border: none;
            padding: 13px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #1e40af;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 550px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .status {
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
        }

        .open {
            background: #dcfce7;
            color: #166534;
        }

        .frozen {
            background: #fee2e2;
            color: #991b1b;
        }

        .action {
            color: #1d4ed8;
            text-decoration: none;
            margin-right: 10px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .action:hover {
            text-decoration: underline;
        }

        .freeze-action {
            color: #dc2626;
        }

        .unfreeze-action {
            color: #15803d;
        }

        @media(max-width: 600px) {
            .header {
                padding: 15px;
            }

            .container {
                width: 94%;
            }

            .section {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h2>QR Attendance</h2>
    <a href="logout.php">Logout</a>
</header>

<div class="container">

    <div class="welcome">
        <h1>Welcome, <?= htmlspecialchars($lecturer_name) ?></h1>
        <p>Manage your lecture attendance here.</p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Total Sessions</h3>
            <p><?= $total_sessions ?></p>
        </div>

        <div class="card">
            <h3>Total Attendance</h3>
            <p id="totalAttendance"><?= $total_students ?></p>
        </div>

    </div>

    <div class="section">

        <h2>Create Attendance Session</h2>

        <form action="create-session.php" method="POST">

            <input
                type="text"
                name="course_name"
                placeholder="Enter Course Name"
                required
            >

            <button type="submit">Create Session</button>

        </form>

    </div>

    <div class="section">

        <h2>My Attendance Sessions</h2>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($sessions)): ?>

                    <tr>
                        <td colspan="5">No attendance sessions created yet.</td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($sessions as $session): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($session["course_name"]) ?>
                            </td>

                            <td>
                                <?= date("d M Y", strtotime($session["created_at"])) ?>
                            </td>

                            <td>
                                <span
                                    class="attendance-count"
                                    data-session-id="<?= (int) $session["id"] ?>">
                                    <?= (int) $session["total_students"] ?>
                                </span>
                            </td>

                            <td>
                                <span class="status <?= htmlspecialchars($session["status"]) ?>">
                                    <?= ucfirst($session["status"]) ?>
                                </span>
                            </td>

                            <td>
                                <a class="action"
                                   href="records.php?id=<?= (int) $session["id"] ?>">
                                    Records
                                </a>

                                <a class="action"
                                   href="qr-code.php?id=<?= (int) $session["id"] ?>">
                                    View QR Code
                                </a>

                                <?php if ($session["status"] === "open"): ?>

                                    <a class="action freeze-action"
                                       href="freeze.php?id=<?= (int) $session["id"] ?>"
                                       onclick="return confirm('Freeze this attendance session?')">
                                        Freeze
                                    </a>

                                <?php else: ?>

                                    <a class="action unfreeze-action"
                                       href="unfreeze.php?id=<?= (int) $session["id"] ?>"
                                       onclick="return confirm('Reopen this attendance session?')">
                                        Unfreeze
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
async function updateAttendanceCounts() {

    const counters = document.querySelectorAll(".attendance-count");

    let grandTotal = 0;

    for (const counter of counters) {

        const sessionId = counter.dataset.sessionId;

        try {

            const response = await fetch(
                "attendance-count.php?id=" + encodeURIComponent(sessionId),
                { cache: "no-store" }
            );

            if (!response.ok) {
                throw new Error("Unable to retrieve attendance count.");
            }

            const data = await response.json();

            if (typeof data.total_students === "number") {
                counter.textContent = data.total_students;
            }

        } catch (error) {
            console.error(error);
        }

        grandTotal += parseInt(counter.textContent, 10) || 0;
    }

    document.getElementById("totalAttendance").textContent = grandTotal;
}

// Update immediately and every 5 seconds
updateAttendanceCounts();
setInterval(updateAttendanceCounts, 5000);
</script>

</body>
</html>
