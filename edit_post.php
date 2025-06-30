<?php
session_start();
include 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID.");
}

$post_id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title && $content) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $post_id);
        $stmt->execute();

        // Flash message
        $_SESSION['message'] = "✅ Post updated successfully!";
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Title and content cannot be empty.";
    }
}

// Fetch current post
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('flower.jpg') no-repeat center center fixed;
            background-size: cover;
            padding: 50px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px 14px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            background-color: rgb(230, 6, 51);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #b00333;
        }
        .error {
            color: red;
            margin: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Post</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        <button type="submit">Update Post</button>
    </form>
</div>
</body>
</html>