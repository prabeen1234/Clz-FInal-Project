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
            $update_query = $con->prepare("UPDATE requests SET status = 'Rejected', donor_id = ? WHERE id = ?");
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

// Fetch blood requests
$requests_query = "SELECT * FROM requests WHERE status = 'Pending'";
$requests_result = $con->query($requests_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Blood Requests</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
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

        .request-table td.actions form input[type="submit"]:nth-child(2) {
            background-color: #dc3545;
        }

        .request-table td.actions form input[type="submit"]:nth-child(2):hover {
            background-color: #c82333;
        }

        @media (max-width: 768px) {
            .side-menu {
                width: 100%;
                height: auto;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .side-menu a {
                flex: 1 1 100%;
                text-align: center;
            }

            .dashboard-content {
                padding: 10px;
            }

            .form-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Donor Menu</h2>
            <a href="donor_dashboard.php">Dashboard</a>
            <a href="update_profile.php">Update Profile</a>
            <a href="request_blood.php" class="active">View Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <div class="form-container">
                <?php if (isset($response)): ?>
                    <div class="message <?php echo $response['status']; ?>">
                        <?php echo $response['message']; ?>
                    </div>
                <?php endif; ?>
                <h2>Pending Blood Requests</h2>
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Requester ID</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $requests_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td class="actions">
                                    <form method="POST">
                                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <input type="submit" name="action" value="Accept">
                                        <input type="submit" name="action" value="Reject">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
