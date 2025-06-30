<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Count total matching posts
$total_query = $conn->prepare("SELECT COUNT(*) as total FROM posts WHERE title LIKE ?");
$search_term = "%$search%";
$total_query->bind_param("s", $search_term);
$total_query->execute();
$total_result = $total_query->get_result()->fetch_assoc();
$total_posts = $total_result['total'];
$total_pages = ceil($total_posts / $limit);

// Fetch paginated posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("sii", $search_term, $limit, $offset);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: url('flower.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 100vh;
            padding-bottom: 50px;
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

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .search-form {
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #e3063b;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .post {
            background: #ffffff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .post-header h3 {
            margin: 0;
            font-size: 20px;
            flex: 1;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions .btn {
            padding: 6px 12px;
            background-color: #e3063b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .actions .btn:hover {
            background-color: #b00333;
        }

        .likes {
            margin-top: 12px;
            font-weight: bold;
            color: #d6336c;
        }

        .comments {
            margin-top: 12px;
            padding-left: 0;
            font-size: 14px;
            color: rgb(92, 57, 14);
            list-style: none;
        }

        .comment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
        }

        .delete-icon {
            color: red;
            font-size: 16px;
            text-decoration: none;
            margin-left: 10px;
        }

        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination a {
            margin: 0 8px;
            text-decoration: none;
            color: #e3063b;
            font-weight: bold;
        }

        .pagination a.active {
            background-color: #e3063b;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="navbar">
        <div class="title">📊 My Blog Admin Panel</div>
        <div>
            <a href="create_post.php">➕ Create Post</a>
            <a href="view_users.php">👥 Users List</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>📜 Blog Stories</h2>

        <form class="search-form" method="GET">
            <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post">
                <div class="post-header">
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <div class="actions">
                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn">✏️ Edit</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn" onclick="return confirm('Delete this post?');">🗑️ Delete</a>
                    </div>
                </div>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                <div class="likes">
                    ❤️ Likes:
                    <?php
                    $post_id = $post['id'];
                    $like_q = $conn->query("SELECT COUNT(*) as total FROM likes WHERE post_id = $post_id");
                    echo $like_q->fetch_assoc()['total'];
                    ?>
                </div>

                <ul class="comments">
                    <strong>💬 Comments:</strong>
                    <?php
                    $comments = $conn->query("SELECT id, comment FROM comments WHERE post_id = $post_id ORDER BY created_at DESC");
                    if ($comments->num_rows > 0) {
                        while ($c = $comments->fetch_assoc()) {
                            echo "<li class='comment-item'>
                                    <span>" . htmlspecialchars($c['comment']) . "</span>
                                    <a href='delete_comment.php?id=" . $c['id'] . "' onclick='return confirm(\"Delete this comment?\");' class='delete-icon'>🗑️</a>
                                </li>";
                        }
                    } else {
                        echo "<li>No comments yet.</li>";
                    }
                    ?>
                </ul>
            </div>
        <?php endwhile; ?>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a class="<?= $i == $page ? 'active' : '' ?>" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>
</body>
</html>
