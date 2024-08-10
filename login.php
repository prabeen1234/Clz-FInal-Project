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
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
        }
        .login-container {
            background: lightblue;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            height: 90vh;
            max-width: 600px;
            max-height: 600px;
            color: #fff;
            text-align: center;
            position: relative;
            transition: background-color 0.3s ease;
        }
        .login-container:hover {
            background: linear-gradient(135deg, #2196f3, #ff4081);
        }
        h2 {
            font-size: 55px; /* Large heading size */
            margin-bottom: 10px;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: red;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            font-size: 30px; /* Large label size */
            margin-bottom: 10px;
            color: black; /* Label color */
            font-weight: bold;
        }
        .form-group input {
            width: calc(100% - 20px);
            padding: 12px; /* Input padding */
            border: 2px solid #ccc;
            border-radius: 8px; /* Rounded corners */
            font-size: 18px; /* Input text size */
            margin: 0 auto;
            display: block;
            color: #333;
            font-weight: bold;
        }
        .form-group input[type="submit"] {
            background-color: #ffeb3b; /* Button color */
            color: #333;
            border: none;
            cursor: pointer;
            font-size: 20px; /* Button text size */
            padding: 14px; /* Button padding */
            margin-top: 20px;
            width: 100%;
            display: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #ff4081; /* Button hover color */
            color: #fff;
        }
        .error {
            color: #d9534f;
            font-size: 16px; /* Error text size */
            margin-bottom: 20px;
        }
        .links {
            margin-top: 30px;
            font-size: 24px; /* Link text size */
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            color: black;
        }
        .links a {
            color: #ffeb3b; /* Link color */
            text-decoration: none;
            padding: 8px;
            color: red;
            border-radius: 8px;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.2);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .links a:hover {
            background-color: #ff4081; /* Link hover background */
            color: #fff; /* Link hover text color */
        }
        .form-group input.invalid {
            border-color: #ff4081;
        }
        /* New Back to Home Button */
        .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    text-decoration: none;
    color: #333;
    font-size: 18px;
    display: flex;
    align-items: center;
    background: #f1f1f1;
    padding: 8px 12px;
    border-radius: 50px;
    transition: background 0.3s;
}

.back-button:hover {
    background: #007bff;
    color: #fff;
}

.back-button i {
    margin-right: 8px;
    font-size: 20px;
}
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitButton = document.querySelector('input[type="submit"]');

            function validateForm() {
                if (emailInput.value && passwordInput.value) {
                    submitButton.style.display = 'block';
                } else {
                    submitButton.style.display = 'none';
                }
            }

            emailInput.addEventListener('input', validateForm);
            passwordInput.addEventListener('input', validateForm);

            validateForm(); // Run initial check
        });
    </script>
    
</head>
<body>

    <div class="login-container">
    <a href="index.php" class="back-button">
            <i>←</i> Back
        </a>
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
            <div class="register-link">
                <p>Not Registered? <a href="pages/register.php"><b>Register Now</b></a></p>
            </div>
            <div class="forgot-password-link">
                <p>Forgot Password? <a href="pages/forgot_password.php"><b>Click Here</b></a></p>
            </div>
        </div>
    </div>
</body>
</html>
