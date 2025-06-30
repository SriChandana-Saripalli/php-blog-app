<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<style>
.navbar-admin {
    background-color: #fff;
    padding: 12px 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: space-between;
}
.navbar-admin a {
    margin-left: 20px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}
.navbar-admin a:hover {
    color: rgb(230, 6, 51);
}
</style>

<div class="navbar-admin">
    <div><strong style="color:rgb(230,6,51)">Admin Panel</strong></div>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="create_post.php">Create New Post</a>
        <a href="logout.php">Logout</a>
    </div>
</div>
