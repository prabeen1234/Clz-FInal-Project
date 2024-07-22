<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/Config.php';
$db = new Database();
$con = $db->getConnection();

$user_id = $_SESSION['user_id'];

// Fetch user requests
$requests_query = $con->prepare("SELECT * FROM requests WHERE user_id = ?");
$requests_query->bind_param("i", $user_id);
$requests_query->execute();
$requests_result = $requests_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests</title>
    <style>
        /* General Styles */
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
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: width 0.3s ease;
        }

        .side-menu h2 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
            font-size: 16px;
        }

        .side-menu a.active, .side-menu a:hover {
            background-color: #007bff;
            padding-left: 20px;
        }

        .side-menu a.logout-btn {
            background-color: #dc3545;
        }

        .side-menu a.logout-btn:hover {
            background-color: #c82333;
        }

        /* Dashboard Content Styles */
        .dashboard-content {
            flex: 1;
            margin-left: 250px; /* Adjust for sidebar width */
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: auto;
            transition: margin-left 0.3s ease;
        }

        .dashboard-content h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        /* Table Styles */
        .request-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }

        .request-table thead {
            background-color: #343a40;
            color: #fff;
        }

        .request-table th, .request-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .request-table th {
            font-size: 16px;
            font-weight: 600;
        }

        .request-table td {
            font-size: 14px;
        }

        .request-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Message Styles */
        #message-container {
            margin-bottom: 20px;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 500;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .dashboard-content {
                margin-left: 0;
                padding: 15px;
            }

            .side-menu {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
                display: block;
            }

            .side-menu a {
                display: inline-block;
                margin: 5px 0;
            }
        }

        @media (max-width: 768px) {
            .side-menu {
                width: 200px;
            }

            .dashboard-content {
                margin-left: 200px; /* Adjust for updated sidebar width */
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
                margin-left: 0;
                padding: 10px;
            }

            .request-table th, .request-table td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>User Menu</h2>
            <a href="user_dashboard.php">Dashboard</a>
            <a href="request_list.php" class="active">My Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>My Requests</h2>
            <div id="message-container">
                <!-- Message will be displayed here -->
            </div>
            <table class="request-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Donor ID</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $requests_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['donor_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
