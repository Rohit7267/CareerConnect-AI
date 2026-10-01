<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$student_id = $_SESSION["user_id"];

/* Check job ID */

if (!isset($_GET["job_id"]) || !is_numeric($_GET["job_id"])) {
    die("Invalid Job ID");
}

$job_id = (int) $_GET["job_id"];


/* Check whether job exists */

$sql = "SELECT id, title FROM jobs WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Job / Internship not found.");
}

$job = $result->fetch_assoc();

$stmt->close();


/* Check if already applied */

$sql = "SELECT id FROM applications
        WHERE job_id = ? AND student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    echo "<h2>⚠️ You have already applied for this opportunity.</h2>";
    echo "<a href='internships.php'>← Back to Internships</a>";

    exit;
}

$stmt->close();


/* Insert application */

$sql = "INSERT INTO applications (job_id, student_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $job_id, $student_id);

if ($stmt->execute()) {

    ?>

    <!DOCTYPE html>
    <html>

    <head>

        <title>Application Submitted</title>

        <style>

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea, #764ba2);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .card {
                background: white;
                padding: 40px;
                border-radius: 18px;
                text-align: center;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            }

            h1 {
                color: #333;
            }

            p {
                color: #666;
            }

            a {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 22px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }

        </style>

    </head>

    <body>

        <div class="card">

            <h1>🎉 Application Submitted!</h1>

            <p>
                Your application for
                <strong>
                    <?php echo htmlspecialchars($job["title"]); ?>
                </strong>
                has been submitted successfully.
            </p>

            <a href="internships.php">
                ← Back to Internships
            </a>

        </div>

    </body>

    </html>

    <?php

} else {

    echo "Application failed: " . $stmt->error;
}

$stmt->close();

?>