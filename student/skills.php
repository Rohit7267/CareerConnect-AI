<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

/* --------------------------------
   ADD / UPDATE SKILL
--------------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $skill_id = (int)($_POST["skill_id"] ?? 0);
    $proficiency = (int)($_POST["proficiency"] ?? 0);

    if ($skill_id <= 0) {

        $message = "Please select a skill.";
        $message_type = "error";

    } elseif ($proficiency < 1 || $proficiency > 100) {

        $message = "Proficiency must be between 1 and 100.";
        $message_type = "error";

    } else {

        /*
         * Check whether this skill already exists
         * for the logged-in student.
         */

        $check = $conn->prepare("
            SELECT id
            FROM student_skills
            WHERE user_id = ? AND skill_id = ?
            LIMIT 1
        ");

        $check->bind_param("ii", $user_id, $skill_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            /* Update existing skill */

            $stmt = $conn->prepare("
                UPDATE student_skills
                SET proficiency = ?
                WHERE user_id = ? AND skill_id = ?
            ");

            $stmt->bind_param(
                "iii",
                $proficiency,
                $user_id,
                $skill_id
            );

            if ($stmt->execute()) {
                $message = "Skill updated successfully!";
                $message_type = "success";
            } else {
                $message = "Unable to update skill.";
                $message_type = "error";
            }

            $stmt->close();

        } else {

            /* Add new skill */

            $stmt = $conn->prepare("
                INSERT INTO student_skills
                (user_id, skill_id, proficiency)
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param(
                "iii",
                $user_id,
                $skill_id,
                $proficiency
            );

            if ($stmt->execute()) {
                $message = "Skill added successfully!";
                $message_type = "success";
            } else {
                $message = "Unable to add skill.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}


/* --------------------------------
   DELETE SKILL
--------------------------------- */

if (isset($_GET["delete"])) {

    $delete_id = (int)$_GET["delete"];

    if ($delete_id > 0) {

        $stmt = $conn->prepare("
            DELETE FROM student_skills
            WHERE id = ? AND user_id = ?
        ");

        $stmt->bind_param(
            "ii",
            $delete_id,
            $user_id
        );

        $stmt->execute();
        $stmt->close();

        header("Location: skills.php");
        exit;
    }
}


/* --------------------------------
   GET ALL AVAILABLE SKILLS
--------------------------------- */

$skills_result = $conn->query("
    SELECT id, skill_name
    FROM skills
    ORDER BY skill_name ASC
");


/* --------------------------------
   GET STUDENT'S SKILLS
--------------------------------- */

$stmt = $conn->prepare("
    SELECT
        student_skills.id,
        skills.skill_name,
        student_skills.proficiency
    FROM student_skills

    INNER JOIN skills
        ON student_skills.skill_id = skills.id

    WHERE student_skills.user_id = ?

    ORDER BY student_skills.proficiency DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$student_skills_result = $stmt->get_result();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Skills - CareerConnect AI</title>

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
        linear-gradient(
            135deg,
            #eef2ff,
            #dbeafe
        );

    padding: 30px;
}


/* Main Container */

.container {

    max-width: 1000px;

    margin: auto;
}


/* Top Navigation */

.topbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;
}

.logo {

    font-size: 26px;

    font-weight: bold;

    color: #1e3a8a;
}

.back {

    text-decoration: none;

    color: #2563eb;

    font-weight: bold;
}


/* Cards */

.card {

    background: white;

    padding: 30px;

    border-radius: 18px;

    box-shadow:
        0 15px 40px
        rgba(0,0,0,0.10);

    margin-bottom: 25px;
}


/* Headings */

h1 {

    color: #1e3a8a;

    margin-bottom: 8px;
}

h2 {

    color: #1e3a8a;

    margin-bottom: 20px;
}

.subtitle {

    color: #666;

    margin-bottom: 25px;
}


/* Form */

.form-grid {

    display: grid;

    grid-template-columns:
        1fr 180px 150px;

    gap: 15px;

    align-items: end;
}

label {

    display: block;

    margin-bottom: 7px;

    font-weight: bold;

    color: #333;
}

select {

    width: 100%;

    padding: 12px;

    border: 1px solid #ccc;

    border-radius: 8px;

    background: white;

    font-size: 15px;
}

select:focus {

    outline: none;

    border-color: #2563eb;
}


/* Button */

button {

    padding: 12px;

    border: none;

    border-radius: 8px;

    background: #2563eb;

    color: white;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;
}

button:hover {

    background: #1d4ed8;
}


/* Messages */

.message {

    padding: 13px;

    border-radius: 8px;

    margin-bottom: 20px;

    text-align: center;
}

.success {

    background: #dcfce7;

    color: #166534;
}

.error {

    background: #fee2e2;

    color: #991b1b;
}


/* Skills */

.skill-list {

    display: flex;

    flex-direction: column;

    gap: 18px;
}

.skill-item {

    padding: 18px;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    background: #f8fafc;
}

.skill-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 10px;
}

.skill-name {

    font-size: 17px;

    font-weight: bold;

    color: #1f2937;
}

.skill-percent {

    font-weight: bold;

    color: #2563eb;
}


/* Progress Bar */

.progress {

    width: 100%;

    height: 10px;

    background: #e5e7eb;

    border-radius: 20px;

    overflow: hidden;
}

.progress-bar {

    height: 100%;

    background: #2563eb;

    border-radius: 20px;
}


/* Delete */

.delete {

    display: inline-block;

    margin-top: 10px;

    color: #dc2626;

    text-decoration: none;

    font-size: 14px;

    font-weight: bold;
}


/* Empty */

.empty {

    text-align: center;

    padding: 30px;

    color: #666;
}


/* Responsive */

@media (max-width: 700px) {

    body {
        padding: 15px;
    }

    .form-grid {

        grid-template-columns: 1fr;

    }

    .card {

        padding: 20px;

    }

    .topbar {

        align-items: flex-start;

        gap: 15px;

        flex-direction: column;

    }
}

</style>

</head>

<body>

<div class="container">


    <!-- TOP BAR -->

    <div class="topbar">

        <div class="logo">
            CareerConnect AI
        </div>

        <a
            class="back"
            href="dashboard.php"
        >
            ← Dashboard
        </a>

    </div>


    <!-- HEADER -->

    <div class="card">

        <h1>
            My Skills 🧠
        </h1>

        <p class="subtitle">
            Add your technical skills and set your proficiency level.
            This information will be used for AI-powered career matching.
        </p>

    </div>


    <!-- ADD SKILL -->

    <div class="card">

        <h2>
            Add / Update Skill
        </h2>

        <?php if ($message !== ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-grid">


                <!-- Skill -->

                <div>

                    <label>
                        Select Skill
                    </label>

                    <select
                        name="skill_id"
                        required
                    >

                        <option value="">
                            -- Select Skill --
                        </option>

                        <?php if ($skills_result): ?>

                            <?php while ($skill = $skills_result->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $skill["id"]; ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $skill["skill_name"]
                                    );
                                    ?>
                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>


                <!-- Proficiency -->

                <div>

                    <label>
                        Proficiency
                    </label>

                    <select
                        name="proficiency"
                        required
                    >

                        <option value="20">
                            Beginner - 20%
                        </option>

                        <option value="40">
                            Basic - 40%
                        </option>

                        <option value="60">
                            Intermediate - 60%
                        </option>

                        <option value="80">
                            Advanced - 80%
                        </option>

                        <option value="100">
                            Expert - 100%
                        </option>

                    </select>

                </div>


                <!-- Submit -->

                <div>

                    <button type="submit">
                        + Add Skill
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- MY SKILLS -->

    <div class="card">

        <h2>
            My Skills
        </h2>


        <?php if ($student_skills_result->num_rows > 0): ?>

            <div class="skill-list">

                <?php while ($student_skill = $student_skills_result->fetch_assoc()): ?>

                    <div class="skill-item">


                        <div class="skill-header">

                            <span class="skill-name">

                                <?php
                                echo htmlspecialchars(
                                    $student_skill["skill_name"]
                                );
                                ?>

                            </span>

                            <span class="skill-percent">

                                <?php
                                echo (int)
                                    $student_skill["proficiency"];
                                ?>%

                            </span>

                        </div>


                        <div class="progress">

                            <div
                                class="progress-bar"
                                style="width:
                                <?php
                                echo (int)
                                    $student_skill["proficiency"];
                                ?>%"
                            ></div>

                        </div>


                        <a
                            class="delete"
                            href="skills.php?delete=<?php
                                echo (int)$student_skill["id"];
                            ?>"
                            onclick="return confirm(
                                'Are you sure you want to remove this skill?'
                            );"
                        >
                            Remove Skill
                        </a>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="empty">

                <p>
                    You haven't added any skills yet.
                </p>

                <p>
                    Add your first skill above 🚀
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>