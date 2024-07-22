<?php
class UserManager {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function deleteUser($userId) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->con->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            return $stmt->affected_rows > 0; // Return true if a row was deleted
        } else {
            throw new Exception("Failed to prepare SQL statement");
        }
    }

    public function getUsers() {
        $query = "SELECT * FROM users";
        $result = $this->con->query($query);
        return $result;
    }
}
?>
