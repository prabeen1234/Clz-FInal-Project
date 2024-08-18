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

        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'pubgidws@gmail.com';
        $this->mail->Password = 'lcdeiryfjiseeouw'; 
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

  
        $this->mail->setFrom('your-email@gmail.com', 'Blood Donation System');
    }

    public function sendOtpEmail($to, $otp)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = 'This is Confidential. Dont share to anyone';
            $this->mail->Body    = "$otp";

            $this->mail->send();
        } catch (Exception $e) {
            throw new Exception("OTP email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
        }
    }
}
