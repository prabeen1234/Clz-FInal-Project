<?php
use App\Services\EmailService;
session_start();
require '../includes/Config.php';
require '../includes/Register.php';
require '../vendor/autoload.php'; // Adjust path as needed

require '../includes/EmailService.php';
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new Database();
$con = $db->getConnection(); // Initialize the database connection

$register = new Register($con); // Pass the connection to Register class

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    // Check if the mobile or email is already registered
    if ($register->isPhoneRegistered($mobile) || $register->isEmailRegistered($email)) {
        echo '<script>
                alert("Mobile or Email is already registered");
                window.location.href = "../login.php";
              </script>';
    } else {
        try {
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $_SESSION['otp'] = $otp; // Store OTP in session
            $_SESSION['registration_data'] = $_POST; // Store registration data

            // Send OTP to user's email
            $emailService = new EmailService();
            $emailService->sendOtpEmail($email, $otp);

            // Redirect to OTP verification page
            header("Location: verify_otp.php");
            exit;
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
            color: #495057;
            margin: 0;
            padding: 0;
        }

        .register {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 700px;
            margin: 50px auto;
            border: 1px solid #ced4da;
        }

        .register h2 {
            margin-bottom: 25px;
            font-size: 32px;
            color: #007bff;
            text-align: center;
        }

        .register label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
            color: #343a40;
        }

        .register input[type="text"],
        .register input[type="email"],
        .register input[type="password"],
        .register input[type="number"],
        .register select {
            width: calc(100% - 24px);
            padding: 14px;
            margin: 8px 0;
            border: 2px solid #ced4da;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f8f9fa;
        }

        .register input[type="text"]:focus,
        .register input[type="email"]:focus,
        .register input[type="password"]:focus,
        .register input[type="number"]:focus,
        .register select:focus {
            border-color: #007bff;
            outline: none;
        }

        .register .password-wrapper {
            position: relative;
        }

        .register .password-wrapper input[type="password"] {
            padding-right: 45px;
        }

        .register .password-wrapper .toggle-eye {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #007bff;
            font-size: 20px;
        }

        .register button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .register button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8d7da;
            color: #721c24;
            font-size: 16px;
            border: 1px solid #f5c6cb;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        @media (max-width: 768px) {
            .register {
                width: 95%;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
<div class="register">
    <h2><b>Registration Form</b></h2>
    <form id="registration-form" action="register.php" method="post">
        <label for="fullname">Fullname:</label>
        <input type="text" id="fullname" name="fullname" required oninput="validateForm()">

        <label for="password">Password:</label>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" required pattern=".{8,}" title="Password should be at least 8 characters long." oninput="validateForm()">
            <span class="toggle-eye" onclick="togglePassword()">👁️</span>
        </div>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required min="1" max="120" oninput="validateForm()">

        <label for="sex">Sex:</label>
        <select id="sex" name="sex" required oninput="validateForm()">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>

        <label for="blood_type">Blood Group:</label>
        <select name="blood_type" required oninput="validateForm()">
            <option value="">Select Blood Group</option>
            <option value="Apos">A+</option>
            <option value="Aneg">A-</option>
            <option value="Bpos">B+</option>
            <option value="Bneg">B-</option>
            <option value="Opos">O+</option>
            <option value="Oneg">O-</option>
            <option value="ABpos">AB+</option>
            <option value="ABneg">AB-</option>
        </select>

        <label for="role">Role:</label>
        <select name="role" required oninput="validateForm()">
            <option value="">Select Role</option>
            <option value="donor">Donor</option>
            <option value="user">User</option>
        </select>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required maxlength="10" pattern="9\d{9}" title="Mobile number should start with 9 and be exactly 10 digits long." oninput="validateForm()">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required oninput="validateForm()">

        <label for="weight">Weight:</label>
        <input type="number" id="weight" name="weight" required oninput="validateForm()">

        <label for="state">State:</label>
        <input type="text" id="state" name="state" required oninput="validateForm()">

        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" readonly required>

        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" readonly required>

        <button type="submit" id="register-btn" disabled>Register</button>
    </form>
    <div id="map"></div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPYMI9P4d29sp8AGl_4z9py1ZEt8YXmcI&callback=initMap" async defer></script>
<script>
function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: {lat: 27.7172, lng: 85.3240}
    });

    const marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: 27.7172, lng: 85.3240}
    });

    google.maps.event.addListener(map, 'click', function(event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        marker.setPosition(event.latLng);
    });

    google.maps.event.addListener(marker, 'dragend', function(event) {
        document.getElementById('latitude').value = this.getPosition().lat();
        document.getElementById('longitude').value = this.getPosition().lng();
    });
}

function togglePassword() {
    var passwordField = document.getElementById("password");
    var toggleEye = document.querySelector(".toggle-eye");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleEye.textContent = "🙈";
    } else {
        passwordField.type = "password";
        toggleEye.textContent = "👁️";
    }
}

function validateForm() {
    const form = document.getElementById('registration-form');
    const button = document.getElementById('register-btn');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.checkValidity()) {
            isValid = false;
        }
    });

    button.disabled = !isValid;
}
</script>
</body>
</html>
