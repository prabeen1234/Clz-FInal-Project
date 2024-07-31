<?php
require '../vendor/autoload.php'; // Adjust path as needed

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

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pubgidws@gmail.com'; // Your email address
                $mail->Password = 'lcdeiryfjiseeouw'; // Your email password or app-specific password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your-email@gmail.com', 'Blood Donation Management System');
                $mail->addAddress($email); // Recipient's email address

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "<p>Your OTP for password reset is: <strong>$otp</strong></p>";
                $mail->AltBody = "Your OTP for password reset is: $otp";

                $mail->send();
                $_SESSION['otp_sent'] = true;
                $_SESSION['email'] = $email;
                session_write_close();
                header("Location: ../pages/reset_password.php");
                exit();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                return "Failed to send the OTP. Please try again later.";
            }
        } else {
            return "Email not found.";
        }
    }
}
