<?php
require '../vendor/autoload.php'; // Adjust path as needed
require'../includes/EmailService.php';

use App\Services\EmailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPassword {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function sendOtp($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            return "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
        }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $otp = mt_rand(100000, 999999); // Generate a 6-digit OTP
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes")); // OTP valid for 15 minutes

            $stmt = $this->conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE id = ?");
            if (!$stmt) {
                return "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            }
            $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
            if (!$stmt->execute()) {
                return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            // Use EmailService to send the OTP
            try {
                $emailService = new EmailService();
                $emailService->sendOtpEmail($email, $otp);
                $_SESSION['otp_sent'] = true;
                $_SESSION['email'] = $email;
                session_write_close();
                header("Location: ../pages/reset_password.php");
                exit();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $e->getMessage());
                return "Failed to send the OTP. Please try again later.";
            }
        } else {
            return "Email not found.";
        }
    }
}
