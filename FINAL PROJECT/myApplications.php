<?php
session_start();
require_once 'core/models.php';

// Check if the user is logged in and has the 'applicant' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the applicant's applications
$applications = getApplicationsByApplicant($user_id); // Function to be defined in models.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
</head>
<body>
    <h1>My Applications</h1>
    <nav>
        <a href="jobListings.php">Job Listings</a>
        <a href="index.php">Dashboard</a>
        <a href="core/handleForms.php?logoutAUser=1">Logout</a>
    </nav>

    <h2>Your Job Applications</h2>
    <?php
    if (!empty($applications)) {
        echo "<ul>";
        foreach ($applications as $application) {
            echo "<li><strong>" . htmlspecialchars($application['title']) . "</strong> - Status: " . htmlspecialchars($application['status']) . "</li>";
            echo "<p>Cover Message: " . htmlspecialchars($application['cover_message']) . "</p>";
            echo "<p>Resume: <a href='" . htmlspecialchars($application['resume']) . "' target='_blank'>View Resume</a></p>";
        }
        echo "</ul>";
    } else {
        echo "<p>You haven't applied to any jobs yet.</p>";
    }
    ?>
</body>
</html>
