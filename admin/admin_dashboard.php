<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/Config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Database connection
$db = new Database();
$con = $db->getConnection();

// Query to get analytics data
$donations_query = "SELECT COUNT(*) AS total_requests, 
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS successful_donations, 
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_requests,
                    AVG(TIMESTAMPDIFF(HOUR, created_at, accepted_date)) AS avg_response_time
                    FROM requests";
$donations_result = mysqli_query($con, $donations_query);
$data = mysqli_fetch_assoc($donations_result);

// Extract analytics data
$total_requests = $data['total_requests'];
$successful_donations = $data['successful_donations'];
$pending_requests = $data['pending_requests'];
$avg_response_time = round($data['avg_response_time'], 2);

if (!$donations_result) {
    die("Database query failed: " . mysqli_error($con));
}

// Query for pending requests
$pending_requests_query = "SELECT * FROM requests WHERE status = 'pending'";
$pending_result = mysqli_query($con, $pending_requests_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
            color: #343a40;
        }

        /* Dashboard Layout */
        .dashboard {
            display: flex;
            width: 100%;
        }

        .side-menu {
            width: 250px;
            background-color: #007bff;
            color: #fff;
            padding: 30px 20px;
            height: 100%;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
        }

        .side-menu h2 {
            font-size: 24px;
            margin-bottom: 40px;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 15px;
            margin: 10px 0;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .side-menu a.active, .side-menu a:hover {
            background-color: #0056b3;
        }

        .side-menu a.logout-btn {
            background-color: #dc3545;
            transition: background-color 0.3s ease;
        }

        .dashboard-content {
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 250px);
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-content h2 {
            font-size: 32px;
            color: #007bff;
            margin-bottom: 30px;
            font-weight: 600;
        }

        /* Flexbox for stat boxes */
        .stat-box-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            flex: 1 1 calc(25% - 20px); /* Adjust size for 4 boxes in a row */
            background-color: #fff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .stat-box h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stat-box:hover {
            transform: scale(1.05);
        }

        /* Colorful request list */
        .request-list {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .request-list h3 {
            font-size: 28px;
            color: #343a40;
            margin-bottom: 20px;
        }

        .request-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #f1f1f1;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .request-item:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .request-item:hover {
            background-color: #007bff;
            color: #fff;
            transform: translateY(-5px);
        }

        .request-item h4 {
            font-size: 20px;
            font-weight: 600;
        }

        .request-item .status {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffc107;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <div class="side-menu">
        <h2>Admin Menu</h2>
        <a href="admin_dashboard.php" class="active">Dashboard</a>
        <a href="manage_user.php">Manage Users</a>
        <a href="manage_request.php">Manage Requests</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>Analytics Dashboard</h2>

        <!-- Display statistics in a row -->
        <div class="stat-box-container">
            <div class="stat-box" style="background-color: #28a745; color: #fff;">
                <h3>Total Requests: <?= $total_requests ?></h3>
            </div>
            <div class="stat-box" style="background-color: #ffc107; color: #fff;">
                <h3>Successful Donations: <?= $successful_donations ?></h3>
            </div>
            <div class="stat-box" style="background-color: #007bff; color: #fff;">
                <h3>Pending Requests: <?= $pending_requests ?></h3>
            </div>
            <div class="stat-box" style="background-color: #17a2b8; color: #fff;">
                <h3>Average Response Time: <?= $avg_response_time ?> hours</h3>
            </div>
        </div>

        <!-- Pending Requests List -->
        <div class="request-list">
            <h3>Pending Requests</h3>
            <?php if (mysqli_num_rows($pending_result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($pending_result)) : ?>
                    <div class="request-item">
                        <h4>Request ID: <?= $row['id'] ?></h4>
                        <span class="status status-pending">Pending</span>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No pending requests.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
