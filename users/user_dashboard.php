<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
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
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .dashboard {
            display: flex;
            height: 100vh;
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
            font-size: 24px;
            margin-bottom: 20px;
            color: #ffc107;
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
    </style>
    <title>User Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>User Dashboard</h2>
            <a href="search_blood.php" class="active">Search Blood</a>
            <a href="request_list.php">View Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>Welcome to Your Dashboard</h2>
        </div>
    </div>
</body>
</html>
