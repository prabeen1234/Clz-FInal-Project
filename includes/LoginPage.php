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
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="../css/style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>Admin Login</title>
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .login-container {
                    background: #fff;
                    padding: 30px;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 400px;
                    box-sizing: border-box;
                    text-align: center;
                }
                .login-container h2 {
                    margin: 0 0 20px;
                    font-size: 26px;
                    color: #333;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                .form-group {
                    margin-bottom: 20px;
                    position: relative;
                }
                .form-group label {
                    font-weight: 600;
                    display: block;
                    margin-bottom: 8px;
                    text-align: left;
                    color: #666;
                }
                .form-group input[type="text"],
                .form-group input[type="password"] {
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 50px;
                    font-size: 16px;
                    color: #333;
                    padding-left: 40px;
                    background: #f9f9f9;
                    box-sizing: border-box;
                    transition: all 0.3s;
                }
                .form-group input[type="text"]:focus,
                .form-group input[type="password"]:focus {
                    border-color: #667eea;
                    background: #fff;
                    outline: none;
                }
                .form-group .input-icon {
                    position: absolute;
                    left: 15px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 18px;
                    color: #667eea;
                }
                .form-group input[type="submit"] {
                    width: 100%;
                    padding: 14px;
                    border: none;
                    border-radius: 50px;
                    background: #667eea;
                    color: white;
                    font-size: 18px;
                    font-weight: 600;
                    text-transform: uppercase;
                    cursor: pointer;
                    transition: background 0.3s, box-shadow 0.3s;
                }
                .form-group input[type="submit"]:hover {
                    background: #764ba2;
                    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                }
                .message {
                    background: #ffdddd;
                    color: #d9534f;
                    padding: 12px;
                    border-radius: 50px;
                    margin-bottom: 20px;
                    border: 1px solid #f5c6cb;
                    text-align: center;
                    font-size: 16px;
                }
                .back-button {
                    position: absolute;
                    top: 15px;
                    left: 15px;
                    text-decoration: none;
                    font-size: 18px;
                    color: #764ba2;
                    display: flex;
                    align-items: center;
                    background: #f1f1f1;
                    padding: 10px 15px;
                    border-radius: 50px;
                    transition: background 0.3s;
                }
                .back-button:hover {
                    background: #667eea;
                    color: #fff;
                }
                .back-button i {
                    margin-right: 8px;
                    font-size: 20px;
                }
            </style>
        </head>
        <body>
            <a href="/blood/index.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
            <div class="login-container">
                <h2>Admin Login</h2>
                <?php if ($this->message): ?>
                    <div class="message"><?php echo htmlspecialchars($this->message); ?></div>
                <?php endif; ?>
                <form action="admin_login.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-lock input-icon"></i>
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
