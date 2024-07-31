<?php
class RequestManager {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function deleteRequest($requestId) {
        $query = "DELETE FROM requests WHERE id = ?";
        $stmt = $this->con->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            return $stmt->affected_rows > 0; // Return true if a row was deleted
        } else {
            throw new Exception("Failed to prepare SQL statement");
        }
    }
    public function searchRequestsByDonorId($donorId) {
        $query = "SELECT * FROM requests WHERE donor_id LIKE ?";
        $stmt = $this->con->prepare($query);
        $searchTerm = '%' . $donorId . '%';
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getRequests() {
        $query = "SELECT * FROM requests";
        $result = $this->con->query($query);
        return $result;
    }
}
?>
