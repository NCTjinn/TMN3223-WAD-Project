<?php
// User.php - User management and authentication
require_once 'config.php';

class User {
    private $conn;
    private $table = 'Users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($data) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                    (username, first_name, last_name, display_name, email, password, role) 
                    VALUES (:username, :first_name, :last_name, :display_name, :email, :password, :role)";

            $stmt = $this->conn->prepare($query);
            
            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Bind values
            $stmt->bindParam(":username", $data['username']);
            $stmt->bindParam(":first_name", $data['first_name']);
            $stmt->bindParam(":last_name", $data['last_name']);
            $stmt->bindParam(":display_name", $data['display_name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":role", $data['role']);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'user_id' => $this->conn->lastInsertId()
                ];
            }
            return ['status' => 'error', 'message' => 'Registration failed'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT user_id, username, password, role FROM " . $this->table . " 
                     WHERE username = :username OR email = :username";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            if($row = $stmt->fetch()) {
                if(password_verify($password, $row['password'])) {
                    // Create session
                    session_start();
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    return [
                        'status' => 'success',
                        'message' => 'Login successful',
                        'user' => [
                            'user_id' => $row['user_id'],
                            'username' => $row['username'],
                            'role' => $row['role']
                        ]
                    ];
                }
            }
            return ['status' => 'error', 'message' => 'Invalid credentials'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getUserById($userId) {
        try {
            $query = "SELECT user_id, username, first_name, last_name, display_name, email, role, points 
                     FROM " . $this->table . " WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateUser($userId, $data) {
        try {
            $updateFields = [];
            $params = [];

            // Build dynamic update query based on provided data
            foreach($data as $key => $value) {
                if($key !== 'user_id' && $key !== 'password') {
                    $updateFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if(!empty($updateFields)) {
                $query = "UPDATE " . $this->table . " SET " . implode(", ", $updateFields) . 
                         " WHERE user_id = :user_id";
                
                $stmt = $this->conn->prepare($query);
                $params[":user_id"] = $userId;
                
                if($stmt->execute($params)) {
                    return ['status' => 'success', 'message' => 'User updated successfully'];
                }
            }
            return ['status' => 'error', 'message' => 'No fields to update'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updatePoints($userId, $points) {
        try {
            $query = "UPDATE " . $this->table . " SET points = points + :points 
                     WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":points", $points);
            $stmt->bindParam(":user_id", $userId);

            if($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Points updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Failed to update points'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return ['status' => 'success', 'message' => 'Logged out successfully'];
    }
}