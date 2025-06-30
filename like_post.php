<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if user already liked this post
$stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If liked, remove the like
    $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
} else {
    // If not liked, add a new like
    $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
}

// Redirect back
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'view_posts.php';
header("Location: $redirect_url");
exit();
