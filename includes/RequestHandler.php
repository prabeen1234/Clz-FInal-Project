<?php
class RequestHandler {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function addRequest($user_id, $donor_id) {
        $stmt = $this->con->prepare("INSERT INTO requests (user_id, donor_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $donor_id);
        $stmt->execute();
    }

    public function getUserLocation($user_id) {
        $stmt = $this->con->prepare("SELECT latitude, longitude FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getDonors() {
        $stmt = $this->con->prepare("SELECT id, fullname, age, bloodgroup, latitude, longitude FROM users WHERE role = 'donor'");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function haversine_distance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371; // in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c;
    }

    public function knn($donors, $user_lat, $user_lng, $k) {
        $distances = [];
        foreach ($donors as $donor) {
            $distance = $this->haversine_distance($user_lat, $user_lng, $donor['latitude'], $donor['longitude']);
            $distances[] = [
                'id' => $donor['id'],
                'fullname' => $donor['fullname'],
                'age' => $donor['age'],
                'bloodgroup' => $donor['bloodgroup'],
                'distance' => $distance
            ];
        }
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        return array_slice($distances, 0, $k);
    }

    
}
?>
