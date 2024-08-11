<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
header("Location: ../login.php");
exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Adjust the path as necessary
include '../includes/Config.php';

$con = new Database();
$con = $con->getConnection();

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch user latitude and longitude
$stmt = $con->prepare("SELECT latitude, longitude FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_location = $result->fetch_assoc();

$user_lat = $user_location['latitude'];
$user_lng = $user_location['longitude'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST['request_blood'])) {
$donor_id = $_POST['donor_id'];
$blood_type = $_POST['blood_type'];

// Fetch donor's email
$stmt = $con->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

// Call Python API to send blood request
$api_url = 'http://localhost:5000/send_blood_request';
$data = array(
'user_id' => $user_id,
'donor_id' => $donor_id,
'blood_type' => $blood_type,
'latitude' => $user_lat,
'longitude' => $user_lng
);
$options = array(
'http' => array(
'header' => "Content-Type: application/json\r\n",
'method' => 'POST',
'content' => json_encode($data)
)
);
$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);
$response = json_decode($response, true);

if ($response['success']) {
$message = "Blood request sent successfully!";

// Set up PHPMailer
$mail = new PHPMailer(true);
try {
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'pubgidws@gmail.com'; // Your email address
$mail->Password = 'lcdeiryfjiseeouw'; // Your email password or app-specific password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Recipients
$mail->setFrom('noreply@yourdomain.com', 'Blood Donation System');
$mail->addAddress($donor['email']); // Donor's email address

// Content
$mail->isHTML(true);
$mail->Subject = 'Blood Request Notification';
$mail->Body = '<p>Dear Donor,</p><p>You have received a new blood request. Please log in to your dashboard to view the details and respond.</p><p>Thank you for your generosity.</p>';

$mail->send();
$message .= " An email notification has been sent to the donor.";
} catch (Exception $e) {
$message .= " Failed to send email notification. Mailer Error: {$mail->ErrorInfo}";
}
} else {
$message = $response['message'];
}
}
}

if (isset($_POST['search'])) {
$blood_type = $_POST['blood_type'];

// Call Python API to search for donors
$api_url = 'http://localhost:5000/search_donors';
$data = array(
'blood_type' => $blood_type,
'user_lat' => $user_lat,
'user_lng' => $user_lng,
'k' => 5
);
$options = array(
'http' => array(
'header' => "Content-Type: application/json\r\n",
'method' => 'POST',
'content' => json_encode($data)
)
);
$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);
$donors_result = json_decode($response, true);
} else {
$donors_result = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Blood</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
        }
        .dashboard {
            display: flex;
            height: 100vh;
        }
        .side-menu {
            width: 250px;
            background: linear-gradient(135deg, #3a3a3a, #222);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
        }
        .side-menu h2 {
            font-size: 26px;
            margin-bottom: 20px;
        }
        .side-menu a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            width: 80%;
            text-align: center;
            transition: background 0.3s ease;
        }
        .side-menu a:hover,
        .side-menu a.active {
            background-color: #007bff;
        }
        .side-menu a.logout-btn {
            background-color: #f44336;
        }
        .dashboard-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #f0f2f5;
            overflow-y: auto;
        }
        .dashboard-content h2 {
            color: #444;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }
        .message {
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group select, .form-group input[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .request-table th, .request-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .request-table th {
            background-color: #f8f9fa;
        }
        .actions form {
            display: inline-block;
        }
        .actions button,
        .actions input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .actions button:hover,
        .actions input[type="submit"]:hover {
            background-color: #218838;
        }
        .donor-details {
            display: none;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .donor-details p {
            margin: 5px 0;
        }
    </style>
    <script>
        function showDonorDetails(id) {
            var details = document.getElementById('donor-details-' + id);
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="dashboard">
        <div class="side-menu">
            <h2>Dashboard</h2>
            <a href="user_dashboard.php">Dashboard</a>
            <a href="search_blood.php" class="active">Search Blood</a>
            <a href="request_list.php">View Requests</a>
            <a href="update_profile.php">Update Profile</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-content">
            <h2>Search for Blood Donors</h2>
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form action="search_blood.php" method="post">
                    <div class="form-group">
                        <label for="blood_type">Blood Type:</label>
                        <select id="blood_type" name="blood_type" required>
                            <option value="">Select Blood Type</option>
                            <option value="Apos">A+</option>
                            <option value="Aneg">A-</option>
                            <option value="Bpos">B+</option>
                            <option value="Bneg">B-</option>
                            <option value="Opos">O+</option>
                            <option value="Oneg">O-</option>
                            <option value="ABpos">AB+</option>
                            <option value="ABneg">AB-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="search" value="Search Donors">
                    </div>
                </form>

                <?php if (!empty($donors_result)): ?>
                    <h3>Available Donors</h3>
                    <table class="request-table">
                        <thead>
                            <tr>
                                <th>Donor ID</th>
                                <th>Blood Type</th>
                                <th>Full Name</th>
                                <th>Distance (km)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donors_result as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['blood_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo number_format($row['distance'], 2); ?></td>
                                    <td class="actions">
                                        <button onclick="showDonorDetails(<?php echo $row['id']; ?>)">View Details</button>
                                        <div id="donor-details-<?php echo $row['id']; ?>" class="donor-details">
                                            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($row['fullname']); ?></p>
                                            <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
                                            <p><strong>Weight:</strong> <?php echo number_format($row['weight'], 2); ?> kg</p>
                                            <form action="search_blood.php" method="post">
                                                <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                <input type="hidden" name="blood_type" value="<?php echo htmlspecialchars($row['blood_type']); ?>">
                                                <input type="submit" name="request_blood" value="Request Blood">
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No donors found for the given criteria.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
