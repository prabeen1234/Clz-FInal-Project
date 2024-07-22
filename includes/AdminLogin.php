<?php
class AdminLogin {
    private $db;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function authenticate($username, $password) {
        // Check if the credentials are "admin" and "admin"
        if ($username === 'admin' && $password === 'admin') {
            return true;
        }

        // Prepare a statement to avoid SQL injection for other users
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Check if the provided password matches the hashed password in the database
            if (password_verify($password, $admin['password'])) {
                return true;
            } else {
                error_log("Password verification failed.");
            }
        } else {
            error_log("Username not found or multiple results.");
        }

        return false;
    }
}
?>
