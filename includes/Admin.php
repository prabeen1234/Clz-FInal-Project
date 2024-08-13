<?php

class Admin {
    private $con;

    public function __construct($dbConnection) {
        $this->con = $dbConnection;
    }

    // Update user status
    public function updateUserStatus($userId, $status) {
        $query = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = $this->con->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->con->error));
        }
        $stmt->bind_param("si", $status, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // Get user details by ID
    public function getUserDetails($userId) {
        $query = "SELECT id, fullname, email, mobile, state, age, weight, blood_type, sex, latitude, longitude FROM users WHERE id = ?";
        $stmt = $this->con->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->con->error));
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Delete user by ID
    public function deleteUser($userId) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->con->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->con->error));
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }

    // Get pending users
    public function getPendingUsers() {
        $query = "SELECT id, fullname, email, mobile FROM users WHERE status = 'pending'";
        $result = $this->con->query($query);
        if ($result === false) {
            die('Query failed: ' . htmlspecialchars($this->con->error));
        }
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
    public function getUserEmail($userId) {
        $stmt = $this->con->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['email'];
    }
    
}
?>
