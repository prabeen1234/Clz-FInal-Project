<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/Config.php';
$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];

// Handle request deletion
if (isset($_POST['delete_request_id'])) {
    $request_id = $_POST['delete_request_id'];
    // Check if the request status is Pending before deleting
    $check_status_query = $conn->prepare("SELECT status FROM requests WHERE id = ? AND user_id = ?");
    $check_status_query->bind_param("ii", $request_id, $user_id);
    $check_status_query->execute();
    $status_result = $check_status_query->get_result();
    if ($status_result->num_rows > 0) {
        $status_row = $status_result->fetch_assoc();
        if ($status_row['status'] === 'Pending') {
            $delete_query = $conn->prepare("DELETE FROM requests WHERE id = ? AND user_id = ?");
            $delete_query->bind_param("ii", $request_id, $user_id);
            $delete_query->execute();
        }
    }
    header("Location: request_list.php");
    exit();
}

// Fetch user requests
$requests_query = $conn->prepare("SELECT * FROM requests WHERE user_id = ?");
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
        .request-table .view-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    text-align: center;
    display: inline-block;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.request-table .view-btn:hover {
    background-color: #0056b3;
}

.request-table .view-btn:active {
    background-color: #004085;
}

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

        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .request-table thead {
            background-color: #343a40;
            color: #fff;
        }

        .request-table th, .request-table td {
            padding: 12px;
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

        .request-table .status-pending {
            background-color: #ffc107;
            color: #333;
        }

        .request-table .status-declined {
            background-color: #dc3545;
            color: #fff;
        }

        .request-table .status-accepted {
            background-color: #28a745;
            color: #fff;
        }

        .request-table .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .request-table .delete-btn:hover {
            background-color: #c82333;
        }

        @media (max-width: 768px) {
            .side-menu {
                width: 200px;
            }
            .dashboard-content {
                margin-left: 200px;
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
    <script>
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to Cancel this request?')) {
                event.preventDefault(); // Prevent the form from submitting
            }
        }
    </script>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>User Menu</h2>
            <a href="user_dashboard.php">Dashboard</a>
            <a href="search_blood.php">Search Blood</a>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $requests_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['donor_id']); ?></td>
                            <td class="<?php echo 'status-' . strtolower(htmlspecialchars($row['status'])); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pending'): ?>
                                    <form method="post" action="" onsubmit="confirmDelete(event)">
                                        <input type="hidden" name="delete_request_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="delete-btn">Cancel</button>
                                    </form>
                                <?php elseif ($row['status'] === 'Accepted'): ?>
                                    <a href="view_donor.php?request_id=<?php echo htmlspecialchars($row['id']); ?>&user_id=<?php echo htmlspecialchars($row['donor_id']); ?>" class="view-btn">View Details</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
