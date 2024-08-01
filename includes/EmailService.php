<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // SMTP configuration
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'pubgidws@gmail.com'; // Your email address
        $this->mail->Password = 'lcdeiryfjiseeouw'; // Your email password or app-specific password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        // Set default "from" email address and name
        $this->mail->setFrom('your-email@gmail.com', 'Blood Donation System');
    }

    public function sendOtpEmail($to, $otp)
    {
        try {
            // Email details
            $this->mail->addAddress($to);
            $this->mail->Subject = 'Your OTP Code';
            $this->mail->Body    = "Your OTP code is: $otp";

            // Send email
            $this->mail->send();
        } catch (Exception $e) {
            throw new Exception("OTP email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
        }
    }
}
