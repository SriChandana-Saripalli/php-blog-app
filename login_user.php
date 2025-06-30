<?php
session_start();
include 'db.php';

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['identifier']); // username or email
    $password = $_POST['password'];

    // Admin credentials (not in DB)
    if ($identifier === 'Admin' && $password === 'admin') {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = 'Admin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // Check users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['city'] = $user['city'];
            $_SESSION['gender'] = $user['gender'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: view_posts.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Blog App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #e60633;
        }
        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #e60633;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        p {
            font-size: 14px;
        }
        a {
            color: #e60633;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="identifier" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>New user? <a href="register_user.php">Register</a></p>
</div>
</body>
</html>
