<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$skills_found = [];
$message = "";

/* Get student profile */
$stmt = $conn->prepare("
    SELECT about, resume
    FROM student_profiles
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$stmt->close();


/* Skill list */
$skill_list = [
    "HTML",
    "CSS",
    "JavaScript",
    "PHP",
    "MySQL",
    "Python",
    "Java",
    "C++",
    "React",
    "Node.js",
    "Git",
    "REST API",
    "Bootstrap",
    "Laravel"
];


/* Analyze About section */
if (!empty($profile["about"])) {

    $about_text = strtolower($profile["about"]);

    foreach ($skill_list as $skill) {

        if (strpos($about_text, strtolower($skill)) !== false) {
            $skills_found[] = $skill;
        }
    }

    if (count($skills_found) > 0) {
        $message = "Skills successfully detected!";
    } else {
        $message = "No skills detected. Add your skills in About Yourself.";
    }

} else {

    $message = "Please complete your About Yourself section first.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resume Analysis - CareerConnect AI</title>

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
    max-width: 850px;
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
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.10);
    margin-bottom: 20px;
}

h1 {
    color: #1e3a8a;
    margin-bottom: 10px;
}

.subtitle {
    color: #666;
    margin-bottom: 25px;
}

.message {
    padding: 14px;
    background: #eff6ff;
    color: #1e40af;
    border-radius: 10px;
    margin-bottom: 25px;
}

.resume {
    padding: 15px;
    background: #f8fafc;
    border-radius: 10px;
    margin-bottom: 25px;
}

.resume a {
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}

.skills {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.skill {
    background: #dbeafe;
    color: #1e3a8a;
    padding: 10px 16px;
    border-radius: 20px;
    font-weight: bold;
}

.no-skill {
    color: #666;
}

.button {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 20px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
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


    <div class="card">

        <h1>🤖 AI Resume Skill Analysis</h1>

        <p class="subtitle">
            CareerConnect AI analyzes your profile and identifies your technical skills.
        </p>


        <?php if (!empty($profile["resume"])): ?>

            <div class="resume">

                📄 <strong>Resume Uploaded</strong>

                <br><br>

                <a
                    href="uploads/<?php echo htmlspecialchars($profile["resume"]); ?>"
                    target="_blank"
                >
                    👁️ View Resume
                </a>

            </div>

        <?php else: ?>

            <div class="resume">
                ⚠️ No resume uploaded yet.
            </div>

        <?php endif; ?>


        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>


        <h2>🔍 Detected Skills</h2>

        <br>


        <?php if (count($skills_found) > 0): ?>

            <div class="skills">

                <?php foreach ($skills_found as $skill): ?>

                    <div class="skill">
                        ✅ <?php echo htmlspecialchars($skill); ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p class="no-skill">
                No skills detected yet.
            </p>

        <?php endif; ?>


        <a class="button" href="skills.php">
            ⚙️ Manage My Skills
        </a>

    </div>

</div>

</body>

</html>
