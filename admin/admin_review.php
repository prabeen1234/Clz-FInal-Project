<?php
session_start();
require '../includes/Config.php'; 

if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Initialize the Database class
$db = new Database();
$con = $db->getConnection();

function updateUserStatus($con, $userId, $status) {
    $query = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($con->error));
    }
    $stmt->bind_param("si", $status, $userId);
    $stmt->execute();
    $stmt->close();
}

// Function to get pending users
function getPendingUsers($con) {
    $query = "SELECT id, fullname, email, mobile FROM users WHERE status = 'pending'";
    $result = $con->query($query);
    if ($result === false) {
        die('Query failed: ' . htmlspecialchars($con->error));
    }
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve']) || isset($_POST['reject'])) {
        $userId = $_POST['user_id'];
        $status = isset($_POST['approve']) ? 'approved' : 'rejected';
        updateUserStatus($con, $userId, $status);
    }
}

// Fetch pending users
$pendingUsers = getPendingUsers($con);

// Close the database connection
$db->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <style>
        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            flex-direction: column;
        }

        .dashboard {
            display: flex;
            flex: 1;
        }

        .side-menu {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .side-menu h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .side-menu a.active {
            background-color: #3498db;
            font-weight: bold;
        }

        .side-menu a:hover {
            background-color: #2980b9;
            color: #fff;
        }

        .side-menu a.logout-btn {
            background-color: #e74c3c;
            font-weight: bold;
        }

        .side-menu a.logout-btn:hover {
            background-color: #c0392b;
        }

        .dashboard-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            overflow: auto;
            border-left: 1px solid #ddd;
        }

        .dashboard-content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #3498db;
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

        form {
            display: inline;
        }

        /* Button styles */
        button, .view-details {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 14px;
            text-decoration: none; /* Remove underline for anchor tag */
            display: inline-block; /* Ensure proper spacing and alignment */
        }

        button:hover, .view-details:hover {
            background-color: #2980b9;
        }

        button:active, .view-details:active {
            background-color: #1f6f9f;
            transform: scale(0.98); /* Slightly shrink button on click */
        }

        .view-details {
            background-color: #2ecc71; /* Unique color for View Details button */
        }

        .view-details:hover {
            background-color: #27ae60;
        }

        .view-details:active {
            background-color: #1e8449;
        }
    </style>
    <title>Manage Requests</title>
</head>
<body>

    <div class="dashboard">
        <div class="side-menu">
            <h2>Admin Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_user.php">Manage Users</a>
            <a href="admin_review.php" class="active">Register Request</a>
            <a href="manage_request.php">Manage Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h1>Pending Donor Registrations</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendingUsers)): ?>
                        <?php foreach ($pendingUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                <td>
                                    <a href="user_details.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="view-details">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No pending registrations</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
