<?php
session_start();
include 'includes/Config.php'; // Include your configuration file if any
include 'includes/Login.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($email && $password) {
        $db = new Database();
        $login = new Login($db);

        if ($login->userLogin($email, $password)) {
            $role = $_SESSION['role'];
            if ($role == 'donor') {
                header("Location: ../blood/donors/donor_dashboard.php");
            } elseif ($role == 'user') {
                header("Location: ../blood/users/user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }

        // Close the database connection
        $db->close();
    } else {
        $error = "Email and password are required";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #00bcd4, #2196f3);
            color: #333;
        }
        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 360px;
            max-width: 90%;
            text-align: center;
        }
        h2 {
            font-size: 26px;
            margin-bottom: 25px;
            color: #2196f3;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin: 0 auto;
            display: block;
        }
        .form-group input[type="submit"] {
            background-color: #00bcd4;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .form-group input[type="submit"]:hover {
            background-color: #007bff;
        }
        .error {
            color: #d9534f;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .links {
            margin-top: 20px;
            font-size: 14px;
        }
        .links a {
            color: red;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
        <div class="links">
            <p>Not Registered? <a href="pages/register.php"><b>Register Now</b></a></p>
            <p>Forgot Password? <a href="pages/forgot_password.php"><b>Click Here</b></a></p>
        </div>
    </div>
</body>
</html>
