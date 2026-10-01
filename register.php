<?php
session_start();
require_once "config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    // Basic validation
    if ($name === "" || $email === "" || $password === "" || $role === "") {
        $message = "Please fill all fields.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = "error";
    } elseif (!in_array($role, ["student", "company", "college"], true)) {
        $message = "Invalid role selected.";
        $message_type = "error";
    } else {

        // Check existing email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");

        if (!$check) {
            die("Prepare failed: " . $conn->error);
        }

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "Email already registered.";
            $message_type = "error";

        } else {

            // Secure password hashing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {
                $message = "Registration successful! You can now login.";
                $message_type = "success";
            } else {
                $message = "Registration failed: " . $stmt->error;
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - CareerConnect AI</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #eef2ff, #dbeafe);
        }

        .register-box {
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
            color: #333;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus,
        select:focus {
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

        .message {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 8px;
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

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="register-box">

    <h1>CareerConnect AI</h1>

    <p class="subtitle">
        Create your account
    </p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>
        <input
            type="text"
            name="name"
            placeholder="Enter your name"
            required
        >

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
            placeholder="Minimum 6 characters"
            required
        >

        <label>Register As</label>

        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="student">Student</option>
            <option value="company">Company</option>
            <option value="college">College</option>
        </select>

        <button type="submit">
            Create Account
        </button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>