<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];


/* Get student's skills */

$student_skills = [];

$stmt = $conn->prepare("
    SELECT skills.skill_name, student_skills.proficiency
    FROM student_skills
    INNER JOIN skills
        ON student_skills.skill_id = skills.id
    WHERE student_skills.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $student_skills[strtolower(trim($row["skill_name"]))] =
        (int)$row["proficiency"];
}

$stmt->close();


/* Get all jobs */

$result = $conn->query("
    SELECT
        jobs.id,
        jobs.title,
        jobs.description,
        jobs.location,
        jobs.job_type,
        jobs.salary,
        jobs.required_skills,
        users.name AS company_name
    FROM jobs
    INNER JOIN users
        ON jobs.company_id = users.id
    ORDER BY jobs.created_at DESC
");


$recommendations = [];


/* Calculate recommendation score */

while ($job = $result->fetch_assoc()) {

    $required_skills = explode(",", $job["required_skills"]);

    $total_skills = 0;
    $total_score = 0;
    $matched = [];
    $missing = [];

    foreach ($required_skills as $skill) {

        $skill = trim($skill);

        if ($skill === "") {
            continue;
        }

        $total_skills++;

        $skill_key = strtolower($skill);

        if (isset($student_skills[$skill_key])) {

            $matched[] = $skill;

            $total_score += $student_skills[$skill_key];

        } else {

            $missing[] = $skill;
        }
    }


    if ($total_skills > 0) {

        $match_percentage = round(
            ($total_score / ($total_skills * 100)) * 100
        );

    } else {

        $match_percentage = 0;
    }


    $job["match_percentage"] = $match_percentage;
    $job["matched"] = $matched;
    $job["missing"] = $missing;

    $recommendations[] = $job;
}


/* Highest match first */

usort($recommendations, function ($a, $b) {

    return $b["match_percentage"] - $a["match_percentage"];

});

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Job Recommendations - CareerConnect AI</title>


<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {

    font-family: Arial, sans-serif;

    min-height: 100vh;

    background:
        linear-gradient(135deg, #eef2ff, #dbeafe);

    padding: 30px;
}

.container {

    max-width: 1000px;

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

.header {

    background: white;

    padding: 30px;

    border-radius: 18px;

    box-shadow:
        0 15px 40px rgba(0,0,0,0.10);

    margin-bottom: 25px;
}

.header h1 {

    color: #1e3a8a;

    margin-bottom: 10px;
}

.header p {

    color: #666;
}

.job {

    background: white;

    padding: 25px;

    border-radius: 16px;

    box-shadow:
        0 10px 30px rgba(0,0,0,0.08);

    margin-bottom: 20px;
}

.job-top {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;
}

.job h2 {

    color: #1e3a8a;

    margin-bottom: 8px;
}

.company {

    color: #64748b;

    margin-bottom: 10px;
}

.match {

    min-width: 110px;

    text-align: center;

    background: #dbeafe;

    color: #1e40af;

    padding: 12px;

    border-radius: 12px;

    font-size: 20px;

    font-weight: bold;
}

.details {

    color: #555;

    margin: 10px 0;
}

.skills {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;

    margin-top: 12px;
}

.skill {

    padding: 7px 12px;

    border-radius: 20px;

    font-size: 14px;

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

.buttons {

    margin-top: 20px;
}

.button {

    display: inline-block;

    padding: 11px 18px;

    background: #2563eb;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: bold;
}

.empty {

    background: white;

    padding: 40px;

    text-align: center;

    border-radius: 16px;

    color: #666;
}


@media (max-width: 650px) {

    body {
        padding: 15px;
    }

    .job-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .match {
        width: 100%;
    }

}

</style>

</head>


<body>

<div class="container">


    <div class="topbar">

        <div class="logo">
            CareerConnect AI 🤖
        </div>

        <a class="back" href="dashboard.php">
            ← Dashboard
        </a>

    </div>


    <div class="header">

        <h1>🤖 Recommended Jobs For You</h1>

        <p>
            CareerConnect AI ranks opportunities according to your skills.
        </p>

    </div>


    <?php if (count($recommendations) > 0): ?>


        <?php foreach ($recommendations as $job): ?>

            <div class="job">

                <div class="job-top">

                    <div>

                        <h2>
                            💼
                            <?php echo htmlspecialchars($job["title"]); ?>
                        </h2>

                        <div class="company">

                            🏢
                            <?php echo htmlspecialchars($job["company_name"]); ?>

                        </div>

                    </div>


                    <div class="match">

                        <?php echo $job["match_percentage"]; ?>%

                        <div style="font-size:12px;">
                            Match
                        </div>

                    </div>

                </div>


                <div class="details">

                    📍
                    <?php echo htmlspecialchars($job["location"]); ?>

                    &nbsp;&nbsp;

                    💰
                    <?php echo htmlspecialchars($job["salary"]); ?>

                    &nbsp;&nbsp;

                    📋
                    <?php echo htmlspecialchars($job["job_type"]); ?>

                </div>


                <p class="details">

                    <?php echo htmlspecialchars($job["description"]); ?>

                </p>


                <?php if (count($job["matched"]) > 0): ?>

                    <strong>✅ Your Matching Skills</strong>

                    <div class="skills">

                        <?php foreach ($job["matched"] as $skill): ?>

                            <span class="skill matched">

                                ✅ <?php echo htmlspecialchars($skill); ?>

                            </span>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>


                <?php if (count($job["missing"]) > 0): ?>

                    <br>

                    <strong>❌ Skills To Improve</strong>

                    <div class="skills">

                        <?php foreach ($job["missing"] as $skill): ?>

                            <span class="skill missing">

                                ❌ <?php echo htmlspecialchars($skill); ?>

                            </span>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>


                <div class="buttons">

                    <a
                        class="button"
                        href="skill-gap.php?job_id=<?php echo $job["id"]; ?>"
                    >
                        🎯 View Skill Gap
                    </a>

                </div>

            </div>

        <?php endforeach; ?>


    <?php else: ?>

        <div class="empty">

            <h2>No jobs available yet.</h2>

            <p>
                Companies need to post internships or jobs first.
            </p>

        </div>

    <?php endif; ?>


</div>

</body>

</html>