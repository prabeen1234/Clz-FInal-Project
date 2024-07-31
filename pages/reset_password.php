<?php
session_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../includes/Config.php';
require '../includes/PasswordReset.php';


$error_message = $success_message = '';

if (!isset($_SESSION['otp_sent']) || !isset($_SESSION['email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

    if ($otp && $new_password) {
        $passwordReset = new PasswordReset();
        $result = $passwordReset->verifyOtpAndResetPassword($_SESSION['email'], $otp, $new_password);

        if ($result === "Password reset successful.") {
            unset($_SESSION['otp_sent']);
            unset($_SESSION['email']);
            echo '<script>
            alert("Password changed successfully");
            window.location.href = "../login.php";
            </script>';
        } else {
            $error_message = $result;
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
      body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .error, .success {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .container a {
            color: #28a745;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="otp">Enter the OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
