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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
       body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #72edf2 10%, #5151e5 100%);
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background-size: cover;
    background-position: center;
}

.register-container {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 30px 40px;
    width: 100%;
    max-width: 800px;
    box-sizing: border-box;
    position: relative;
    transition: transform 0.3s, box-shadow 0.3s;
}

.register-container:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    text-decoration: none;
    color: #333;
    font-size: 18px;
    display: flex;
    align-items: center;
    background: #f1f1f1;
    padding: 8px 12px;
    border-radius: 50px;
    transition: background 0.3s;
}

.back-button:hover {
    background: #007bff;
    color: #fff;
}

.back-button i {
    margin-right: 8px;
    font-size: 20px;
}

.register-container h2 {
    
    text-align: center;
    color: #007bff;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 40px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: red;
}

.register-container label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
    display: block;
}

.register-container input,
.register-container select {
    width: 100%;
    padding: 12px;
    margin: 8px 0 20px 0;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-sizing: border-box;
    font-size: 16px;
    color: #333;
    background: #f9f9f9;
    transition: border-color 0.3s, background 0.3s;
}

.register-container input:focus,
.register-container select:focus {
    border-color: #007bff;
    background: #fff;
    outline: none;
}

.register-container button {
    width: 100%;
    background-color: #007bff;
    color: white;
    padding: 14px;
    margin: 20px 0;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.register-container button:hover {
    background-color: #0056b3;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.register-container .toggle-eye {
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #007bff;
    font-size: 20px;
}

#map {
    height: 300px;
    margin-top: 20px;
    border-radius: 15px;
    border: 2px solid #ddd;
    transition: border-color 0.3s;
}

#map:hover {
    border-color: #007bff;
}

input[type="text"]:hover,
input[type="number"]:hover,
input[type="email"]:hover,
select:hover {
    background-color: #e6f7ff;
}

input[type="text"]::placeholder,
input[type="number"]::placeholder,
input[type="email"]::placeholder,
select::placeholder {
    color: #aaa;
}

input[type="text"]::-webkit-input-placeholder,
input[type="number"]::-webkit-input-placeholder,
input[type="email"]::-webkit-input-placeholder,
select::-webkit-input-placeholder {
    color: #aaa;
}

input[type="text"]:-ms-input-placeholder,
input[type="number"]:-ms-input-placeholder,
input[type="email"]:-ms-input-placeholder,
select:-ms-input-placeholder {
    color: #aaa;
}

input[type="text"]::-ms-input-placeholder,
input[type="number"]::-ms-input-placeholder,
input[type="email"]::-ms-input-placeholder,
select::-ms-input-placeholder {
    color: #aaa;
}

input[type="text"]::placeholder,
input[type="number"]::placeholder,
input[type="email"]::placeholder,
select::placeholder {
    color: #aaa;
}

    </style>
</head>
<body>
    <div class="register-container">
        <a href="../index.php" class="back-button">
            <i>←</i> Back
        </a>
        <h2>Registration Form</h2>
        <form id="registration-form" action="register.php" method="post">
            <label for="fullname">Fullname:</label>
            <input type="text" id="fullname" name="fullname" required oninput="validateForm()">

            <label for="password">Password:</label>
            <div class="password-wrapper" style="position:relative;">
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
            center: { lat: 27.7172, lng: 85.3240 }
        });

        const marker = new google.maps.Marker({
            map: map,
            draggable: true,
            position: { lat: 27.7172, lng: 85.3240 }
        });

        google.maps.event.addListener(map, 'click', function (event
) {
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

function validateForm() {
    const form = document.getElementById('registration-form');
    const inputs = form.querySelectorAll('input, select');
    let valid = true;

    inputs.forEach(input => {
        if (!input.checkValidity()) {
            valid = false;
        }
    });

    document.getElementById('register-btn').disabled = !valid;
}

function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordType = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = passwordType;
}
</script>
</body>
</html>
