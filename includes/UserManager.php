<?php
class UserManager {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function deleteUser($id) {
        // Delete related requests first
        $stmt = $this->con->prepare("DELETE FROM requests WHERE donor_id = ? OR user_id = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $stmt->close();

        // Now delete the user
        $stmt = $this->con->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchUsersByMobile($mobile) {
        $query = "SELECT * FROM users WHERE mobile LIKE ?";
        $stmt = $this->con->prepare($query);
        $searchTerm = '%' . $mobile . '%';
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUsers() {
        $query = "SELECT * FROM users";
        $result = $this->con->query($query);
        return $result;
    }
    public function getUserById($id) {
        $stmt = $this->con->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUserStatus($id, $status) {
        $stmt = $this->con->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}
?>
