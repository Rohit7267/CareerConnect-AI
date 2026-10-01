<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$sql = "
SELECT applications.id AS application_id,
       users.name,
       users.email,
       jobs.title,
       applications.status
FROM applications
JOIN users ON applications.student_id = users.id
JOIN jobs ON applications.job_id = jobs.id
ORDER BY applications.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Smart Shortlisting</title>

    <style>
        body {
            font-family: Arial;
            background: #f1f5f9;
            padding: 30px;
        }

        .box {
            background: white;
            padding: 20px;
            margin: 15px auto;
            max-width: 700px;
            border-radius: 15px;
            box-shadow: 0 5px 15px #ddd;
        }

        .high {
            color: green;
            font-weight: bold;
        }

        .medium {
            color: orange;
            font-weight: bold;
        }

        .low {
            color: red;
            font-weight: bold;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<h1>🤖 Smart Candidate Shortlisting</h1>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<div class="box">

    <h2>👤 <?php echo htmlspecialchars($row["name"]); ?></h2>

    <p>📧 <?php echo htmlspecialchars($row["email"]); ?></p>

    <p>💼 <?php echo htmlspecialchars($row["title"]); ?></p>

    <?php
        /*
         Demo score.
         Later this can use the exact AI ranking score.
        */
        $score = 80;

        if ($score >= 80) {
            echo '<p class="high">🟢 Highly Recommended</p>';
        } elseif ($score >= 60) {
            echo '<p class="medium">🟡 Consider Candidate</p>';
        } else {
            echo '<p class="low">🔴 Low Match</p>';
        }
    ?>

    <p>AI Match Score: <b><?php echo $score; ?>%</b></p>

    <p>Status: <?php echo htmlspecialchars($row["status"]); ?></p>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="box">
    <h2>📭 No Applicants</h2>
</div>

<?php endif; ?>

<a href="candidate-ranking.php">← Back to AI Ranking</a>

</body>
</html>