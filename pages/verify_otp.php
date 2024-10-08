<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/Config.php';
require '../includes/Register.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredOtp = $_POST['otp'];
    if ($enteredOtp == $_SESSION['otp']) {
        // OTP is correct, proceed with registration
        $data = $_SESSION['registration_data'];
        $imageBlob = isset($_SESSION['registration_data']['image']) ? $_SESSION['registration_data']['image'] : null;

        // Get database connection
        $db = new Database();
        $con = $db->getConnection();

        // Create an instance of Register and call handleRegistration
        $register = new Register($con);
        $register->handleRegistration($data, $imageBlob);

        // Clear session data
        unset($_SESSION['otp']);
        unset($_SESSION['registration_data']);
    } else {
        echo '<script>
        alert("Invalid OTP");
        </script>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6b73ff 0%, #000dff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .otp-verification {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s;
        }
        .otp-verification:hover {
            transform: translateY(-5px);
        }
        .otp-verification h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 28px;
            font-weight: 700;
        }
        .otp-verification form {
            display: flex;
            flex-direction: column;
        }
        .otp-verification label {
            margin-bottom: 10px;
            font-size: 18px;
            color: #555;
            font-weight: 600;
        }
        .otp-verification input[type="text"] {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .otp-verification input[type="text"]:focus {
            outline: none;
            border-color: #6b73ff;
        }
        .otp-verification button {
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #28a745;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .otp-verification button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        @media (max-width: 480px) {
            .otp-verification {
                padding: 30px;
                box-shadow: none;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body>
<div class="otp-verification">
    <h2>Verify OTP</h2>
    <form action="verify_otp.php" method="post">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <button type="submit">Verify OTP</button>
    </form>
</div>
</body>
</html>
