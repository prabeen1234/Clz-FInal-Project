
<?php
session_start();
include 'includes/Config.php'; 
include 'includes/Login.php';

$error = null; 
$success = null; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($email && $password) {
        $db = new Database();
        $login = new Login($db);

        if ($login->userLogin($email, $password)) {
            $role = $_SESSION['role'];
            $success = "Login successful! Redirecting...";
            echo "<script>window.location.href = '../blood/{$role}s/{$role}_dashboard.php?login=success';</script>";
        } else {
            $error = "Invalid email, password, or account not approved";
        }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
.login-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px;
    border: 1px solid #007bff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    border-radius: 20px;
    background-color: #ffffff; /* Default background color */
    transition: background 0.3s ease, box-shadow 0.3s ease; /* Added transition for smooth effect */
}

.login-container:hover {
    background: linear-gradient(135deg, #87ceeb, #00bfff); /* Light blue gradient on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Increased shadow on hover */
}



.back-button {
    display: inline-block;
    margin-bottom: 20px;
    color: #007bff;
    text-decoration: none;
}

.back-button i {
    margin-right: 5px;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.form-group i {
    margin-right: 8px;
}

.links {
    margin-top: 20px;
}

.links a {
    display: block;
    margin: 20px 20px;
    text-decoration: none;
    color: red;
}

.links a i {
    margin-right: 5px;
}

 body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
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
        .toast {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: #ffffff; /* Light background for better visibility */
    color: #333; /* Dark text color for contrast */
    text-align: center;
    border-radius: 2px;
    padding: 16px;
    position: fixed;
    z-index: 1;
    left: 50%;
    bottom: 30px;
    font-size: 17px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Slight shadow for better visibility */
}

.toast.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2s;
    animation: fadein 0.5s, fadeout 0.5s 2s;
}

@-webkit-keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}

.toast.success {
    background-color: #d4edda; /* Light green for success */
    color: #155724; /* Dark green text for success */
}

.toast.error {
    background-color: #f8d7da; /* Light red for error */
    color: #721c24; /* Dark red text for error */
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
    border-radius: 8px;
    font-weight: bold;
    color: red;
    background-color: rgba(255, 255, 255, 0.2);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.links a:hover {
    background-color: #87ceeb; /* Sky blue background color on hover */
    color: blueviolet; /* White text color on hover */
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
}    </style>
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

            <?php if ($error): ?>
                showToast("<?php echo $error; ?>", "error");
            <?php elseif ($success): ?>
                showToast("<?php echo $success; ?>", "success");
            <?php endif; ?>
        });

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'toast show ' + type;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.className = toast.className.replace('show', '');
                setTimeout(() => document.body.removeChild(toast), 500);
            }, 3000);
        }
    </script>
</head>
<body>

<div class="login-container">
    <a href="index.php" class="back-button">
        <i class="fas fa-arrow-circle-left"></i> Back
    </a>
    <h2><i class="fas fa-user"></i> Login</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Login">
        </div>
    </form>

    <div class="links">
        <a href="pages/register.php"><i class="fas fa-user-plus"></i> Register</a>
        <a href="pages/forgot_password.php"><i class="fas fa-unlock"></i> Forgot Password?</a>
    </div>
</div>


</body>
</html>
