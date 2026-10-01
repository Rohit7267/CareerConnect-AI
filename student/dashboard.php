<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> 👋</h1>

    <p>You are logged in as Student.</p>

    <a href="../logout.php">Logout</a>
    <a href="recommendations.php">🤖 AI Recommendations</a>

<a href="internships.php">💼 Internships</a>

<a href="ai-analysis.php">🧠 AI Analysis</a>

</body>
</html>