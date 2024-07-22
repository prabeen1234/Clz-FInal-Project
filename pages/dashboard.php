<?php
session_start();
require '../includes/Config.php';

// Check if user is logged in and get their role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Determine sidebar items based on role
$sidebarItems = [];
if ($role === 'Donor') {
    $sidebarItems = [
        'Requests' => 'requests.php',
        'Your Info' => 'your_info.php',
        'Logout' => 'logout.php'
    ];
} elseif ($role === 'User') {
    $sidebarItems = [
        'Find Blood' => 'find_blood.php',
        'Your Info' => 'your_info.php',
        'Your Requests' => 'your_requests.php',
        'Logout' => 'logout.php'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #007bff;
            color: #fff;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin: 0;
            padding: 10px;
            background-color: #0056b3;
            font-size: 24px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #0056b3;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            font-size: 18px;
        }

        .sidebar ul li a:hover {
            background-color: #0056b3;
            border-radius: 4px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .header {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            font-size: 20px;
            text-align: center;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h3 {
            margin-top: 0;
            color: #007bff;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <?php foreach ($sidebarItems as $name => $link): ?>
                <li><a href="<?php echo $link; ?>"><?php echo $name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            Welcome, <?php echo htmlspecialchars($username); ?>!
        </div>
        <div class="card">
            <h3>Dashboard Content</h3>
            <p>This is where the content specific to the dashboard will be displayed.</p>
        </div>
    </div>
</body>
</html>
