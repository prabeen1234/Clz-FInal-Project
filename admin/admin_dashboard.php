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

// Fetch users from the database
$query = "SELECT * FROM users"; // Adjust query if needed
$users_result = mysqli_query($con, $query);

if (!$users_result) {
    die("Database query failed: " . mysqli_error($con));
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
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        .dashboard {
            display: flex;
            width: 100%;
        }

        .side-menu {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #343a40;
            color: #fff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-size: 16px;
        }

        td {
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .actions a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        .actions a:hover {
            text-decoration: underline;
        }
    </style>
    <title>Admin Dashboard</title>
</head>
<body>

    <div class="dashboard">
        <div class="side-menu">
            <h2>Admin Menu</h2>
            <a href="admin_dashboard.php" class="active">Dashboard</a>
            <a href="manage_user.php">Manage Users</a>
            <a href="manage_request.php">Manage Request</a>
            <a href="admin_review.php">Register  Request</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>Welcome to Admin Dashboard</h2>
            </table>
        </div>
    </div>

</body>
</html>
