<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit();
}
require_once 'core/dbConfig.php';

// Get all messages sent to HR
$stmt = $pdo->prepare("SELECT * FROM messages WHERE to_user = ?");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
</head>
<body>
    <h1>Messages</h1>
    <table>
        <tr>
            <th>From</th>
            <th>Message</th>
        </tr>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['from_user']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
