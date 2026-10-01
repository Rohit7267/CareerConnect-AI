<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "college") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";


// ===============================
// DASHBOARD STATISTICS
// ===============================

// Total Students
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role = 'student'"
);

$total_students = $result->fetch_assoc()["total"];


// Total Jobs / Internships
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM jobs"
);

$total_jobs = $result->fetch_assoc()["total"];


// Total Applications
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM applications"
);

$total_applications = $result->fetch_assoc()["total"];


// Total Shortlisted
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE status = 'Shortlisted'"
);

$total_shortlisted = $result->fetch_assoc()["total"];


// Total Rejected
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE status = 'Rejected'"
);

$total_rejected = $result->fetch_assoc()["total"];


// Pending
$pending = $total_applications
           - $total_shortlisted
           - $total_rejected;


// ===============================
// STUDENT OVERVIEW
// ===============================

$sql = "SELECT
            users.name,
            users.email,
            student_profiles.course,
            student_profiles.branch,
            student_profiles.cgpa,
            COUNT(applications.id) AS total_applications,

            SUM(
                CASE
                    WHEN applications.status = 'Shortlisted'
                    THEN 1
                    ELSE 0
                END
            ) AS shortlisted

        FROM users

        LEFT JOIN student_profiles
            ON users.id = student_profiles.user_id

        LEFT JOIN applications
            ON users.id = applications.student_id

        WHERE users.role = 'student'

        GROUP BY users.id

        ORDER BY users.name";

$students_result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>College Dashboard</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial, sans-serif;

    background: linear-gradient(
        135deg,
        #667eea,
        #764ba2
    );

    min-height: 100vh;

    padding: 30px;
}


.container {

    max-width: 1100px;

    margin: auto;

}


.header {

    background: white;

    padding: 30px;

    border-radius: 20px;

    margin-bottom: 25px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.15);

}


.header h1 {

    margin: 0;

    color: #333;

}


.header h2 {

    margin-top: 0;

    color: #333;

}


.header p {

    color: #666;

}


.cards {

    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));

    gap: 20px;

}


.card {

    background: white;

    padding: 30px;

    border-radius: 18px;

    text-align: center;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.15);

}


.icon {

    font-size: 40px;

}


.number {

    font-size: 35px;

    font-weight: bold;

    color: #667eea;

    margin: 10px 0;

}


.card p {

    color: #666;

}


.student-card {

    background: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 15px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.15);

}


.student-card h2 {

    margin-top: 0;

    color: #333;

}


.student-card p {

    color: #555;

    line-height: 1.6;

}


.section {

    margin-top: 35px;

}


.logout {

    display: inline-block;

    margin-top: 25px;

    padding: 12px 20px;

    background: #dc3545;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: bold;

}

</style>

</head>


<body>


<div class="container">


<!-- ===============================
     HEADER
================================ -->

<div class="header">

<h1>
🎓 College Dashboard
</h1>

<p>

Welcome,
<strong>
<?php echo htmlspecialchars($_SESSION["name"]); ?>
</strong>
👋

</p>

<p>

Monitor students, internships,
applications and placement activities.

</p>

</div>



<!-- ===============================
     MAIN STATISTICS
================================ -->

<div class="cards">


<div class="card">

<div class="icon">
👨‍🎓
</div>

<div class="number">
<?php echo $total_students; ?>
</div>

<p>
Total Students
</p>

</div>



<div class="card">

<div class="icon">
💼
</div>

<div class="number">
<?php echo $total_jobs; ?>
</div>

<p>
Total Jobs / Internships
</p>

</div>



<div class="card">

<div class="icon">
📝
</div>

<div class="number">
<?php echo $total_applications; ?>
</div>

<p>
Total Applications
</p>

</div>



<div class="card">

<div class="icon">
🎯
</div>

<div class="number">
<?php echo $total_shortlisted; ?>
</div>

<p>
Shortlisted Students
</p>

</div>


</div>



<!-- ===============================
     STUDENT OVERVIEW
================================ -->

<div class="section">

<div class="header">

<h2>
👨‍🎓 Student Overview
</h2>

<p>
College can monitor student profiles
and application status.
</p>

</div>



<?php if ($students_result && $students_result->num_rows > 0): ?>


<?php while ($student = $students_result->fetch_assoc()): ?>


<div class="student-card">


<h2>

👤
<?php echo htmlspecialchars($student["name"]); ?>

</h2>


<p>

📧 Email:

<?php echo htmlspecialchars($student["email"]); ?>

</p>


<p>

📚 Course:

<?php

echo htmlspecialchars(
    $student["course"] ?? "Not Added"
);

?>

</p>


<p>

🔧 Branch:

<?php

echo htmlspecialchars(
    $student["branch"] ?? "Not Added"
);

?>

</p>


<p>

⭐ CGPA:

<?php

echo htmlspecialchars(
    $student["cgpa"] ?? "Not Added"
);

?>

</p>


<p>

📝 Applications:

<strong>

<?php echo $student["total_applications"]; ?>

</strong>

</p>


<p>

🎯 Shortlisted:

<strong>

<?php echo $student["shortlisted"] ?? 0; ?>

</strong>

</p>


</div>


<?php endwhile; ?>


<?php else: ?>


<div class="student-card">

<h2>
📭 No Students Found
</h2>

<p>
Registered students will appear here.
</p>

</div>


<?php endif; ?>


</div>



<!-- ===============================
     PLACEMENT ANALYTICS
================================ -->

<div class="section">


<div class="header">

<h2>
📊 Placement Analytics
</h2>

<p>
Overview of student application
and placement activity.
</p>

</div>



<div class="cards">


<!-- Applications -->

<div class="card">

<div class="icon">
📝
</div>

<div class="number">

<?php echo $total_applications; ?>

</div>

<p>
Total Applications
</p>

</div>



<!-- Shortlisted -->

<div class="card">

<div class="icon">
🎯
</div>

<div class="number">

<?php echo $total_shortlisted; ?>

</div>

<p>
Shortlisted
</p>

</div>



<!-- Rejected -->

<div class="card">

<div class="icon">
❌
</div>

<div class="number">

<?php echo $total_rejected; ?>

</div>

<p>
Rejected
</p>

</div>



<!-- Pending -->

<div class="card">

<div class="icon">
⏳
</div>

<div class="number">

<?php echo $pending; ?>

</div>

<p>
Pending
</p>

</div>
<div class="card">
    <h3>📈 Placement Rate</h3>
    <p>
        <?php
        if ($total_applications > 0) {
            $placement_rate = ($total_shortlisted / $total_applications) * 100;
            echo round($placement_rate, 2) . "%";
        } else {
            echo "0%";
        }
        ?>
    </p>
</div>


</div>

</div>



<!-- ===============================
     LOGOUT
================================ -->

<a
href="../logout.php"
class="logout"
>

Logout

</a>


</div>


</body>

</html>