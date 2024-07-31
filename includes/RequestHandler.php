<?php
class RequestHandler {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest($user_id, $donor_id, $user_blood_type) {
        // Check if a request already exists
        $stmt = $this->conn->prepare("SELECT * FROM requests WHERE user_id = ? AND donor_id = ?");
        $stmt->bind_param("ii", $user_id, $donor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Request already exists
        }

        $stmt = $this->conn->prepare("INSERT INTO requests (user_id, donor_id, user_blood_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $donor_id, $user_blood_type);
        $stmt->execute();
        return true;
    }

    public function getUserLocation($user_id) {
        $stmt = $this->conn->prepare("SELECT latitude, longitude FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getDonors() {
        $stmt = $this->conn->prepare("SELECT id, fullname, age,weight, blood_type, latitude, longitude FROM users WHERE role = 'donor'");
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
