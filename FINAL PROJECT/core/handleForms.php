<?php
// Include necessary files
require_once 'dbConfig.php';  // This will ensure that the PDO connection is established
require_once 'models.php';  // This will allow access to the model functions like insertNewUser

// Start the session only if it isn't already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle user registration
if (isset($_POST['registerUserBtn'])) {
    // Sanitize and assign input values
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = trim($_POST['role']); // 'applicant' or 'hr'

    if (!empty($username) && !empty($password) && !empty($role)) {
        // Call the function to insert the new user
        $result = insertNewUser($pdo, $username, $password, $role);
        
        // Store the message in session and redirect
        $_SESSION['message'] = $result['message'];
        header("Location: ../login.php");  // Redirect after successful registration
        exit();  // Always exit after a header redirect
    } else {
        $_SESSION['message'] = "Please fill in all fields";
        header("Location: ../register.php");  // Redirect back to the register page
        exit();
    }
}

// Handle job posting
if (isset($_POST['createJobPostBtn'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $created_by = $_SESSION['user_id'];

    if (!empty($title) && !empty($description)) {
        $result = createJobPost($pdo, $title, $description, $created_by);
        $_SESSION['message'] = $result['message'];
        header("Location: ../hr_dashboard.php"); // Adjusted to redirect to hr_dashboard.php outside of core
        exit();
    } else {
        $_SESSION['message'] = "Please fill in all fields";
        header("Location: ../hr_dashboard.php"); // Adjusted to redirect to hr_dashboard.php outside of core
        exit();
    }
}

if (isset($_POST['applyJobBtn'])) {
    $user_id = $_SESSION['user_id'];
    $job_id = $_POST['job_post_id'];
    $cover_message = trim($_POST['cover_message']);
    $resume = $_FILES['resume'];

    // Validate uploaded file
    if ($resume['type'] !== 'application/pdf' || $resume['size'] > 5 * 1024 * 1024) { // 5MB limit
        $_SESSION['message'] = "Invalid resume file. Please upload a PDF under 5MB.";
        header("Location: ../applicant_dashboard.php");
        exit();
    }

    // Save resume to server
    $upload_dir = 'uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $resume_path = $upload_dir . basename($resume['name']);
    move_uploaded_file($resume['tmp_name'], $resume_path);

    // Save application to database
    $query = "INSERT INTO applications (user_id, job_post_id, cover_message, resume) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $job_id, $cover_message, $resume_path]);

    $_SESSION['message'] = "Application submitted successfully!";
    header("Location: ../applicant_dashboard.php");
    exit();
}


if (isset($_FILES['resume'])) {
    $file_tmp = $_FILES['resume']['tmp_name'];
    $file_name = $_FILES['resume']['name'];
    $file_path = 'uploads/resumes/' . $file_name;
    
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Insert the file path into the database
        $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_post_id, cover_message, resume_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $job_post_id, $cover_message, $file_path]);
    }
}


if ($_FILES['resume']['error'] == 0) {
    // Assuming you are using a folder called 'uploads/resumes/'
    $targetDir = 'uploads/resumes/';
    $targetFile = $targetDir . basename($_FILES['resume']['name']);
    
    if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
        // Save the file path to the database
        $stmt = $pdo->prepare("UPDATE applications SET resume_path = ? WHERE id = ?");
        $stmt->execute([$targetFile, $application_id]);
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

if (isset($_POST['sendMessageBtn'])) {
    $from_user_id = $_SESSION['user_id'];
    $to_user_id = $_POST['hr_id'];
    $message = trim($_POST['message']);

    $query = "INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$from_user_id, $to_user_id, $message]);

    $_SESSION['message'] = "Message sent successfully!";
    header("Location: ../applicant_dashboard.php");
    exit();
}


if (isset($_POST['loginUserBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $user = getUserByUsername($pdo, $username);

        if ($user && password_verify($password, $user['password'])) {
            // Store user details in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'hr') {
                header("Location: ../hr_dashboard.php"); // Redirect HR to HR dashboard
            } elseif ($user['role'] === 'applicant') {
                header("Location: ../applicant_dashboard.php"); // Redirect applicants to Applicant dashboard
            } else {
                $_SESSION['message'] = "Invalid role. Contact system administrator.";
                header("Location: ../login.php");
            }
            exit();
        } else {
            $_SESSION['message'] = "Invalid username or password.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
        header("Location: ../login.php");
        exit();
    }
}

if (isset($_POST['rejectApplicationBtn'])) {
    $application_id = $_POST['application_id'];
    $status = 'rejected';

    // Update application status to rejected
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $application_id]);

    // Send message to applicant
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    $message = "Dear " . $application['applicant_name'] . ", your application for the job '" . $application['job_title'] . "' has been rejected.";
    // Insert message to applicants (you can adjust this part based on your messaging system)
    $stmt = $pdo->prepare("INSERT INTO messages (from_user, to_user, message) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $application['applicant_id'], $message]);

    header("Location: ../viewApplications.php"); // Redirect back to applications page
    exit();
}

if (isset($_POST['acceptApplicationBtn'])) {
    $application_id = $_POST['application_id'];
    $status = 'accepted';

    // Update application status to accepted
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $application_id]);

    // Send message to applicant
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    $message = "Dear " . $application['applicant_name'] . ", your application for the job '" . $application['job_title'] . "' has been accepted.";
    // Insert message to applicants
    $stmt = $pdo->prepare("INSERT INTO messages (from_user, to_user, message) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $application['applicant_id'], $message]);

    header("Location: ../viewApplications.php"); // Redirect back to applications page
    exit();
}

if (isset($_POST['applyJobBtn'])) {
    // Get job post ID, cover message, and resume
    $job_post_id = $_POST['job_post_id'];
    $cover_message = $_POST['cover_message'];
    $user_id = $_SESSION['user_id'];

    // Handle file upload (resume)
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resumeTmpPath = $_FILES['resume']['tmp_name'];
        $resumeName = $_FILES['resume']['name'];
        $resumeDestPath = 'uploads/resumes/' . $resumeName;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($resumeTmpPath, $resumeDestPath)) {
            // Insert the application data into the database
            $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_post_id, cover_message, resume) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $job_post_id, $cover_message, $resumeDestPath]);

            // Redirect to a confirmation page or the user's applications page
            header("Location: ../myApplications.php");
            exit();
        } else {
            echo "Error uploading resume file.";
        }
    } else {
        echo "Error with the file upload.";
    }
}

if (isset($_GET['logoutAUser'])) {
    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: ../login.php");
    exit();
}
?>
