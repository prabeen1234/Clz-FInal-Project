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
$request_id = $_GET['request_id'];
$requested_user_id = $_GET['user_id'];

// Fetch user details
$user_query = $con->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $requested_user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Fetch request status
$request_query = $con->prepare("SELECT status FROM requests WHERE id = ?");
$request_query->bind_param("i", $request_id);
$request_query->execute();
$request_result = $request_query->get_result();
$request_status = $request_result->fetch_assoc()['status'];

// Extract latitude and longitude
$user_latitude = $user['latitude'];
$user_longitude = $user['longitude'];

// Handle POST request for accept/reject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action === 'Accept') {
        $update_query = $con->prepare("UPDATE requests SET status = 'Accepted', donor_id = ? WHERE id = ?");
        $update_query->bind_param("ii", $user_id, $request_id);
    } else if ($action === 'Reject') {
        $update_query = $con->prepare("UPDATE requests SET status = 'Declined', donor_id = ? WHERE id = ?");
        $update_query->bind_param("ii", $user_id, $request_id);
    }

    if ($update_query->execute()) {
        echo '<script>
        alert("Request updated successfully");
        window.location.href = "request_blood.php";
        </script>';
    } else {
        echo "Failed to update request.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
       body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f0f2f5;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
}

.content {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 100%;
    text-align: center;
}

h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #007bff;
}

.user-details {
    margin-top: 20px;
    text-align: left;
}

.user-details h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #007bff;
}

.user-details p {
    margin-bottom: 10px;
    line-height: 1.6;
}

.user-details form {
    margin-top: 30px;
}

.user-details input[type="submit"] {
    padding: 10px 20px;
    margin-right: 10px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1rem;
}

.user-details input[type="submit"]:hover {
    background-color: #0056b3;
}

.user-details input[type="submit"][name="action"][value="Reject"] {
    background-color: #dc3545;
}

.user-details input[type="submit"][name="action"][value="Reject"]:hover {
    background-color: #c82333;
}

#map {
    height: 400px;
    width: 100%;
    margin-top: 20px;
    border-radius: 12px;
}

@media (max-width: 768px) {
    .content {
        padding: 20px;
    }

    h2 {
        font-size: 1.5rem;
    }

    .user-details h3 {
        font-size: 1.2rem;
    }

    .user-details p {
        font-size: 0.9rem;
    }

    .user-details input[type="submit"] {
        font-size: 0.9rem;
        padding: 8px 16px;
    }
}
ok-button {
        padding: 10px 20px;
        margin-right: 10px;
        border: none;
        border-radius: 10px;
        background-color: #007bff; /* Blue color */
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 3rem;
    }

    .ok-button:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }
    </style>
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
                title: 'Requester Location'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
</head>
<body>
    <div class="content">
        <h2>User Details</h2>
        <div id="map"></div>
        <div class="user-details">
            <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($user['mobile']); ?></p>
            <p>Blood Group: <?php echo htmlspecialchars($user['blood_type']); ?></p>
            <p>Location: Latitude <?php echo htmlspecialchars($user['latitude']); ?>, Longitude <?php echo htmlspecialchars($user['longitude']); ?></p>
            <form method="post" action="">
                <?php if ($request_status === 'Pending'): ?>
                    <input type="submit" name="action" value="Accept">
                    <input type="submit" name="action" value="Reject">
                <?php else: ?>
                    <input type="button" class="ok-button" value="OK" onclick="window.location.href='request_blood.php';">
                    <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
