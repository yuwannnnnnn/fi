<?php
session_start();
require_once 'core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

// Fetch all job posts (you can filter if needed)
$jobPosts = getAllJobPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
</head>
<body>
    <h1>Available Job Listings</h1>

    <?php
    foreach ($jobPosts as $job) {
        echo "<h2>" . htmlspecialchars($job['title']) . "</h2>";
        echo "<p>" . htmlspecialchars($job['description']) . "</p>";
        echo "<form action='core/handleForms.php' method='POST' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='job_post_id' value='" . $job['id'] . "'>";
        echo "<textarea name='cover_message' placeholder='Why are you the best candidate for this job?' required></textarea><br>";
        echo "<input type='file' name='resume' accept='.pdf' required><br>";
        echo "<button type='submit' name='applyJobBtn'>Apply</button>";
        echo "</form>";
    }
    ?>
</body>
</html>
