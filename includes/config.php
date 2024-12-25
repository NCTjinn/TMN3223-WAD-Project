<?php
// config.php - Database connection and core configuration
class Database {
    private $host = "localhost";
    private $db_name = "PuffLab";
    private $username = "root";  // Change as per your configuration
    private $password = "";      // Change as per your configuration
    private $conn = null;

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// Constants for application configuration
define('SECRET_KEY', 'your_secret_key_here'); // Used for JWT tokens
define('UPLOAD_PATH', '../uploads/');         // Path for file uploads
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);    // 5MB max file size