<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title == "" || $content == "") {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $success = "Post created successfully.";
        } else {
            $error = "Error creating post.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('flower.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .navbar {
            background-color: #e3063b;
            color: white;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .title {
            font-weight: 600;
            font-size: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        .navbar a:hover {
            background-color: #b00333;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.6);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-top: 0;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background: rgb(230, 6, 51);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background: #b00333;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Admin Navbar -->
<div class="navbar">
    <div class="title">📊 My Blog Admin Panel</div>
    <div>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="view_users.php">👥 Users List</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="overlay">
    <div class="container">
        <h2>Create Post</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Post Title" required />
            <textarea name="content" rows="5" placeholder="Post Content" required></textarea>
            <button type="submit">Create</button>
        </form>
    </div>
</div>
</body>
</html>
