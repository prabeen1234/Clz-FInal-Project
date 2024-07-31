<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../includes/Config.php';
$db = new Database();
$con = $db->getConnection();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($con->connect_error) {
    die('Database connection failed: ' . $con->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = $con->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['phone'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $age = $_POST['age'];
    $weight = $_POST['weight'];

    // Update user information
    $update_query = $con->prepare("UPDATE users SET fullname = ?, email = ?, mobile = ?, age = ?, weight = ?, latitude = ?, longitude = ? WHERE id = ?");
    $update_query->bind_param("sssiissi", $fullname, $email, $mobile, $age, $weight, $latitude, $longitude, $user_id);

    if ($update_query->execute()) {
        $message = "Profile updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update profile: " . $con->error;
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .dashboard {
            display: flex;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .side-menu {
            width: 250px;
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .side-menu h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .side-menu a.active, .side-menu a:hover {
            background-color: #0056b3;
        }

        .side-menu a.logout-btn {
            background-color: #dc3545;
        }

        .dashboard-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            overflow: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard-content h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            max-width: 650px;
            width: 100%;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #fff;
            font-weight: bold;
        }

        .message.success {
            background-color: #28a745;
        }

        .message.error {
            background-color: #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 15px;
        }

        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
    <title>Update Profile</title>
</head>
<body>
<div class="dashboard"> <div class="side-menu"> <h2>User Menu</h2> <a href="user_dashboard.php" >Search Blood</a> <a href="request_list.php">View Requests</a> <a href="update_profile.php" class="active">Update Profile</a> <a href="../logout.php" class="logout-btn">Logout</a> </div>

        <div class="dashboard-content">
            <h2>Update Your Information</h2>
            <div class="form-container">
                <?php if (isset($message)): ?>
                    <div class="message <?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form action="update_profile.php" method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight:</label>
                        <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($user['weight']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($user['latitude']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($user['longitude']); ?>" readonly required>
                    </div>
                    <div id="map"></div>
                    <div class="form-group">
                        <input type="submit" value="Update Info">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPYMI9P4d29sp8AGl_4z9py1ZEt8YXmcI&callback=initMap" async defer></script>
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: {lat: <?php echo htmlspecialchars($user['latitude']); ?>, lng: <?php echo htmlspecialchars($user['longitude']); ?>}
            });

            const marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: {lat: <?php echo htmlspecialchars($user['latitude']); ?>, lng: <?php echo htmlspecialchars($user['longitude']); ?>}
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
