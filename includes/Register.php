<?php
class Register {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    // Private methods for checking registration
    private function checkPhone($phone) {
        $query = "SELECT id FROM users WHERE mobile = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    private function checkEmail($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Public methods to check if phone or email is registered
    public function isPhoneRegistered($phone) {
        return $this->checkPhone($phone);
    }

    public function isEmailRegistered($email) {
        return $this->checkEmail($email);
    }

    public function handleRegistration($data) {
        // Validate input
        if (
            isset($data['password'], $data['fullname'], $data['age'], $data['sex'],
                  $data['blood_type'], $data['mobile'], $data['email'], $data['weight'],
                  $data['state'], $data['latitude'], $data['longitude'], $data['role']) &&
            !empty($data['password']) && !empty($data['fullname']) &&
            !empty($data['age']) && !empty($data['sex']) && !empty($data['blood_type']) &&
            !empty($data['mobile']) && !empty($data['email']) && !empty($data['weight']) &&
            !empty($data['state']) && !empty($data['latitude']) && !empty($data['longitude']) && !empty($data['role'])
        ) {
            // Sanitize input data
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $fullname = $this->con->real_escape_string($data['fullname']);
            $age = (int)$data['age'];
            $sex = $this->con->real_escape_string($data['sex']);
            $blood_type = $this->con->real_escape_string($data['blood_type']);
            $mobile = $this->con->real_escape_string($data['mobile']);
            $email = $this->con->real_escape_string($data['email']);
            $weight = $this->con->real_escape_string($data['weight']);
            $state = $this->con->real_escape_string($data['state']);
            $latitude = (float)$data['latitude'];
            $longitude = (float)$data['longitude'];
            $role = $this->con->real_escape_string($data['role']);

            // Set account status based on role
            $status = ($role === 'donor') ? 'pending' : 'approved';

            // Check if phone number already exists
            if ($this->isPhoneRegistered($mobile)) {
                echo '<script>
                        alert("Mobile number already registered");
                        window.location.href = "../pages/register.php";
                      </script>';
                return;
            }

            // Check if email already exists
            if ($this->isEmailRegistered($email)) {
                echo '<script>
                        alert("Email is already registered");
                        window.location.href = "../pages/register.php";
                      </script>';
                return;
            }

            // Prepare SQL statement
            $query = "INSERT INTO users (password, fullname, age, sex, blood_type, mobile, email, weight, state, latitude, longitude, role, status)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ssissssssdsss", $password, $fullname, $age, $sex, $blood_type, $mobile, $email, $weight, $state, $latitude, $longitude, $role, $status);

            if ($stmt->execute()) {
                // Registration successful
                echo '<script>
                        alert("Registration successful. Your account is ' . $status . '.");
                        window.location.href = "../index.php";
                      </script>';
            } else {
                // Registration failed
                echo '<script>
                        alert("Registration failed: ' . htmlspecialchars($this->con->error, ENT_QUOTES, 'UTF-8') . '");
                        window.location.href = "../pages/register.php";
                      </script>';
            }

            $stmt->close();
        } else {
            // Missing or empty fields
            echo '<script>
                    alert("Please fill in all required fields.");
                    window.location.href = "../pages/register.php";
                  </script>';
        }
    }
}
?>
