<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$user_id = $_SESSION["user_id"];

/* Get student's skills */
$sql = "SELECT s.skill_name, ss.proficiency
        FROM student_skills ss
        JOIN skills s ON ss.skill_id = s.id
        WHERE ss.user_id = ?
        ORDER BY ss.proficiency DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$skills = [];

while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}

/* Default values */
$total_skills = count($skills);
$total_score = 0;

$strong_skills = [];
$medium_skills = [];
$weak_skills = [];

/* Analyze skills */
foreach ($skills as $skill) {

    $name = $skill["skill_name"];
    $proficiency = (int)$skill["proficiency"];

    $total_score += $proficiency;

    if ($proficiency >= 75) {
        $strong_skills[] = $name;
    } elseif ($proficiency >= 50) {
        $medium_skills[] = $name;
    } else {
        $weak_skills[] = $name;
    }
}

/* Overall score */
if ($total_skills > 0) {
    $overall_score = round($total_score / $total_skills);
} else {
    $overall_score = 0;
}

/* Career recommendation */
$career = "Beginner Developer";

if (
    in_array("PHP", array_column($skills, "skill_name")) &&
    in_array("MySQL", array_column($skills, "skill_name"))
) {
    $career = "PHP / Full Stack Web Developer";
}

if (
    in_array("Python", array_column($skills, "skill_name"))
) {
    $career = "Python Developer";
}

if (
    in_array("React", array_column($skills, "skill_name"))
) {
    $career = "Frontend / React Developer";
}

if (
    in_array("Java", array_column($skills, "skill_name"))
) {
    $career = "Java Developer";
}

/* Learning recommendations */
$recommendations = [];

if (in_array("PHP", array_column($skills, "skill_name")) &&
    !in_array("REST API", array_column($skills, "skill_name"))) {

    $recommendations[] = "Learn REST API";
}

if (in_array("JavaScript", array_column($skills, "skill_name")) &&
    !in_array("React", array_column($skills, "skill_name"))) {

    $recommendations[] = "Learn React.js";
}

if (!in_array("Git", array_column($skills, "skill_name"))) {
    $recommendations[] = "Learn Git & GitHub";
}

if (!in_array("REST API", array_column($skills, "skill_name"))) {
    $recommendations[] = "Practice REST API";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AI Skill Analysis</title>

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
    border-radius: 15px;
    margin-bottom: 20px;
}

.header h1 {
    margin: 0;
    color: #333;
}

.header p {
    color: #666;
}

.cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.card h2 {
    margin-top: 0;
    color: #555;
}

.score {
    font-size: 45px;
    font-weight: bold;
    color: #667eea;
}

.section {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.section h2 {
    color: #333;
}

.skill {
    margin-bottom: 18px;
}

.skill-name {
    display: flex;
    justify-content: space-between;
    margin-bottom: 7px;
    font-weight: bold;
}

.progress {
    width: 100%;
    height: 12px;
    background: #eee;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: #667eea;
}

.badge {
    display: inline-block;
    padding: 8px 14px;
    margin: 5px;
    border-radius: 20px;
    background: #667eea;
    color: white;
}

.recommendation {
    background: #f5f5f5;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
}

.back {
    display: inline-block;
    background: white;
    color: #333;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 20px;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #777;
}

@media(max-width: 700px) {

    .cards {
        grid-template-columns: 1fr;
    }

    body {
        padding: 15px;
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

<h1>🤖 AI Skill Analysis</h1>

<p>
Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!
</p>

</div>


<?php if ($total_skills == 0): ?>

<div class="section empty">

<h2>No Skills Added Yet</h2>

<p>
Please add your skills first to generate your skill analysis.
</p>

<a href="skills.php">
    Add Skills
</a>

</div>

<?php else: ?>


<!-- SCORE CARDS -->

<div class="cards">

<div class="card">

<h2>Overall Skill Score</h2>

<div class="score">
    <?php echo $overall_score; ?>%
</div>

</div>


<div class="card">

<h2>Total Skills</h2>

<div class="score">
    <?php echo $total_skills; ?>
</div>

</div>


<div class="card">

<h2>Recommended Career</h2>

<p style="font-size:20px;font-weight:bold;">
    <?php echo htmlspecialchars($career); ?>
</p>

</div>

</div>


<!-- SKILL ANALYSIS -->

<div class="section">

<h2>📊 Your Skill Analysis</h2>

<?php foreach ($skills as $skill): ?>

<div class="skill">

<div class="skill-name">

<span>
    <?php echo htmlspecialchars($skill["skill_name"]); ?>
</span>

<span>
    <?php echo $skill["proficiency"]; ?>%
</span>

</div>

<div class="progress">

<div
class="progress-bar"
style="width: <?php echo $skill["proficiency"]; ?>%;">
</div>

</div>

</div>

<?php endforeach; ?>

</div>


<!-- STRONG SKILLS -->

<div class="section">

<h2>💪 Strong Skills</h2>

<?php if (count($strong_skills) > 0): ?>

<?php foreach ($strong_skills as $skill): ?>

<span class="badge">
    <?php echo htmlspecialchars($skill); ?>
</span>

<?php endforeach; ?>

<?php else: ?>

<p>No strong skills yet.</p>

<?php endif; ?>

</div>


<!-- MEDIUM SKILLS -->

<div class="section">

<h2>📚 Skills to Improve</h2>

<?php if (count($medium_skills) > 0): ?>

<?php foreach ($medium_skills as $skill): ?>

<span class="badge">
    <?php echo htmlspecialchars($skill); ?>
</span>

<?php endforeach; ?>

<?php else: ?>

<p>No medium-level skills.</p>

<?php endif; ?>

</div>


<!-- WEAK SKILLS -->

<div class="section">

<h2>⚠️ Skill Gaps</h2>

<?php if (count($weak_skills) > 0): ?>

<?php foreach ($weak_skills as $skill): ?>

<span class="badge">
    <?php echo htmlspecialchars($skill); ?>
</span>

<?php endforeach; ?>

<?php else: ?>

<p>🎉 No major skill gaps detected!</p>

<?php endif; ?>

</div>


<!-- RECOMMENDATIONS -->

<div class="section">

<h2>🚀 Recommended Learning</h2>

<?php if (count($recommendations) > 0): ?>

<?php foreach ($recommendations as $recommendation): ?>

<div class="recommendation">

📌 <?php echo htmlspecialchars($recommendation); ?>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="recommendation">
    🎉 Your current skill set is looking good!
</div>

<?php endif; ?>

</div>


<?php endif; ?>

</div>

</body>

</html>