<?php
class SessionManager {
    public function __construct() {
        session_start();
    }

    public function checkUserSession() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            header("Location: ../login.php");
            exit();
        }
    }

    public function getUserId() {
        return $_SESSION['user_id'];
    }
}
?>
