
<?php
use App\Services\EmailService;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/Config.php'; // Ensure this includes Database.php and initializes $db
require '../includes/Admin.php';
require '../vendor/autoload.php';
require '../includes/EmailService.php'; 

if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Initialize the Database class
$db = new Database();
$con = $db->getConnection();

// Initialize the Admin class
$admin = new Admin($con);

// Initialize the EmailService class
$emailService = new EmailService();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve']) || isset($_POST['reject'])) {
        $userId = $_POST['user_id'];
        $status = isset($_POST['approve']) ? 'approved' : 'rejected';
        $userEmail = $admin->getUserEmail($userId); // Assuming you have a method to get the user email

        try {
            if ($status == 'rejected') {
                // Delete the user if rejected
                $admin->deleteUser($userId);
                $emailService->sendOtpEmail($userEmail, 'Your Registration has been Rejected... Try Again'); // Adjust method as necessary
                echo '<script>
                alert("User rejected and email notification sent.");
                window.location.href = "manage_request.php";
                </script>';
            } else {
                // Update the user status if approved
                $admin->updateUserStatus($userId, $status);
                $emailService->sendOtpEmail($userEmail, 'Your Request has been Accepted.'); // Adjust method as necessary
                echo '<script>
                alert("User approved and email notification sent.");
                window.location.href = "manage_request.php";
                </script>';
            }
        } catch (Exception $e) {
            echo '<script>
            alert("Error sending email: ' . htmlspecialchars($e->getMessage()) . '");
            window.location.href = "manage_request.php";
            </script>';
        }
        exit();
    }
}

// Get user ID from query parameter
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = $admin->getUserDetails($userId);

// Ensure default values for potentially unset fields
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
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
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

        .dashboard-content h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            max-width: 650px;
            width: 100%;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
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
            border: 1px solid #ced4da;
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

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 5px;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }

        .button-container button.reject {
            background-color: #dc3545;
        }

        .button-container button.reject:hover {
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
                        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight:</label>
                        <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($user['weight']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($user['latitude']); ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($user['longitude']); ?>" required disabled>
                    </div>
                    <div id="map"></div>
                    <div class="button-container">
                        <button type="submit" name="approve">Approve</button>
                        <button type="submit" name="reject" class="reject">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPYMI9P4d29sp8AGl_4z9py1ZEt8YXmcI&callback=initMap" async defer></script>
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: {lat: <?php echo htmlspecialchars($user['latitude']); ?>, lng: <?php echo htmlspecialchars($user['longitude']); ?>}
            });

            const marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: {lat: <?php echo htmlspecialchars($user['latitude']); ?>, lng: <?php echo htmlspecialchars($user['longitude']); ?>}
            });
        }
    </script>
</body>
</html>
