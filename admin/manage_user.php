<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/Config.php';
require '../includes/UserManager.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Initialize database and user manager
$db = new Database();
$con = $db->getConnection();
$userManager = new UserManager($con);

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $userId = $_POST['id'];
    if ($userManager->deleteUser($userId)) {
        echo "<script>alert('User deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete user');</script>";
    }
}

// Handle user search by mobile number
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';

if (!empty($mobile)) {
    $users_result = $userManager->searchUsersByMobile($mobile);
} else {
    $users_result = $userManager->getUsers();
}

if (!$users_result) {
    die("Database query failed: " . $con->error);
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
            transition: background-color 0.3s ease;
        }

        .side-menu a.active {
            background-color: #3498db;
        }

        .side-menu a:hover {
            background-color: #2980b9;
        }

        .side-menu a.logout-btn {
            background-color: #e74c3c;
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

        form {
            margin-bottom: 20px;
        }

        form input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #2980b9;
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

        .actions .delete-btn {
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border: none;
            background: #e74c3c;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
    <title>Manage Users</title>
    <script>
        function confirmDeletion(event) {
            if (!confirm("Are you sure you want to delete this user?")) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>

    <div class="dashboard">
        <div class="side-menu">
            <h2>Admin Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
           
            <a href="manage_user.php" class="active">Manage Users</a>
             <a href="admin_review.php">Register  Request</a>
            <a href="manage_request.php" >Manage Request</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>User Management</h2>
            <form method="GET" action="manage_user.php">
                <input type="text" name="mobile" placeholder="Search by Mobile Number" value="<?php echo isset($_GET['mobile']) ? htmlspecialchars($_GET['mobile']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Email</th>
                        <th>Blood Group</th>
                        <th>Role</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['age']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['blood_type']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                            <td class="actions">
                                <form action="manage_user.php" method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" name="delete" class="delete-btn" onclick="confirmDeletion(event)">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
