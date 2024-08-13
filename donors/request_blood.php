<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/Config.php';
$db = new Database();
$con = $db->getConnection();

$user_id = $_SESSION['user_id'];

// Handle POST request for blood requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']);
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action === 'Accept') {
            $update_query = $con->prepare("UPDATE requests SET status = 'Accepted', donor_id = ? WHERE id = ?");
            $update_query->bind_param("ii", $user_id, $request_id);
        } else if ($action === 'Reject') {
            $update_query = $con->prepare("UPDATE requests SET status = 'Declined', donor_id = ? WHERE id = ?");
            $update_query->bind_param("ii", $user_id, $request_id);
        } else {
            $response = array("status" => "error", "message" => "Invalid action: '$action'.");
            echo json_encode($response);
            exit();
        }

        if ($update_query->execute()) {
            echo '<script>
            alert("Request updated successfully");
            window.location.href = "request_blood.php";
            </script>';
        } else {
            $response = array("status" => "error", "message" => "Failed to update request.");
            echo json_encode($response);
        }
        exit();
    }
}

// Fetch user details
$user_query = $con->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Extract latitude and longitude
$user_latitude = $user['latitude'];
$user_longitude = $user['longitude'];

// Fetch blood requests
$requests_query = "SELECT * FROM requests WHERE status = 'Pending' AND donor_id = $user_id";
$requests_result = $con->query($requests_query);

// Fetch accepted requests
$accepted_requests_query = "SELECT * FROM requests WHERE donor_id = ? AND status = 'Accepted'";
$accepted_requests_stmt = $con->prepare($accepted_requests_query);
$accepted_requests_stmt->bind_param("i", $user_id);
$accepted_requests_stmt->execute();
$accepted_requests_result = $accepted_requests_stmt->get_result();
$accepted_requests = $accepted_requests_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Blood Requests</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPYMI9P4d29sp8AGl_4z9py1ZEt8YXmcI&callback=initMap" async defer></script>
    <script>
        function initMap() {
            var mapOptions = {
                zoom: 12,
                center: { lat: parseFloat('<?php echo $user_latitude; ?>'), lng: parseFloat('<?php echo $user_longitude; ?>') }
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            var marker = new google.maps.Marker({
                position: { lat: parseFloat('<?php echo $user_latitude; ?>'), lng: parseFloat('<?php echo $user_longitude; ?>') },
                map: map,
                title: 'User Location'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
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
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .side-menu h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .side-menu a.active, .side-menu a:hover {
            background-color: #007bff;
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
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            border: 1px solid #ddd;
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

        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .request-table th, .request-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .request-table th {
            background-color: #f8f9fa;
            color: #333;
        }

        .request-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .request-table tbody tr:hover {
            background-color: #e9ecef;
        }

        .request-table td.actions {
            display: flex;
            justify-content: space-around;
        }

        .request-table td.actions form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .request-table td.actions form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .request-table td.actions form input[type="submit"].reject-btn {
            background-color: #dc3545;
        }

        .request-table td.actions form input[type="submit"].reject-btn:hover {
            background-color: #c82333;
        }

        .request-table td.actions form input[type="submit"].delete-btn {
            background-color: #6c757d;
        }

        .request-table td.actions form input[type="submit"].delete-btn:hover {
            background-color: #5a6268;
        }

        .accepted-requests {
            margin-top: 20px;
            width: 100%;
        }

        .accepted-requests h3 {
            margin-bottom: 10px;
            font-size: 24px;
            color: #28a745;
        }

        .accepted-requests ul {
            list-style-type: none;
            padding: 0;
        }

        .accepted-requests ul li {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .accepted-requests ul li span {
            display: block;
        }

        .accepted-requests ul li form input[type="submit"].delete-btn {
            background-color: #dc3545;
        }

        .accepted-requests ul li form input[type="submit"].delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Dashboard</h2>
            <a href="update_profile.php">Update Profile</a>
            <a href="request_blood.php" class="active">Blood Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="dashboard-content">
            <h2>Blood Requests</h2>
            <div id="map" style="height: 400px; width: 100%; margin-top: 20px;"></div>
            <?php
            if ($requests_result->num_rows > 0) {
                echo '<table class="request-table">';
                echo '<thead><tr><th>Requester Id</th><th>Created At</th><th>Status</th><th>Blood Group</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                while ($row = $requests_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['user_id'] . '</td>';
                    echo '<td>' . $row['created_at'] . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    echo '<td>' . $row['blood_type'] . '</td>';
                    echo '<td class="actions">';
                    echo '<form method="get" action="user_detail.php">';
                    echo '<input type="hidden" name="request_id" value="' . $row['id'] . '">';
                    echo '<input type="hidden" name="user_id" value="' . $row['user_id'] . '">';
                    echo '<input type="submit" value="View Details">';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No pending blood requests at the moment.</p>';
            }
            ?>
            <div class="accepted-requests">
    <h3>Accepted Requests</h3>
    <ul>
        <?php
        foreach ($accepted_requests as $request) {
            echo '<li>';
            echo '<span>Request ID: ' . $request['id'] . '</span>';
            echo '<span>Created At: ' . $request['created_at'] . '</span>';
            echo '<span><b>Please Visit to the Users Nearby Hospital or Blood Bank and Contact For Blood Donation <br>As Soon as Possible!!!!!!</b></span>';
            echo '<form method="get" action="user_detail.php">';
            echo '<input type="hidden" name="request_id" value="' . $request['id'] . '">';
            echo '<input type="hidden" name="user_id" value="' . $request['user_id'] . '">';
            echo '<input type="submit" value="View Details" style="background-color: gray; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease;">';
            echo '</form>';
            echo '</li>';
            
        }
        ?>
    </ul>
</div>
        </div>
    </div>
</body>
</html>
