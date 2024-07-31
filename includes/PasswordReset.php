<?php
require '../vendor/autoload.php'; // Adjust path as needed
class PasswordReset {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function verifyOtpAndResetPassword($email, $otp, $new_password) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND otp = ? AND otp_expiry >= NOW()");
        if (!$stmt) {
            return "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
        }
        $stmt->bind_param("ss", $email, $otp);
        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $stmt = $this->conn->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE id = ?");
            if (!$stmt) {
                return "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            }
            $stmt->bind_param("si", $hashed_password, $user['id']);
            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            return "Password reset successful.";
        } else {
            return "Invalid OTP or OTP expired.";
        }
    }
}
