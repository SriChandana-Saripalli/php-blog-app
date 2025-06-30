<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);
$comment = trim($_POST['comment']);

if (!empty($comment)) {
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
}

// Redirect back to the post list or previous page
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'view_posts.php';
header("Location: $redirect_url");
exit();
