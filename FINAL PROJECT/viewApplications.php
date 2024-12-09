<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}
require_once 'core/dbConfig.php';

// Get all applications for HR, including applicant name, job title, and resume path
$stmt = $pdo->prepare("
    SELECT a.*, u.username AS applicant_name, j.title AS job_title, a.resume_path
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN job_posts j ON a.job_post_id = j.id
    WHERE j.created_by = ?
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
</head>
<body>
    <h1>Applications</h1>
    <table>
        <tr>
            <th>Applicant</th>
            <th>Job Title</th>
            <th>Status</th>
            <th>Resume</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($applications as $app): ?>
            <tr>
                <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                <td><?php echo htmlspecialchars($app['status']); ?></td>
                <td>
    <?php
        // Debugging: Output the resume file path
        var_dump($app['resume_path']); 
    ?>
    <?php if (!empty($app['resume_path']) && file_exists($app['resume_path'])): ?>
        <a href="<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank">View Resume</a>
    <?php else: ?>
        No resume uploaded or file is missing.
    <?php endif; ?>
</td>

                <td>
                    <form action="core/handleForms.php" method="POST">
                        <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                        <button type="submit" name="rejectApplicationBtn">Reject</button>
                        <button type="submit" name="acceptApplicationBtn">Accept</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
