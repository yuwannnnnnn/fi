<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user information from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Role-based redirection for unauthorized access
if ($role !== 'hr' && $role !== 'applicant') {
    header("Location: unauthorized.php"); // Optional: Redirect to an unauthorized access page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
</head>
<body>
    <header>
        <h1>Welcome to FindHire</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <?php if ($role === 'hr'): ?>
                    <li><a href="createJobPost.php">Create Job Post</a></li>
                    <li><a href="viewApplications.php">View Applications</a></li>
                <?php elseif ($role === 'applicant'): ?>
                    <li><a href="jobListings.php">Job Listings</a></li>
                    <li><a href="myApplications.php">My Applications</a></li>
                <?php endif; ?>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="core/handleForms.php?logoutAUser=1">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>You are logged in as a <strong><?php echo htmlspecialchars($role); ?></strong>.</p>

        <?php if ($role === 'hr'): ?>
            <section>
                <h3>Your Job Posts</h3>
                <?php
                $jobPosts = getJobPosts($pdo); // Fetch job posts for HR
                if (!empty($jobPosts)) {
                    echo "<ul>";
                    foreach ($jobPosts as $job) {
                        echo "<li><strong>" . htmlspecialchars($job['title']) . "</strong> - " . htmlspecialchars($job['description']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No job posts found.</p>";
                }
                ?>
            </section>
        <?php elseif ($role === 'applicant'): ?>
            <section>
                <h3>Available Jobs</h3>
                <?php
                $jobPosts = getJobPosts($pdo); // Fetch available job posts for applicants
                if (!empty($jobPosts)) {
                    echo "<ul>";
                    foreach ($jobPosts as $job) {
                        echo "<li><strong>" . htmlspecialchars($job['title']) . "</strong> - " . htmlspecialchars($job['description']) . " 
                              <form action='core/handleForms.php' method='POST' enctype='multipart/form-data' style='display:inline;'>
                                  <textarea name='cover_message' placeholder='Why are you the best fit?' required></textarea>
                                  <input type='file' name='resume' accept='application/pdf' required>
                                  <input type='hidden' name='job_id' value='" . $job['id'] . "'>
                                  <button type='submit' name='applyJobBtn'>Apply</button>
                              </form>
                              </li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No jobs available at the moment.</p>";
                }
                ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> FindHire. All Rights Reserved.</p>
    </footer>
</body>
</html>
