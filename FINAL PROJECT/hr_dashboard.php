<?php
session_start();
require_once 'core/models.php';

// Check if the user is logged in and has the HR role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
</head>
<body>
    <h1>Welcome HR: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <a href="createJobPost.php">Create Job Post</a>
    <a href="viewApplications.php">View Applications</a>
    <a href="messages.php">Messages</a> <!-- HR can also check messages -->
    <a href="core/handleForms.php?logoutAUser=1">Logout</a>

    <h2>Your Job Posts</h2>
    <?php
    // Fetch and display job posts for HR
    $jobPosts = getJobPosts($user_id);  // Fetch job posts using the logged-in user ID
    if (!empty($jobPosts)) {
        echo "<ul>";
        foreach ($jobPosts as $job) {
            echo "<li><strong>" . htmlspecialchars($job['title']) . "</strong> - " . htmlspecialchars($job['description']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No job posts available.</p>";
    }
    ?>
</body>
</html>
