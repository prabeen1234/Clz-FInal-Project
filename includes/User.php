<?php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function searchDonors($blood_type, $user_lat, $user_lng, $k) {
        $compatibleBloodTypes = $this->getCompatibleBloodTypes($blood_type);

        $placeholders = implode(',', array_fill(0, count($compatibleBloodTypes), '?'));
        $query = "SELECT id, fullname, blood_type, age, weight, latitude, longitude FROM users WHERE role = 'donor' AND blood_type IN ($placeholders)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($compatibleBloodTypes)), ...$compatibleBloodTypes);
        $stmt->execute();
        $result = $stmt->get_result();

        $donors = [];
        while ($row = $result->fetch_assoc()) {
            $distance = $this->haversine($user_lat, $user_lng, $row['latitude'], $row['longitude']);
            $row['distance'] = $distance;
            $donors[] = $row;
        }

        usort($donors, function($a, $b) {
            return $a['distance'] - $b['distance'];
        });

        return array_slice($donors, 0, $k);
    }

    private function getCompatibleBloodTypes($blood_type) {
        $compatibility = [
            'Apos' => ['Apos', 'Aneg', 'Opos', 'Oneg'],
            'Aneg' => ['Aneg', 'Oneg'],
            'Bpos' => ['Bpos', 'Bneg', 'Opos', 'Oneg'],
            'Bneg' => ['Bneg', 'Oneg'],
            'ABpos' => ['Apos', 'Aneg', 'Bpos', 'Bneg', 'ABpos', 'ABneg', 'Opos', 'Oneg'],
            'ABneg' => ['Aneg', 'Bneg', 'ABneg', 'Oneg'],
            'Opos' => ['Opos', 'Oneg'],
            'Oneg' => ['Oneg']
        ];

        return $compatibility[$blood_type] ?? [];
    }

    private function haversine($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earth_radius * $c;
    }

    public function sendBloodRequest($userId, $donorId, $bloodType, $latitude, $longitude) {
        $query = "INSERT INTO requests (user_id, donor_id, blood_type, latitude, longitude, status) 
                  VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }
        if ($this->hasPendingRequest($userId, $donorId)) {
            return false;
        }

        $stmt->bind_param('iissd', $userId, $donorId, $bloodType, $latitude, $longitude);

        if ($stmt->execute()) {
            return true;
        } else {
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        }
    }

    private function hasPendingRequest($user_id, $donor_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM requests WHERE user_id = ? AND donor_id = ? AND status = 'Pending'");
        $stmt->bind_param("ii", $user_id, $donor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>