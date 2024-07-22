<?php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function searchDonors($blood_type, $latitude, $longitude) {
        $query = "SELECT id, fullname, bloodgroup, latitude, longitude 
                  FROM users 
                  WHERE role = 'donor' AND bloodgroup = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $blood_type);
        $stmt->execute();
        $result = $stmt->get_result();

        $donors = [];
        while ($row = $result->fetch_assoc()) {
            $row['distance'] = $this->calculateDistance($latitude, $longitude, $row['latitude'], $row['longitude']);
            $donors[] = $row;
        }

        usort($donors, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $donors;
    }

    public function sendBloodRequest($user_id, $donor_id, $blood_type) {
        $query = "INSERT INTO requests (user_id, donor_id, status) VALUES (?, ?, 'Pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $donor_id);
        return $stmt->execute();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371; // Earth radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c;
    }
}
?>
