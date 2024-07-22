<?php
session_start();
require '../includes/Config.php';
require 'RequestHandler.php';

$requestHandler = new RequestHandler($con);
$user_id = $_SESSION['user_id'];

// Handle request action
if (isset($_POST['request_donor_id'])) {
    $donor_id = $_POST['request_donor_id'];
    $requestHandler->addRequest($user_id, $donor_id);
    header("Location: find_donors.php");
    exit();
}

$user_location = $requestHandler->getUserLocation($user_id);
$user_lat = $user_location['latitude'];
$user_lng = $user_location['longitude'];

// Get user's blood group
$stmt = $con->prepare("SELECT bloodgroup FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_bloodgroup = $result->fetch_assoc()['bloodgroup'];

$donors_result = $requestHandler->getDonors();

// Filter compatible donors and calculate distances
$compatible_donors = [];
while ($donor = $donors_result->fetch_assoc()) {
    if ($requestHandler->is_blood_compatible($user_bloodgroup, $donor['bloodgroup'])) {
        $compatible_donors[] = $donor;
    }
}

$k = 5;
$nearest_donors = $requestHandler->knn($compatible_donors, $user_lat, $user_lng, $k);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Donors</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8b500, #f6d365);
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .donor-list {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .donor-list h2 {
            margin-top: 0;
        }
        .donor-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .donor-item:last-child {
            border-bottom: none;
        }
        .donor-item .label {
            font-weight: bold;
        }
        .donor-item .value {
            margin-left: 10px;
        }
        .donor-item form {
            margin-top: 10px;
        }
        .donor-item button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #4CAF50;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }
        .donor-item button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="donor-list">
            <h2>Nearest Donors</h2>
            <?php if (empty($nearest_donors)) { ?>
                <p>No donors found.</p>
            <?php } else { ?>
                <?php foreach ($nearest_donors as $donor) { ?>
                    <div class="donor-item">
                        <div>
                            <p><span class="label">Donor ID:</span> <span class="value"><?php echo htmlspecialchars($donor['id']); ?></span></p>
                            <p><span class="label">Full Name:</span> <span class="value"><?php echo htmlspecialchars($donor['fullname']); ?></span></p>
                            <p><span class="label">Age:</span> <span class="value"><?php echo htmlspecialchars($donor['age']); ?></span></p>
                            <p><span class="label">Blood Group:</span> <span class="value"><?php echo htmlspecialchars($donor['bloodgroup']); ?></span></p>
                            <p><span class="label">Distance:</span> <span class="value"><?php echo number_format($donor['distance'], 2); ?> km</span></p>
                        </div>
                        <form method="post" action="">
                            <input type="hidden" name="request_donor_id" value="<?php echo htmlspecialchars($donor['id']); ?>">
                            <button type="submit">Send Request</button>
                        </form>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

</body>
</html>
