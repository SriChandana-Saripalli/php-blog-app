<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? "";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Count total users matching search
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE username LIKE ? OR email LIKE ?");
$like = "%$search%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$total_users = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Fetch users
$stmt = $conn->prepare("SELECT id, username, email, phone, city, gender, role FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ssii", $like, $like, $limit, $offset);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: url('flower.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .overlay {
            background: rgba(255, 255, 255, 0.88);
            min-height: 100vh;
            padding: 40px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: rgb(230, 6, 51);
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px 14px;
            width: 250px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            padding: 8px 18px;
            margin-left: 10px;
            background: rgb(230, 6, 51);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #f4f4f4;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .delete-btn:hover {
            background: #b00333;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            margin: 0 6px;
            padding: 6px 12px;
            background: rgb(230, 6, 51);
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .pagination a.active {
            background: #333;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        <h2>All Registered Users</h2>

        <form method="GET">
            <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Gender</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php if ($users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['city']) ?></td>
                        <td><?= $user['gender'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <a class="delete-btn" href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');">🗑️ Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No users found.</td></tr>
            <?php endif; ?>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
