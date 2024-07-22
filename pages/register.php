<?php
session_start();
require '../includes/Config.php';
require '../includes/Register.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new Database();
$con = $db->getConnection(); // Initialize the database connection

$register = new Register($con); // Pass the connection to Register class

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$register->handleRegistration($_POST);
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
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
background-color: #f4f6f9;
color: #333;
margin: 0;
padding: 0;
}

.register {
background-color: #ffffff;
padding: 30px;
border-radius: 10px;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
width: 90%;
max-width: 600px;
margin: 50px auto;
text-align: center;
border: 1px solid #ddd;
}

.register h2 {
margin-bottom: 20px;
font-size: 28px;
color: #007bff;
}

.register label {
display: block;
margin: 15px 0 5px;
font-weight: bold;
color: #555;
}

.register input[type="text"],
.register input[type="email"],
.register input[type="password"],
.register input[type="number"],
.register select {
width: calc(100% - 20px);
padding: 12px;
margin: 10px 0;
border: 1px solid #ccc;
border-radius: 6px;
font-size: 16px;
box-sizing: border-box;
background-color: #fafafa;
}

.register input[type="text"]:focus,
.register input[type="email"]:focus,
.register input[type="password"]:focus,
.register input[type="number"]:focus,
.register select:focus {
border-color: #007bff;
outline: none;
}

.register button {
width: 100%;
padding: 15px;
border: none;
border-radius: 6px;
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
border-radius: 6px;
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
<body onload="initMap()">

<div class="register">
<h2><b>Registration Form</b></h2>
<form action="register.php" method="post">
<label for="username">Username:</label>
<input type="text" id="username" name="username" required>

<label for="password">Password:</label>
<input type="password" id="password" name="password" required>

<label for="fullname">Fullname:</label>
<input type="text" id="fullname" name="fullname" required>

<label for="age">Age:</label>
<input type="number" id="age" name="age" required min="1" max="120">

<label for="sex">Sex:</label>
<select id="sex" name="sex" required>
<option value="male">Male</option>
<option value="female">Female</option>
<option value="other">Other</option>
</select>

<label for="bloodgroup">Blood Group:</label>
<select name="bloodgroup" required>
<option value="">Select Blood Group</option>
<option value="Apos">A+</option>
<option value="Aneg">A-</option>
<option value="Bpos">B+</option>
<option value="Bneg">B-</option>
<option value="Opos">O+</option>
<option value="Oneg">O-</option>
<option value="ABpos">AB+</option>
<option value="ABneg">AB-</option>
<option value="All">Blood Bank</option>
</select>

<label for="role">Role:</label>
<select name="role" required>
<option value="">Select Role</option>
<option value="donor">Donor</option>
<option value="user">User</option>
<!-- Add other options here -->
</select>

<label for="mobile">Mobile:</label>
<input type="text" id="mobile" name="mobile" required>

<label for="email">Email:</label>
<input type="email" id="email" name="email" required>

<label for="town">Town:</label>
<input type="text" id="town" name="town" required>

<label for="state">State:</label>
<input type="text" id="state" name="state" required>

<label for="latitude">Latitude:</label>
<input type="text" id="latitude" name="latitude" readonly required>

<label for="longitude">Longitude:</label>
<input type="text" id="longitude" name="longitude" readonly required>

<button type="submit">Register</button>
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
</script>
</body>
</html>


