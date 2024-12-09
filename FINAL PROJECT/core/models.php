<?php
require_once 'dbConfig.php';

// Function to insert a new user
function insertNewUser($pdo, $username, $password, $role) {
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
        return ['status' => '200', 'message' => 'User registered successfully'];
    } catch (Exception $e) {
        return ['status' => '500', 'message' => $e->getMessage()];
    }
}

// Function to create a job post
function createJobPost($pdo, $title, $description, $created_by) {
    try {
        $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $created_by]);
        return ['status' => '200', 'message' => 'Job post created successfully'];
    } catch (Exception $e) {
        return ['status' => '500', 'message' => $e->getMessage()];
    }
}

// Function to apply for a job
function applyForJob($pdo, $job_id, $applicant_id, $resume_path) {
    try {
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume_path) VALUES (?, ?, ?)");
        $stmt->execute([$job_id, $applicant_id, $resume_path]);
        return ['status' => '200', 'message' => 'Application submitted successfully'];
    } catch (Exception $e) {
        return ['status' => '500', 'message' => $e->getMessage()];
    }
}

// Function to retrieve job posts
function getJobPosts($user_id) {
    global $pdo;  // This ensures that the $pdo object is available within the function.
    $stmt = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}


// Function to send a message
function sendMessage($pdo, $sender_id, $receiver_id, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
        return ['status' => '200', 'message' => 'Message sent successfully'];
    } catch (Exception $e) {
        return ['status' => '500', 'message' => $e->getMessage()];
    }
}

// Function to retrieve messages
function getMessages($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE receiver_id = ? OR sender_id = ?");
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getUserByUsername($pdo, $username) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}


// Function to login a user
function loginUser($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // User credentials are valid, return user data
            return [
                'success' => true,
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
        } else {
            // Invalid username or password
            return [
                'success' => false,
                'message' => 'Invalid username or password'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Get all HR users
function getAllHRUsers($pdo) {
    $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'hr'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all job posts
function getAllJobPosts() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM job_posts");
    $stmt->execute();
    return $stmt->fetchAll();
}


// Get messages for HR
function getMessagesForHR($pdo, $hr_id) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.username AS from_username 
        FROM messages m 
        JOIN users u ON m.from_user_id = u.id 
        WHERE m.to_user_id = ?
    ");
    $stmt->execute([$hr_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all job posts for applicants
function getJobPostsForApplicants() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM job_posts");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to fetch applications for a specific applicant
function getApplicationsByApplicant($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT job_posts.title, applications.status, applications.cover_message, applications.resume
        FROM applications
        JOIN job_posts ON applications.job_post_id = job_posts.id
        WHERE applications.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}


?>
