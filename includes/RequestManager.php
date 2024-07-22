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

    public function getRequests() {
        $query = "SELECT * FROM requests";
        $result = $this->con->query($query);
        return $result;
    }
}
?>
