<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$candidates = [];

/* Get all applied students */
$sql = "
SELECT
    applications.id AS application_id,
    applications.status,
    users.id AS student_id,
    users.name,
    users.email,
    jobs.title,
    jobs.required_skills
FROM applications
JOIN users
    ON applications.student_id = users.id
JOIN jobs
    ON applications.job_id = jobs.id
ORDER BY applications.id DESC
";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $student_id = (int)$row["student_id"];

        /* Get student's skills */
        $skill_sql = "
        SELECT
            skills.skill_name,
            student_skills.proficiency
        FROM student_skills
        JOIN skills
            ON student_skills.skill_id = skills.id
        WHERE student_skills.user_id = ?
        ";

        $skill_stmt = $conn->prepare($skill_sql);

        if (!$skill_stmt) {
            continue;
        }

        $skill_stmt->bind_param("i", $student_id);
        $skill_stmt->execute();

        $skill_result = $skill_stmt->get_result();

        $student_skills = [];

        while ($skill = $skill_result->fetch_assoc()) {

            $skill_name = strtolower(trim($skill["skill_name"]));

            $student_skills[$skill_name] =
                (int)$skill["proficiency"];
        }

        $skill_stmt->close();


        /* Required job skills */
        $required_skills = explode(",", $row["required_skills"]);

        $total_required = 0;
        $total_score = 0;
        $matched_skills = 0;

        foreach ($required_skills as $required) {

            $required = strtolower(trim($required));

            if ($required == "") {
                continue;
            }

            $total_required++;

            if (isset($student_skills[$required])) {

                $total_score += $student_skills[$required];

                $matched_skills++;
            }
        }


        /* Calculate AI match */
        if ($total_required > 0) {

            $match_percentage =
                ($total_score / ($total_required * 100)) * 100;

        } else {

            $match_percentage = 0;
        }


        $row["match_percentage"] = round($match_percentage);

        $row["matched_skills"] = $matched_skills;

        $row["total_required"] = $total_required;

        $candidates[] = $row;
    }
}


/* Highest match first */
usort($candidates, function ($a, $b) {

    return $b["match_percentage"] - $a["match_percentage"];

});

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AI Candidate Ranking</title>

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial, sans-serif;

    background: linear-gradient(135deg, #eef2ff, #f8fafc);

    color: #1e293b;
}

.container {

    width: 90%;

    max-width: 1100px;

    margin: 40px auto;
}

.header {

    background: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 25px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.header h1 {

    margin: 0 0 8px;

    color: #4f46e5;
}

.header p {

    margin: 0;

    color: #64748b;
}

.candidate {

    background: white;

    padding: 22px;

    margin-bottom: 18px;

    border-radius: 18px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.07);

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;
}

.rank {

    font-size: 28px;

    font-weight: bold;

    width: 55px;
}

.info {

    flex: 1;
}

.info h2 {

    margin: 0 0 8px;

    font-size: 21px;
}

.info p {

    margin: 5px 0;

    color: #64748b;
}

.job {

    display: inline-block;

    background: #eef2ff;

    color: #4f46e5;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: bold;
}

.skills {

    margin-top: 10px;

    font-size: 14px;

    color: #475569;
}

.status {

    display: inline-block;

    margin-top: 8px;

    padding: 5px 10px;

    border-radius: 15px;

    background: #f1f5f9;

    font-size: 12px;
}

.match {

    width: 180px;

    text-align: center;
}

.percentage {

    font-size: 30px;

    font-weight: bold;

    color: #16a34a;
}

.progress {

    width: 100%;

    height: 10px;

    background: #e5e7eb;

    border-radius: 10px;

    overflow: hidden;

    margin-top: 8px;
}

.progress-bar {

    height: 100%;

    background: #22c55e;
}

.back {

    display: inline-block;

    margin-top: 15px;

    padding: 10px 18px;

    background: #4f46e5;

    color: white;

    text-decoration: none;

    border-radius: 10px;
}

.empty {

    background: white;

    padding: 40px;

    text-align: center;

    border-radius: 18px;
}

@media(max-width:700px) {

    .candidate {

        flex-direction: column;

        align-items: flex-start;
    }

    .match {

        width: 100%;

        text-align: left;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>🤖 AI Candidate Ranking</h1>

        <p>
            Candidates are ranked automatically according to
            their skills and job requirements.
        </p>

    </div>


    <?php if (count($candidates) > 0): ?>

        <?php $rank = 1; ?>

        <?php foreach ($candidates as $candidate): ?>

            <div class="candidate">

                <div class="rank">

                    <?php

                    if ($rank == 1) {

                        echo "🥇";

                    } elseif ($rank == 2) {

                        echo "🥈";

                    } elseif ($rank == 3) {

                        echo "🥉";

                    } else {

                        echo "#" . $rank;

                    }

                    ?>

                </div>


                <div class="info">

                    <h2>
                        <?php
                        echo htmlspecialchars($candidate["name"]);
                        ?>
                    </h2>

                    <p>
                        📧
                        <?php
                        echo htmlspecialchars($candidate["email"]);
                        ?>
                    </p>

                    <span class="job">

                        💼
                        <?php
                        echo htmlspecialchars($candidate["title"]);
                        ?>

                    </span>

                    <div class="skills">

                        🧠 Skills Matched:

                        <?php
                        echo $candidate["matched_skills"];
                        ?>

                        /

                        <?php
                        echo $candidate["total_required"];
                        ?>

                    </div>

                    <span class="status">

                        Status:

                        <?php
                        echo htmlspecialchars($candidate["status"]);
                        ?>

                    </span>

                </div>


                <div class="match">

                    <div class="percentage">

                        <?php
                        echo $candidate["match_percentage"];
                        ?>%

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar"
                            style="width:
                            <?php
                            echo $candidate["match_percentage"];
                            ?>%;">
                        </div>

                    </div>

                    <small>AI Match Score</small>

                </div>

            </div>

            <?php $rank++; ?>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty">

            <h2>📭 No Applicants Yet</h2>

            <p>
                No student applications found in the database.
            </p>

        </div>

    <?php endif; ?>


    <a href="dashboard.php" class="back">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>