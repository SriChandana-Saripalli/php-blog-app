<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) exit();
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $gender = $_POST['gender'];
    $stmt = $conn->prepare("UPDATE users SET phone = ?, city = ?, gender = ? WHERE id = ?");
    $stmt->bind_param("sssi", $phone, $city, $gender, $_SESSION['user_id']);
    $success = $stmt->execute() ? "Updated successfully." : "Update failed.";
    $_SESSION['phone'] = $phone;
    $_SESSION['city'] = $city;
    $_SESSION['gender'] = $gender;
}
?>
<!DOCTYPE html>
<html><head><title>Update Profile</title></head><body>
<h2>Update Profile</h2>
<?php if ($success) echo "<p style='color:green'>$success</p>"; ?>
<form method="POST">
    <input type="text" name="phone" value="<?php echo $_SESSION['phone']; ?>" placeholder="Phone"><br>
    <input type="text" name="city" value="<?php echo $_SESSION['city']; ?>" placeholder="City"><br>
    <select name="gender">
        <option value="Male" <?php if ($_SESSION['gender'] == 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if ($_SESSION['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if ($_SESSION['gender'] == 'Other') echo 'selected'; ?>>Other</option>
    </select><br>
    <button type="submit">Update</button>
</form>
</body></html>