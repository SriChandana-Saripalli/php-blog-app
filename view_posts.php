<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? "";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Count total matching posts
$sql_count = "SELECT COUNT(*) as total FROM posts WHERE title LIKE ? OR content LIKE ?";
$stmt = $conn->prepare($sql_count);
$like_search = "%$search%";
$stmt->bind_param("ss", $like_search, $like_search);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Fetch posts
$sql = "SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $like_search, $like_search);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('flower.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .overlay {
            background-color: rgba(247, 252, 254, 0.6);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
        max-width: 950px;
        margin: auto;
        padding: 30px;
        border-radius: 12px;
        background: transparent;
        box-shadow: none;
        }

        h2 {
                text-align: center;
            }
            .post {
        background-color: rgb(250, 249, 249);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 30px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15); /* Darker + more spread */
        border: 1px solid rgb(230, 6, 51); /* Fixed spacing */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .post:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(241, 9, 9, 0.8);
        }
        .comment-form {
            flex: 1;
            width: 100%;
            margin-top: 12px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        resize: vertical;
        font-family: 'Poppins', sans-serif;
        line-height: 1.4;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: border-color 0.3s ease;
    }

    .comment-form textarea:focus {
    box-shadow: 0 0 4px rgba(230, 6, 51, 0.5);
}


    .comment-form button {
        margin-top: 8px;
        padding: 8px 16px;
        background-color: #e3063b;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .comment-form button:hover {
        background-color: #b00333;
    }


        .post h3 {
            margin: 0 0 8px;
        }
        .post small {
            color: #666;
        }
        .actions a {
            margin-right: 8px;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            background: rgb(230, 6, 51);
        }
        .actions a:hover {
            background: #b00333;
        }
        .interaction-box {
            margin-top: 15px;
        }
        .interaction-actions {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .like-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .heart-icon {
            width: 32px;
            height: 32px;
            cursor: pointer;
            border: none;
            transition: transform 0.2s ease;
        }
        .heart-icon:active {
            transform: scale(1.3);
        }
        .like-btn {
            border: none;
            background: none;
            padding: 0;
            cursor: pointer;
        }
        .like-count {
            margin-top: 5px;
            font-size: 14px;
            color: #444;
            font-weight: bold;
        }
        .comment-form {
            flex: 1;
            max-width: 600px;
            margin-top: 10px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .comment-form button {
            margin-top: 8px;
            padding: 8px 16px;
            background-color: rgb(230, 6, 51);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        .comment-form button:hover {
            background-color: #b00333;
        }
        .comments {
            margin-top: 15px;
            padding-left: 20px;
            font-size: 14px;
            color: #333;
            list-style-type: disc;
        }
        .comments li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="overlay">
    <div class="container">
        <h2>All Posts</h2>
        <?php include 'search_form.php'; ?>

        <?php if ($result->num_rows == 0): ?>
            <p>No posts found.</p>
        <?php else: ?>
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post" id="post-<?php echo $post['id']; ?>">
                    <?php
                    $post_id = $post['id'];
                    $liked = false;
                    if ($_SESSION['role'] === 'user') {
                        $like_check = $conn->prepare("SELECT 1 FROM likes WHERE post_id = ? AND user_id = ? LIMIT 1");
                        $like_check->bind_param("ii", $post_id, $user_id);
                        $like_check->execute();
                        $liked = $like_check->get_result()->num_rows > 0;
                    }
                    ?>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <small>Created: <?php echo $post['created_at']; ?></small>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="actions">
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                            <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'user'): ?>
                        <div class="interaction-box">
                            <div class="interaction-actions">
                                <form method="POST" action="like_post.php" class="like-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                    <button type="submit" class="like-btn">
                                        <img src="<?php echo $liked ? 'heart_pink.jpg' : 'heart_white.jpg'; ?>" alt="Like" class="heart-icon" />
                                    </button>
                                    <div class='like-count'>
                                        <?php
                                        $like_stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE post_id = ?");
                                        $like_stmt->bind_param("i", $post_id);
                                        $like_stmt->execute();
                                        $like_count = $like_stmt->get_result()->fetch_assoc()['total'];
                                        echo $like_count > 0 ? $like_count : "Like";
                                        ?>
                                    </div>
                                </form>

                                <form method="POST" action="add_comment.php" class="comment-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <textarea name="comment" placeholder="💬 Add a comment..." required></textarea>
                                    <button type="submit">Comment</button>
                                </form>
                            </div>

                            <?php
                            $comment_stmt = $conn->prepare("
                                SELECT comments.comment, comments.created_at, users.username 
                                FROM comments 
                                JOIN users ON comments.user_id = users.id 
                                WHERE comments.post_id = ? 
                                ORDER BY comments.created_at DESC
                            ");
                            $comment_stmt->bind_param("i", $post_id);
                            $comment_stmt->execute();
                            $comment_result = $comment_stmt->get_result();
                            ?>

                            <ul class="comments">
                            <strong>💬 Comments:</strong>
                            <?php if ($comment_result->num_rows > 0): ?>
                                <?php while ($row = $comment_result->fetch_assoc()): ?>
                                    <li>
                                        <strong style="color: #e3063b;">
                                        <?php echo ($row['username'] === $_SESSION['username']) ? 'You' : htmlspecialchars($row['username']); ?>:
                                        </strong>
                                        <?php echo htmlspecialchars($row['comment']); ?>
                                        <small style="color: gray;"> (<?php echo $row['created_at']; ?>)</small>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li>No comments yet.</li>
                            <?php endif; ?>
                        </ul>

                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <div class="pagination">
            <?php include 'pagination.php'; ?>
        </div>
    </div>
</div>
</body>
</html>
