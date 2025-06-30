<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = $success = "";

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("UPDATE users SET phone = ?, city = ?, gender = ? WHERE id = ?");
    $stmt->bind_param("sssi", $phone, $city, $gender, $user_id);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        // Update session values
        $_SESSION['phone'] = $phone;
        $_SESSION['city'] = $city;
        $_SESSION['gender'] = $gender;
    } else {
        $error = "Update failed.";
    }
}

// Get user info from DB
$stmt = $conn->prepare("SELECT username, email, phone, city, gender FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $city, $gender);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 450px;
            margin: 60px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: rgb(230, 6, 51);
            text-align: center;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background-color: rgb(230, 6, 51);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        label {
            margin-top: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>My Profile</h2>
        <?php if ($success) echo "<div class='success'>$success</div>"; ?>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" value="<?php echo htmlspecialchars($username); ?>" disabled>

            <label>Email</label>
            <input type="text" value="<?php echo htmlspecialchars($email); ?>" disabled>

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

            <label>City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>">

            <label>Gender</label>
            <select name="gender">
                <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
            </select>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
