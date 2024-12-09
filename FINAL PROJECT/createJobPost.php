<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit();
}
require_once 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Insert the job post into the database
    $sql = "INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $_SESSION['user_id']]);

    header("Location: hr_dashboard.php"); // Redirect after successful job post creation
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
</head>
<body>
    <h1>Create a New Job Post</h1>
    <form method="POST" action="createJobPost.php">
        <label for="title">Job Title:</label>
        <input type="text" name="title" required>
        <br><br>
        <label for="description">Job Description:</label>
        <textarea name="description" required></textarea>
        <br><br>
        <button type="submit">Create Job Post</button>
    </form>
</body>
</html>
