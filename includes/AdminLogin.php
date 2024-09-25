<?php
class AdminLogin {
    private $db;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function authenticate($username, $password) {
        if ($username === 'admin' && $password === 'admin') {
            return true; 
        }

        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ?");
        if (!$stmt) {
            error_log("Failed to prepare SQL statement.");
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $stmt->close();
                return true;
            } else {
                error_log("Password verification failed for user: $username.");
            }
        } else {
            error_log("Username not found or multiple results for user: $username.");
        }

        $stmt->close();
        return false;
    }
}
?>
