<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
        }
        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 30px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            color: #1abc9c;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 16px;
        }
        .sidebar a:hover {
            background: #1abc9c;
            color: #fff;
        }
        /* Main Content */
        .main {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
        }
        header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        header h1 {
            margin: 0;
            font-size: 26px;
            color: #2c3e50;
        }
        header p {
            color: #555;
            margin-top: 5px;
        }
        /* Dashboard Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #1abc9c;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .card a:hover {
            background: #16a085;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>📚 Student Portal</h2>
        <a href="../logout.php">🚪 Logout</a>
        <a href="recommendations.php">🤖 AI Recommendations</a>
        <a href="internships.php">💼 Internships</a>
        <a href="ai-analysis.php">🧠 AI Analysis</a>
    </div>

    <div class="main">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> 👋</h1>
            <p>You are logged in as <strong>Student</strong>.</p>
        </header>

        <div class="cards">
            <div class="card">
                <h3>🤖 AI Recommendations</h3>
                <p>Get personalized study tips and career guidance powered by AI.</p>
                <a href="recommendations.php">Explore</a>
            </div>
            <div class="card">
                <h3>💼 Internships</h3>
                <p>Find internship opportunities tailored to your skills and interests.</p>
                <a href="internships.php">View</a>
            </div>
            <div class="card">
                <h3>🧠 AI Analysis</h3>
                <p>Analyze your progress and performance with smart insights.</p>
                <a href="ai-analysis.php">Check</a>
            </div>
        </div>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Student Portal | All Rights Reserved</p>
        </footer>
    </div>

</body>
</html>
