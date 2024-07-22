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
            $update_query = $con->prepare("UPDATE requests SET status = 'Rejected' WHERE id = ?");
            $update_query->bind_param("i", $request_id);
        } else {
            $response = array("status" => "error", "message" => "Invalid action: '$action'.");
            echo json_encode($response);
            exit();
        }

        if ($update_query->execute()) {
            echo '<script>
            alert("Updated successful");
            window.location.href = "../donors/request_blood.php";
            </script>';
        } else {
            $response = array("status" => "error", "message" => "Failed to update request.");
        }
        echo json_encode($response);
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

        .request-table td.actions {
            display: flex;
            align-items: center;
        }

        .request-table td.actions form {
            display: flex;
            gap: 10px;
        }

        .request-table td.actions input[type="submit"] {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .request-table td.actions .accept {
            background-color: #28a745;
        }

        .request-table td.actions .accept:hover {
            background-color: #218838;
        }

        .request-table td.actions .reject {
            background-color: #dc3545;
        }

        .request-table td.actions .reject:hover {
            background-color: #c82333;
        }

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

            .request-table td.actions input[type="submit"] {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Donor Dashboard</h2>
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
                            <th>Requester Id</th>
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
                                        <input type="submit" name="action" value="Accept" class="accept">
                                        <input type="submit" name="action" value="Reject" class="reject">
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
