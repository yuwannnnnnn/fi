<?php
session_start(); // Start the session at the very beginning

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert"><?php echo $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form action="core/handleForms.php" method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" required><br>

        <label for="password">Password</label>
        <input type="password" name="password" required><br>

        <label for="role">Role</label>
        <select name="role" required>
            <option value="applicant">Applicant</option>
            <option value="hr">HR</option>
        </select><br>

        <button type="submit" name="registerUserBtn">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
