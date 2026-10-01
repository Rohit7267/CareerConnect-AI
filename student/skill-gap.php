<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$selected_job = null;
$match_percentage = 0;
$matched_skills = [];
$missing_skills = [];


/* Get student's skills */

$student_skills = [];

$sql = "
    SELECT skills.skill_name, student_skills.proficiency
    FROM student_skills
    INNER JOIN skills ON student_skills.skill_id = skills.id
    WHERE student_skills.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $student_skills[strtolower(trim($row["skill_name"]))] =
        (int)$row["proficiency"];
}

$stmt->close();


/* Get selected job */

$job_id = intval($_GET["job_id"] ?? 0);

if ($job_id > 0) {

    $stmt = $conn->prepare("
        SELECT id, title, description, location, job_type,
               salary, required_skills
        FROM jobs
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $job_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $selected_job = $result->fetch_assoc();

    $stmt->close();
}


/* Calculate Skill Gap */

if ($selected_job) {

    $required_skills = explode(",", $selected_job["required_skills"]);

    $total_skills = 0;
    $total_score = 0;

    foreach ($required_skills as $skill) {

        $skill = trim($skill);

        if ($skill === "") {
            continue;
        }

        $total_skills++;

        $skill_key = strtolower($skill);

        if (isset($student_skills[$skill_key])) {

            $matched_skills[] = $skill;

            $total_score += $student_skills[$skill_key];

        } else {

            $missing_skills[] = $skill;
        }
    }

    if ($total_skills > 0) {
        $match_percentage = round(
            $total_score / ($total_skills * 100) * 100
        );
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Skill Gap Analysis - CareerConnect AI</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #eef2ff, #dbeafe);
    padding: 30px;
}

.container {
    max-width: 900px;
    margin: auto;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.logo {
    font-size: 25px;
    font-weight: bold;
    color: #1e3a8a;
}

.back {
    text-decoration: none;
    color: #2563eb;
    font-weight: bold;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.10);
    margin-bottom: 20px;
}

h1 {
    color: #1e3a8a;
    margin-bottom: 8px;
}

h2 {
    color: #1e3a8a;
    margin-bottom: 15px;
}

.subtitle {
    color: #666;
    margin-bottom: 25px;
}

.job-info {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.job-info p {
    margin: 8px 0;
}

.match-box {
    text-align: center;
    padding: 25px;
    background: #eff6ff;
    border-radius: 15px;
    margin-bottom: 25px;
}

.match-number {
    font-size: 48px;
    font-weight: bold;
    color: #2563eb;
}

.match-text {
    color: #475569;
    font-size: 18px;
}

.skills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
}

.skill {
    padding: 10px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.matched {
    background: #dcfce7;
    color: #166534;
}

.missing {
    background: #fee2e2;
    color: #991b1b;
}

.tip {
    background: #fef3c7;
    color: #92400e;
    padding: 15px;
    border-radius: 10px;
    margin-top: 20px;
}

.button {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
}

.empty {
    text-align: center;
    color: #666;
    padding: 30px;
}

</style>

</head>


<body>

<div class="container">


    <div class="topbar">

        <div class="logo">
            CareerConnect AI 🤖
        </div>

        <a class="back" href="internships.php">
            ← Internships
        </a>

    </div>


    <?php if (!$selected_job): ?>

        <div class="card empty">

            <h1>🎯 Skill Gap Analysis</h1>

            <p>
                Please select an internship or job to analyze your skill match.
            </p>

            <a class="button" href="internships.php">
                View Internships
            </a>

        </div>


    <?php else: ?>


        <div class="card">

            <h1>🎯 Skill Gap Analysis</h1>

            <p class="subtitle">
                Compare your skills with the skills required for this opportunity.
            </p>


            <div class="job-info">

                <h2>
                    💼 <?php echo htmlspecialchars($selected_job["title"]); ?>
                </h2>

                <p>
                    📍 <?php echo htmlspecialchars($selected_job["location"]); ?>
                </p>

                <p>
                    💰 <?php echo htmlspecialchars($selected_job["salary"]); ?>
                </p>

                <p>
                    📋 <?php echo htmlspecialchars($selected_job["job_type"]); ?>
                </p>

            </div>


            <div class="match-box">

                <div class="match-number">
                    <?php echo $match_percentage; ?>%
                </div>

                <div class="match-text">
                    🎯 Skill Match
                </div>

            </div>


            <h2>✅ Matched Skills</h2>

            <?php if (count($matched_skills) > 0): ?>

                <div class="skills">

                    <?php foreach ($matched_skills as $skill): ?>

                        <div class="skill matched">
                            ✅ <?php echo htmlspecialchars($skill); ?>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p>No matching skills found.</p>

            <?php endif; ?>


            <h2>❌ Missing Skills</h2>

            <?php if (count($missing_skills) > 0): ?>

                <div class="skills">

                    <?php foreach ($missing_skills as $skill): ?>

                        <div class="skill missing">
                            ❌ <?php echo htmlspecialchars($skill); ?>
                        </div>

                    <?php endforeach; ?>

                </div>


                <div class="tip">

                    💡 <strong>AI Recommendation:</strong>

                    Improve your missing skills to increase your chances
                    of getting shortlisted.

                </div>

            <?php else: ?>

                <div class="tip">

                    🎉 <strong>Excellent!</strong>

                    You have all the required skills for this opportunity.

                </div>

            <?php endif; ?>


            <a
                class="button"
                href="apply.php?job_id=<?php echo $selected_job["id"]; ?>"
            >
                🚀 Apply Now
            </a>

        </div>


    <?php endif; ?>


</div>

</body>

</html>