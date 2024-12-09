<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
</head>
<body>
    <h1>Welcome Applicant: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <a href="jobListings.php">View Job Listings</a>
    <a href="myApplications.php">My Applications</a>
    <a href="core/handleForms.php?logoutAUser=1">Logout</a>
</body>
</html>
