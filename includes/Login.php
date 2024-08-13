<?php
class Login {
    private $db;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function userLogin($email, $password) {
        // Prepare statement
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Check user status
            if ($user['status'] !== 'approved') {
                return false; // User is either pending or rejected
            }
            // Assuming passwords are hashed
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }
}
?>
