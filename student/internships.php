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


/* =========================
   GET STUDENT SKILLS
========================= */

$sql = "SELECT s.skill_name, ss.proficiency
        FROM student_skills ss
        JOIN skills s ON ss.skill_id = s.id
        WHERE ss.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

$student_skills = [];

while ($row = $result->fetch_assoc()) {

    $student_skills[strtolower(trim($row["skill_name"]))] =
        (int)$row["proficiency"];
}

$stmt->close();


/* =========================
   GET ALL JOBS
========================= */

$sql = "SELECT jobs.*, users.name AS company_name
        FROM jobs
        JOIN users ON jobs.company_id = users.id
        ORDER BY jobs.created_at DESC";

$result = $conn->query($sql);

$jobs = [];


/* =========================
   CALCULATE MATCH SCORE
========================= */

while ($job = $result->fetch_assoc()) {

    $required_skills = explode(",", $job["required_skills"]);

    $total_skills = count($required_skills);

    $matched_skills = [];
    $missing_skills = [];

    $score = 0;

    foreach ($required_skills as $required_skill) {

        $required_skill = strtolower(trim($required_skill));

        if (isset($student_skills[$required_skill])) {

            $matched_skills[] = $required_skill;

            /*
             * Proficiency contributes to match score.
             * Example:
             * PHP 80% = 80 points
             */

            $score += $student_skills[$required_skill];

        } else {

            $missing_skills[] = $required_skill;
        }
    }


    /* Calculate average match percentage */

    if ($total_skills > 0) {

        $match_score = round($score / $total_skills);

    } else {

        $match_score = 0;
    }


    /* Keep score between 0 and 100 */

    if ($match_score > 100) {
        $match_score = 100;
    }


    $job["match_score"] = $match_score;
    $job["matched_skills"] = $matched_skills;
    $job["missing_skills"] = $missing_skills;

    $jobs[] = $job;
}


/* Sort highest match first */

usort($jobs, function ($a, $b) {

    return $b["match_score"] - $a["match_score"];

});

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Internship Matching</title>

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


.header {

    background: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 25px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.15);

}


.header h1 {

    margin: 0;

    color: #333;

}


.header p {

    color: #666;

}


/* JOB CARD */

.job-card {

    background: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 20px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.15);

}


.job-header {

    display: flex;

    justify-content: space-between;

    gap: 20px;

    align-items: center;

}


.job-title {

    font-size: 24px;

    font-weight: bold;

    color: #333;

}


.company {

    color: #666;

    margin-top: 7px;

}


.match {

    font-size: 25px;

    font-weight: bold;

    color: #667eea;

}


.info {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;

    margin: 15px 0;

}


.badge {

    background: #f0f0f0;

    padding: 8px 12px;

    border-radius: 20px;

    color: #555;

}


.skills {

    margin-top: 20px;

}


.skill-box {

    display: inline-block;

    padding: 7px 12px;

    margin: 5px;

    border-radius: 20px;

    font-size: 14px;

}


.matched {

    background: #d4edda;

    color: #155724;

}


.missing {

    background: #f8d7da;

    color: #721c24;

}


.apply {

    display: inline-block;

    margin-top: 20px;

    padding: 12px 22px;

    background: #667eea;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: bold;

}


.apply:hover {

    opacity: 0.9;

}


.back {

    display: inline-block;

    margin-bottom: 20px;

    color: white;

    text-decoration: none;

}


.no-jobs {

    background: white;

    padding: 40px;

    text-align: center;

    border-radius: 18px;

}


@media(max-width: 700px) {

    body {
        padding: 15px;
    }

    .job-header {
        flex-direction: column;
        align-items: flex-start;
    }

}

</style>

</head>


<body>


<div class="container">


<a href="dashboard.php" class="back">
    ← Back to Dashboard
</a>


<div class="header">

<h1>🎯 Internship Matching</h1>

<p>
Welcome <?php echo htmlspecialchars($_SESSION["name"]); ?>!
</p>

<p>
Based on your skills, we found the most relevant opportunities for you.
</p>

</div>


<?php if (count($jobs) == 0): ?>

<div class="no-jobs">

<h2>😔 No Internships Available</h2>

<p>
Companies have not posted any opportunities yet.
</p>

</div>

<?php endif; ?>


<?php foreach ($jobs as $job): ?>


<div class="job-card">


<div class="job-header">


<div>

<div class="job-title">

<?php echo htmlspecialchars($job["title"]); ?>

</div>


<div class="company">

🏢 <?php echo htmlspecialchars($job["company_name"]); ?>

</div>

</div>


<div class="match">

<?php echo $job["match_score"]; ?>% Match

</div>


</div>


<div class="info">

<span class="badge">

📍 <?php echo htmlspecialchars($job["location"]); ?>

</span>


<span class="badge">

💼 <?php echo htmlspecialchars($job["job_type"]); ?>

</span>


<?php if (!empty($job["salary"])): ?>

<span class="badge">

💰 <?php echo htmlspecialchars($job["salary"]); ?>

</span>

<?php endif; ?>

</div>


<p>

<?php echo nl2br(htmlspecialchars($job["description"])); ?>

</p>


<!-- MATCHED SKILLS -->

<div class="skills">

<strong>✅ Your Matching Skills</strong>

<br>


<?php if (count($job["matched_skills"]) > 0): ?>

<?php foreach ($job["matched_skills"] as $skill): ?>

<span class="skill-box matched">

✓ <?php echo htmlspecialchars($skill); ?>

</span>

<?php endforeach; ?>

<?php else: ?>

<p>No matching skills found.</p>

<?php endif; ?>


</div>


<!-- MISSING SKILLS -->

<div class="skills">

<strong>⚠️ Skills to Improve</strong>

<br>


<?php if (count($job["missing_skills"]) > 0): ?>

<?php foreach ($job["missing_skills"] as $skill): ?>

<span class="skill-box missing">

⚠ <?php echo htmlspecialchars($skill); ?>

</span>

<?php endforeach; ?>

<?php else: ?>

<p>🎉 You have all the required skills!</p>

<?php endif; ?>


</div>


<a
    href="apply.php?job_id=<?php echo $job['id']; ?>"
    class="apply"
>
    🚀 Apply Now
</a>

</div>


<?php endforeach; ?>


</div>


</body>

</html>