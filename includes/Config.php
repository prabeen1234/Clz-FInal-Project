<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "prabin@123";
    private $database = "blood";
    private $con;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        // Create connection
        $this->con = new mysqli($this->servername, $this->username, $this->password, $this->database);

        // Check connection
        if ($this->con->connect_error) {
            die("Database connection failed: " . $this->con->connect_error);
        }
    }

    public function getConnection() {
        return $this->con;
    }

    public function close() {
        $this->con->close();
    }
}
?>
