<?php
session_start();
require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter email and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role 
             FROM users 
             WHERE email = ? 
             LIMIT 1"
        );

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                // Store user information in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                // Redirect according to role
                if ($user["role"] === "student") {
                    header("Location: student/dashboard.php");
                    exit;
                }

                if ($user["role"] === "company") {
                    header("Location: company/dashboard.php");
                    exit;
                }

                if ($user["role"] === "college") {
                    header("Location: college/dashboard.php");
                    exit;
                }

            } else {
                $message = "Invalid email or password.";
            }

        } else {
            $message = "Invalid email or password.";
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

    <title>Login - CareerConnect AI</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #eef2ff, #dbeafe);
        }

        .login-box {
            width: 420px;
            max-width: 92%;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        h1 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            padding: 13px;
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

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            text-align: center;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h1>CareerConnect AI</h1>

    <p class="subtitle">
        Login to your account
    </p>

    <?php if ($message !== ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Create Account
        </a>

    </div>

</div>

</body>
</html>