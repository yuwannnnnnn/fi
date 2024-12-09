CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('applicant', 'hr') NOT NULL
);

CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Foreign key to the applicants
    job_post_id INT NOT NULL, -- Foreign key to job posts
    cover_message TEXT NOT NULL,
    resume VARCHAR(255) NOT NULL, -- Stores the path of the uploaded PDF file
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_post_id) REFERENCES job_posts(id)
);


CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user_id INT NOT NULL, -- Sender
    to_user_id INT NOT NULL, -- Recipient
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id),
    FOREIGN KEY (to_user_id) REFERENCES users(id)
);
