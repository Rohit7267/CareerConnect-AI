<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Applicants</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 100vh;
    padding: 30px;
}

.container {
    max-width: 1100px;
    margin: auto;
}

.back {
    color: white;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
}

.header {
    background: white;
    padding: 25px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.header h1 {
    margin: 0;
    color: #333;
}

.header p {
    color: #666;
}

.applicant {
    background: white;
    padding: 25px;
    border-radius: 18px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.job {
    color: #667eea;
    font-size: 22px;
    font-weight: bold;
}

.student {
    font-size: 25px;
    font-weight: bold;
    margin-top: 15px;
}

.info {
    margin-top: 12px;
    line-height: 1.8;
    color: #555;
}

.status {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 15px;
    border-radius: 20px;
    background: #fff3cd;
    color: #856404;
    font-weight: bold;
}

.buttons {
    margin-top: 20px;
}

.btn {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 8px;
    color: white;
    text-decoration: none;
    margin-right: 8px;
    font-weight: bold;
}

.shortlist {
    background: #28a745;
}

.reject {
    background: #dc3545;
}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">
← Back to Dashboard
</a>

<div class="header">

<h1>👥 Applicants</h1>

<p>
Students who applied for your internships and jobs.
</p>

</div>


<!-- Applicant -->

<div class="applicant">

<div class="job">
💼 PHP Developer Intern
</div>

<div class="student">
👤 Test Student
</div>

<div class="info">

📧 Email: student@test.com
<br>

🎓 College: ABC College
<br>

📚 Course: B.Tech
<br>

🔧 Branch: Computer Science
<br>

🎓 Graduation Year: 2027
<br>

⭐ CGPA: 8.2
<br>

📅 Applied: Today

</div>

<div class="status">
🟡 Status: Applied
</div>

< class="buttons">

<a href="shortlist.php?id=1" class="btn shortlist">
    ✅ Shortlist
</a>

<a href="reject.php?id=1" class="btn reject">
    ❌ Reject
</a>

</div>

</div>


</div>

</body>

</html>