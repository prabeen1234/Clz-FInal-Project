<?php
class Register {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

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

    public function isPhoneRegistered($phone) {
        return $this->checkPhone($phone);
    }

    public function isEmailRegistered($email) {
        return $this->checkEmail($email);
    }

    public function handleRegistration($data, $imageBlob) {
        if (
            isset($data['password'], $data['fullname'], $data['age'], $data['sex'],
                  $data['blood_type'], $data['mobile'], $data['email'], $data['weight'],
                  $data['state'], $data['latitude'], $data['longitude'], $data['role']) &&
            !empty($data['password']) && !empty($data['fullname']) &&
            !empty($data['age']) && !empty($data['sex']) && !empty($data['blood_type']) &&
            !empty($data['mobile']) && !empty($data['email']) && !empty($data['weight']) &&
            !empty($data['state']) && !empty($data['latitude']) && !empty($data['longitude']) && !empty($data['role'])
        ) {
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

            $status = ($role === 'donor') ? 'pending' : 'approved';

            if ($this->isPhoneRegistered($mobile)) {
                echo '<script>
                        alert("Mobile number already registered");
                        window.location.href = "../pages/register.php";
                      </script>';
                return;
            }

            if ($this->isEmailRegistered($email)) {
                echo '<script>
                        alert("Email is already registered");
                        window.location.href = "../pages/register.php";
                      </script>';
                return;
            }

            $query = "INSERT INTO users (password, fullname, age, sex, blood_type, mobile, email, weight, state, latitude, longitude, role, status, imageBlob)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ssissssssdssss", $password, $fullname, $age, $sex, $blood_type, $mobile, $email, $weight, $state, $latitude, $longitude, $role, $status, $imageBlob);

            if ($stmt->execute()) {
                echo '<script>
                        alert("Registration successful. Your account is ' . $status . '.");
                        window.location.href = "../index.php";
                      </script>';
            } else {
                echo '<script>
                        alert("Registration failed: ' . htmlspecialchars($this->con->error, ENT_QUOTES, 'UTF-8') . '");
                        window.location.href = "../pages/register.php";
                      </script>';
            }

            $stmt->close();
        } else {
            echo '<script>
                    alert("Please fill in all required fields.");
                    window.location.href = "../pages/register.php";
                  </script>';
        }
    }
}
?>