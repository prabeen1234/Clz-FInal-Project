<?php
use App\Services\EmailService;
session_start();
require '../includes/Config.php'; // Ensure this includes Database.php and initializes $db
require '../includes/Admin.php';
require '../vendor/autoload.php';
require '../includes/EmailService.php'; 

if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

$db = new Database();
$con = $db->getConnection();

$admin = new Admin($con);
$emailService = new EmailService();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve']) || isset($_POST['reject'])) {
        $userId = $_POST['user_id'];
        $status = isset($_POST['approve']) ? 'approved' : 'rejected';
        $userEmail = $admin->getUserEmail($userId);

        try {
            if ($status == 'rejected') {
                $admin->deleteUser($userId);
                $emailService->sendOtpEmail($userEmail, 'Your Registration has been Rejected... Try Again');
                echo '<script>
                alert("User rejected and email notification sent.");
                window.location.href = "admin_review.php";
                </script>';
            } else {
                $admin->updateUserStatus($userId, $status);
                $emailService->sendOtpEmail($userEmail, 'Your Registration Request has been Accepted.');
                echo '<script>
                alert("User approved and email notification sent.");
                window.location.href = "admin_review.php";
                </script>';
            }
        } catch (Exception $e) {
            echo '<script>
            alert("Error sending email: ' . htmlspecialchars($e->getMessage()) . '");
            window.location.href = "admin_review.php";
            </script>';
        }
        exit();
    }
}

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = $admin->getUserDetails($userId);
$userImageBlob = $admin->getUserImage($userId);

$user = array_merge([
    'fullname' => '',
    'email' => '',
    'blood_type' => '',
    'role' => '',
    'mobile' => '',
    'sex' => '',
    'age' => '',
    'weight' => '',
    'latitude' => '',
    'longitude' => ''
], $user);

$db->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <!-- Lightbox2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <style>
        /* General Body and Dashboard Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
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
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .side-menu h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .side-menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .side-menu a.active, .side-menu a:hover {
            background-color: #0056b3;
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

        /* Form Container Styling */
        .form-container {
            max-width: 800px;
            width: 100%;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #28a745;
        }

        .message.error {
            background-color: #dc3545;
        }

        /* Form Group Styling */
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
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f8f9fa;
        }

        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 15px;
            border-radius: 5px;
        }

        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Image Styling */
        .form-group img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Form Actions Styling */
        .form-actions {
            display: flex;
            justify-content: space-between;
        }

        .form-actions input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .form-actions input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-actions input[name="reject"] {
            background-color: #dc3545;
        }

        .form-actions input[name="reject"]:hover {
            background-color: #c82333;
        }
    </style>
    <title>User Details</title>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Admin Menu</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_user.php">Manage Users</a>
            <a href="manage_request.php">Manage Requests</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="dashboard-content">
            <h2>User Details</h2>
            <div class="form-container">
                <?php if (isset($message)): ?>
                    <div class="message <?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form action="user_details.php" method="post">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['fullname']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="blood">Blood Group:</label>
                        <input type="text" id="blood_type" name="blood_type" value="<?php echo htmlspecialchars($user['blood_type']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['mobile']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="sex">Sex:</label>
                        <input type="text" id="sex" name="sex" value="<?php echo htmlspecialchars($user['sex']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="text" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight:</label>
                        <input type="text" id="weight" name="weight" value="<?php echo htmlspecialchars($user['weight']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($user['latitude']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($user['longitude']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="image">Id Image:</label>
                        <?php if ($userImageBlob): ?>
                            <a href="data:image/jpeg;base64,<?php echo base64_encode($userImageBlob); ?>" data-lightbox="user-image" data-title="User Image">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($userImageBlob); ?>" alt="User Image">
                            </a>
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-actions">
                        <input type="submit" name="approve" value="Approve">
                        <input type="submit" name="reject" value="Reject">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Lightbox2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
</body>
</html>
