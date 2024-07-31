<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../includes/Config.php';
$db = new Database();
$con = $db->getConnection();

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

    // Update user information
    $update_query = $con->prepare("UPDATE users SET fullname = ?, email = ?, mobile = ?, latitude = ?, longitude = ? WHERE id = ?");
    $update_query->bind_param("sssddi", $fullname, $email, $mobile, $latitude, $longitude, $user_id);

    if ($update_query->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Failed to update profile: " . $con->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
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
        }

        .dashboard-content h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .side-menu {
                width: 200px;
            }

            .dashboard-content {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .dashboard {
                flex-direction: column;
            }

            .side-menu {
                width: 100%;
                height: auto;
                box-shadow: none;
            }

            .dashboard-content {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Donor Menu</h2>
            <a href="donor_dashboard.php" class="active">Dashboard</a>
            <a href="update_profile.php" >Update Info</a>
            <a href="request_blood.php">View Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>Welcome to Donors Dashboard</h2>
           
        </div>
    </div>
</body>
</html>
