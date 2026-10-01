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

/* Fetch existing profile */
$stmt = $conn->prepare("
    SELECT phone, college, course, branch, graduation_year, cgpa, about, resume
    FROM student_profiles
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$stmt->close();


/* Save profile */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST["phone"] ?? "");
    $college = trim($_POST["college"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $branch = trim($_POST["branch"] ?? "");
    $graduation_year = (int)($_POST["graduation_year"] ?? 0);
    $cgpa = (float)($_POST["cgpa"] ?? 0);
    $about = trim($_POST["about"] ?? "");

    $resume_name = $profile["resume"] ?? "";


    /* Resume Upload */
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES["resume"]["error"] === UPLOAD_ERR_OK) {

            $file_name = $_FILES["resume"]["name"];
            $file_tmp = $_FILES["resume"]["tmp_name"];
            $file_size = $_FILES["resume"]["size"];

            $file_extension = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            /* Only PDF allowed */
            if ($file_extension !== "pdf") {

                $message = "Only PDF resume is allowed.";
                $message_type = "error";

            } elseif ($file_size > 5 * 1024 * 1024) {

                $message = "Resume size must be less than 5 MB.";
                $message_type = "error";

            } else {

                $new_file_name = "resume_" . $user_id . "_" . time() . ".pdf";

                $upload_folder = __DIR__ . "/uploads/";

                if (!is_dir($upload_folder)) {
                    mkdir($upload_folder, 0777, true);
                }

                $upload_path = $upload_folder . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {

                    $resume_name = $new_file_name;

                } else {

                    $message = "Resume upload failed.";
                    $message_type = "error";
                }
            }

        } else {

            $message = "Error while uploading resume.";
            $message_type = "error";
        }
    }


    /* Validation */
    if ($college === "" || $course === "" || $branch === "") {

        $message = "Please fill College, Course and Branch.";
        $message_type = "error";

    } elseif ($cgpa < 0 || $cgpa > 10) {

        $message = "CGPA must be between 0 and 10.";
        $message_type = "error";

    } elseif ($message_type !== "error") {


        /* Check existing profile */
        $check = $conn->prepare("
            SELECT id
            FROM student_profiles
            WHERE user_id = ?
            LIMIT 1
        ");

        $check->bind_param("i", $user_id);
        $check->execute();
        $check->store_result();


        if ($check->num_rows > 0) {

            /* UPDATE */

            $stmt = $conn->prepare("
                UPDATE student_profiles
                SET phone = ?,
                    college = ?,
                    course = ?,
                    branch = ?,
                    graduation_year = ?,
                    cgpa = ?,
                    about = ?,
                    resume = ?
                WHERE user_id = ?
            ");

            $stmt->bind_param(
                "ssssidssi",
                $phone,
                $college,
                $course,
                $branch,
                $graduation_year,
                $cgpa,
                $about,
                $resume_name,
                $user_id
            );

        } else {

            /* INSERT */

            $stmt = $conn->prepare("
                INSERT INTO student_profiles
                (
                    user_id,
                    phone,
                    college,
                    course,
                    branch,
                    graduation_year,
                    cgpa,
                    about,
                    resume
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "issssidss",
                $user_id,
                $phone,
                $college,
                $course,
                $branch,
                $graduation_year,
                $cgpa,
                $about,
                $resume_name
            );
        }


        if ($stmt->execute()) {

            $message = "Profile saved successfully!";
            $message_type = "success";

        } else {

            $message = "Unable to save profile: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();
        $check->close();


        /* Reload profile */

        $stmt = $conn->prepare("
            SELECT phone, college, course, branch, graduation_year, cgpa, about, resume
            FROM student_profiles
            WHERE user_id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Profile - CareerConnect AI</title>

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
}

h1 {
    color: #1e3a8a;
    margin-bottom: 8px;
}

.subtitle {
    color: #666;
    margin-bottom: 25px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: bold;
}

input,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #2563eb;
}

button {
    margin-top: 22px;
    padding: 13px 25px;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #1d4ed8;
}

.message {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.success {
    background: #dcfce7;
    color: #166534;
}

.error {
    background: #fee2e2;
    color: #991b1b;
}

.resume-box {
    margin-top: 10px;
    padding: 12px;
    background: #f1f5f9;
    border-radius: 8px;
}

.resume-box a {
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}

@media (max-width: 650px) {

    body {
        padding: 15px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .full {
        grid-column: auto;
    }

    .card {
        padding: 22px;
    }
}

</style>

</head>


<body>

<div class="container">

    <div class="topbar">

        <div class="logo">
            CareerConnect AI
        </div>

        <a class="back" href="dashboard.php">
            ← Dashboard
        </a>

    </div>


    <div class="card">

        <h1>My Profile 👤</h1>

        <p class="subtitle">
            Complete your profile to get better internship and placement matches.
        </p>


        <?php if ($message !== ""): ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">


                <div>

                    <label>Phone Number</label>

                    <input
                        type="text"
                        name="phone"
                        value="<?php echo htmlspecialchars($profile["phone"] ?? ""); ?>"
                        placeholder="Enter phone number"
                    >

                </div>


                <div>

                    <label>College</label>

                    <input
                        type="text"
                        name="college"
                        value="<?php echo htmlspecialchars($profile["college"] ?? ""); ?>"
                        placeholder="Enter college name"
                        required
                    >

                </div>


                <div>

                    <label>Course</label>

                    <input
                        type="text"
                        name="course"
                        value="<?php echo htmlspecialchars($profile["course"] ?? ""); ?>"
                        placeholder="e.g. B.Tech"
                        required
                    >

                </div>


                <div>

                    <label>Branch</label>

                    <input
                        type="text"
                        name="branch"
                        value="<?php echo htmlspecialchars($profile["branch"] ?? ""); ?>"
                        placeholder="e.g. Computer Science"
                        required
                    >

                </div>


                <div>

                    <label>Graduation Year</label>

                    <input
                        type="number"
                        name="graduation_year"
                        value="<?php echo htmlspecialchars($profile["graduation_year"] ?? ""); ?>"
                        placeholder="e.g. 2027"
                        min="2020"
                        max="2035"
                    >

                </div>


                <div>

                    <label>CGPA</label>

                    <input
                        type="number"
                        name="cgpa"
                        value="<?php echo htmlspecialchars($profile["cgpa"] ?? ""); ?>"
                        placeholder="e.g. 8.2"
                        step="0.01"
                        min="0"
                        max="10"
                    >

                </div>


                <div class="full">

                    <label>About Yourself</label>

                    <textarea
                        name="about"
                        placeholder="Tell us about your skills, interests and career goals..."
                    ><?php echo htmlspecialchars($profile["about"] ?? ""); ?></textarea>

                </div>


                <!-- RESUME -->

                <div class="full">

                    <label>📄 Upload Resume (PDF)</label>

                    <input
                        type="file"
                        name="resume"
                        accept=".pdf,application/pdf"
                    >

                    <small>
                        Maximum size: 5 MB | PDF only
                    </small>


                    <?php if (!empty($profile["resume"])): ?>

                        <div class="resume-box">

                            ✅ Resume uploaded:

                            <a
                                href="uploads/<?php echo htmlspecialchars($profile["resume"]); ?>"
                                target="_blank"
                            >
                                View Resume
                            </a>

                        </div>

                    <?php endif; ?>

                </div>


            </div>


            <button type="submit">
                💾 Save Profile
            </button>

        </form>

    </div>

</div>

</body>

</html>