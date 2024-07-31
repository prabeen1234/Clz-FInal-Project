<?php
class LoginPage {
    private $login;
    private $message;

    public function __construct($login) {
        $this->login = $login;
        $this->message = '';
    }

    public function handlePostRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->login->authenticate($username, $password)) {
                $_SESSION['admin'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $this->message = "Invalid username or password.";
            }
        }
    }

    public function display() {
        $this->handlePostRequest();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/style.css">
            <title>Admin Login</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background: linear-gradient(135deg, #f8b500, #f6d365);
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .login-container {
                    background: #fff;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                    width: 100%;
                    max-width: 400px;
                }
                .login-container h2 {
                    margin-top: 0;
                    margin-bottom: 20px;
                    font-size: 24px;
                    text-align: center;
                }
                .form-group {
                    margin-bottom: 15px;
                }
                .form-group label {
                    display: block;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .form-group input[type="text"],
                .form-group input[type="password"] {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .form-group input[type="submit"] {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    background: #f8b500;
                    color: #fff;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                .form-group input[type="submit"]:hover {
                    background: #f6d365;
                }
                .message {
                    background: #dff0d8;
                    color: #3c763d;
                    padding: 10px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                    border: 1px solid #d6e9c6;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h2>Admin Login</h2>
                <?php if ($this->message): ?>
                    <div class="message"><?php echo htmlspecialchars($this->message); ?></div>
                <?php endif; ?>
                <form action="admin_login.php" method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}
?>
