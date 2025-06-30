<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    .navbar-wrapper {
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 10px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .navbar {
        max-width: 1100px;
        margin: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        font-family: 'Poppins', sans-serif;
    }
    .navbar .brand {
        font-weight: 600;
        font-size: 20px;
        color: rgb(230, 6, 51);
        text-decoration: none;
    }
    .navbar-links a {
        margin-left: 25px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 6px;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .navbar-links a:hover {
        background-color: rgb(230, 6, 51);
        color: white;
    }
</style>

<div class="navbar-wrapper">
    <div class="navbar">
        <a class="brand" href="view_posts.php">MyBlog</a>
        <div class="navbar-links">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="create_post.php">New Post</a>
                <a href="view_posts.php">Manage Posts</a>
                <a href="admin_users.php">View Users</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>