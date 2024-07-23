<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../includes/Config.php';
include '../includes/User.php';

$con = new Database();
$con = $con->getConnection();
$user = new User($con);

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['request_blood'])) {
        $donor_id = $_POST['donor_id'];
        $blood_type = $_POST['blood_type'];

        if ($user->sendBloodRequest($user_id, $donor_id, $blood_type)) {
            $message = "Blood request sent successfully!";
        } else {
            $message = "Failed to send blood request.";
        }
    }
}

if (isset($_POST['search'])) {
    $blood_type = $_POST['blood_type'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $donors_result = $user->searchDonors($blood_type, $latitude, $longitude);
} else {
    $donors_result = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <style>
        /* Existing CSS styles */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8b500, #f6d365);
            margin: 0;
            padding: 0;
        }
        .dashboard {
            display: flex;
            height: 100vh;
        }
        .side-menu {
            width: 250px;
            background: #333;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .side-menu h2 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            text-align: center;
        }
        .side-menu a {
            display: block;
            color: #fff;
            padding: 10px;
            text-decoration: none;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .side-menu a:hover {
            background: #575757;
        }
        .logout-btn {
            background: #f44336;
        }
        .dashboard-content {
            flex: 1;
            padding: 20px;
            background: #f9f9f9;
        }
        .dashboard-content h2 {
            margin-top: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .form-container .form-group {
            margin-bottom: 15px;
        }
        .form-container label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-container input[type="text"],
        .form-container select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container input[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #f8b500;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-container input[type="submit"]:hover {
            background: #f6d365;
        }
        .message {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #d6e9c6;
        }
        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .request-table th, .request-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .request-table th {
            background: #f8b500;
            color: #fff;
        }
        .request-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .request-table .actions {
            text-align: center;
        }
        .request-table .actions form {
            display: inline;
        }
        .request-table .actions input[type="submit"] {
            background: #4CAF50;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .request-table .actions input[type="submit"]:hover {
            background: #45a049;
        }
        #map {
            height: 400px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
    <title>Search Blood</title>
    
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
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Dashboard</h2>
            <a href="user_dashboard.php">Search Blood</a>
            <a href="request_list.php">View Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>Search for Blood Donors</h2>
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form action="search_blood.php" method="post">
                    <div class="form-group">
                        <label for="blood_type">Blood Type:</label>
                        <select id="blood_type" name="blood_type" required>
                            <option value="">Select Blood Type</option>
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
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" id="latitude" name="latitude" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" id="longitude" name="longitude" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="search" value="Search Donors">
                    </div>
                </form>

                <div id="map"></div>

                <?php if (!empty($donors_result)): ?>
                    <h3>Available Donors</h3>
                    <table class="request-table">
                        <thead>
                            <tr>
                                <th>Donor ID</th>
                                <th>Blood Type</th>
                                <th>Full Name</th>
                                <th>Distance (km)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donors_result as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['bloodgroup']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo number_format($row['distance'], 2); ?></td>
                                    <td class="actions">
                                        <form action="search_blood.php" method="post" style="display:inline;">
                                            <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                            <input type="hidden" name="blood_type" value="<?php echo htmlspecialchars($row['bloodgroup']); ?>">
                                            <input type="submit" name="request_blood" value="Request Blood">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No donors found for the given criteria.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
