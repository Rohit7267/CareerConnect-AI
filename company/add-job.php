<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../login.php");
    exit;
}

require_once "../config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);
    $job_type = $_POST["job_type"];
    $salary = trim($_POST["salary"]);
    $required_skills = trim($_POST["required_skills"]);

    if (
        empty($title) ||
        empty($description) ||
        empty($location) ||
        empty($required_skills)
    ) {

        $message = "Please fill all required fields.";

    } else {

        $company_id = $_SESSION["user_id"];

        $sql = "INSERT INTO jobs
                (company_id, title, description, location, job_type, salary, required_skills)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "issssss",
            $company_id,
            $title,
            $description,
            $location,
            $job_type,
            $salary,
            $required_skills
        );

        if ($stmt->execute()) {

            $message = "Job / Internship posted successfully! 🎉";

        } else {

            $message = "Something went wrong: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Post Internship</title>

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
    max-width: 800px;
    margin: auto;
}

.card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

h1 {
    margin-top: 0;
    color: #333;
}

.subtitle {
    color: #666;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: bold;
    color: #333;
}

input,
textarea,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #667eea;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    opacity: 0.9;
}

.message {
    padding: 12px;
    background: #f0f0f0;
    border-radius: 8px;
    margin-bottom: 20px;
    color: #333;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    color: white;
    text-decoration: none;
}

.example {
    background: #f7f7f7;
    padding: 12px;
    border-radius: 8px;
    color: #666;
    font-size: 14px;
}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">
    ← Back to Dashboard
</a>

<div class="card">

<h1>💼 Post Internship / Job</h1>

<p class="subtitle">
    Find talented students for your organization.
</p>

<?php if (!empty($message)): ?>

<div class="message">
    <?php echo htmlspecialchars($message); ?>
</div>

<?php endif; ?>


<form method="POST">

<div class="form-group">

<label>Job / Internship Title *</label>

<input
    type="text"
    name="title"
    placeholder="Example: PHP Developer Intern"
    required
>

</div>


<div class="form-group">

<label>Description *</label>

<textarea
    name="description"
    placeholder="Describe the internship or job..."
    required
></textarea>

</div>


<div class="form-group">

<label>Location *</label>

<input
    type="text"
    name="location"
    placeholder="Example: Delhi / Remote"
    required
>

</div>


<div class="form-group">

<label>Type</label>

<select name="job_type">

<option value="Internship">
    Internship
</option>

<option value="Full Time">
    Full Time
</option>

<option value="Part Time">
    Part Time
</option>

</select>

</div>


<div class="form-group">

<label>Salary / Stipend</label>

<input
    type="text"
    name="salary"
    placeholder="Example: ₹15,000/month"
>

</div>


<div class="form-group">

<label>Required Skills *</label>

<input
    type="text"
    name="required_skills"
    placeholder="PHP, MySQL, HTML, CSS, JavaScript"
    required
>

<div class="example">
    💡 Separate skills using commas.
    <br><br>
    Example:
    PHP, MySQL, HTML, CSS, JavaScript
</div>

</div>


<button type="submit">
    🚀 Post Opportunity
</button>

</form>

</div>

</div>

</body>

</html>