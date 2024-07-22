
<?php
class Register {
private $con;

public function __construct($con) {
$this->con = $con;
}

public function handleRegistration($data) {
// Validate input
if (
isset($data['username'], $data['password'], $data['fullname'], $data['age'], $data['sex'],
$data['bloodgroup'], $data['mobile'], $data['email'], $data['town'],
$data['state'], $data['latitude'], $data['longitude'], $data['role'])
&& !empty($data['username']) && !empty($data['password']) && !empty($data['fullname'])
&& !empty($data['age']) && !empty($data['sex']) && !empty($data['bloodgroup'])
&& !empty($data['mobile']) && !empty($data['email']) && !empty($data['town'])
&& !empty($data['state']) && !empty($data['latitude']) && !empty($data['longitude']) && !empty($data['role'])
) {
// Sanitize input data
$username = $this->con->real_escape_string($data['username']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);
$fullname = $this->con->real_escape_string($data['fullname']);
$age = (int)$data['age'];
$gender = $this->con->real_escape_string($data['sex']);
$bloodgroup = $this->con->real_escape_string($data['bloodgroup']);
$mobile = $this->con->real_escape_string($data['mobile']);
$email = $this->con->real_escape_string($data['email']);
$town = $this->con->real_escape_string($data['town']);
$state = $this->con->real_escape_string($data['state']);
$latitude = (float)$data['latitude'];
$longitude = (float)$data['longitude'];
$role = $this->con->real_escape_string($data['role']);

// Prepare SQL statement
$query = "INSERT INTO users (username, password, fullname, age, sex, bloodgroup, mobile, email, town, state, latitude, longitude, role)
VALUES ('$username', '$password', '$fullname', $age, '$gender', '$bloodgroup', '$mobile', '$email', '$town', '$state', $latitude, $longitude, '$role')";

if ($this->con->query($query)) {
// Registration successful
echo '<script>
alert("Registration successful");
window.location.href = "../index.php";
</script>';
} else {
// Registration failed
echo '<script>
alert("Registration failed: ' . htmlspecialchars($this->con->error, ENT_QUOTES, 'UTF-8') . '");
</script>';
}
} else {
// Missing or empty fields
echo '<script>
alert("Please fill and select all required fields");
window.location.href = "../index.php";
</script>';
}
}
}
?>