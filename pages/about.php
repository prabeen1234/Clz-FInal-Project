<?php
ob_start();
session_start();

require '../includes/Config.php';
require '../includes/Navbar.php';
require '../includes/Footer.php';

class AboutPage {
    private $navbar;
    private $footer;

    public function __construct() {
        $this->footer = new Footer();
    }

    public function render() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f0f2f5;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    flex-direction: column;
                    height: 100vh;
                }

                #comname {
                    text-align: center;
                    font-size: 2em;
                    margin: 20px;
                }

                #nav {
                    background-color: #343a40;
                    color: #fff;
                    padding: 10px 0;
                }

                #nav ul {
                    list-style-type: none;
                    padding: 0;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                }

                #nav li {
                    margin: 0 15px;
                }

                #nav a {
                    color: #fff;
                    text-decoration: none;
                    font-size: 1.2em;
                }

                #nav a.active, #nav a:hover {
                    background-color: rgba(255,0,0,0.5);
                    padding: 10px 15px;
                    border-radius: 5px;
                }

                .myFrontPic {
                    width: 100%;
                    height: auto;
                }

                .about-content {
                    margin: 20px auto;
                    padding: 20px;
                    max-width: 900px;
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .about-content h3 {
                    font-size: 1.5em;
                    color: #333;
                    margin-bottom: 20px;
                }

                .about-content p {
                    color: #555;
                    line-height: 1.6;
                    margin-bottom: 20px;
                }

                .about-content h4 {
                    color: #666;
                    margin-top: 20px;
                    font-size: 1.2em;
                }

                hr {
                    border: 0;
                    border-top: 1px dotted #9e9e9e;
                    margin: 20px 0;
                }

                #footer {
                    background-color: #343a40;
                    color: #fff;
                    text-align: center;
                    padding: 20px;
                    margin-top: auto;
                }
            </style>
            <title>About Us</title>
        </head>
        <body>
            <?php $this->navbar->render(); ?>

            <div id="comname">
                <i class="fa fa-balance-scale" aria-hidden="true"></i><br><br>BLOOD <b>DONATION</b>
            </div>
            <div id="nav" class="col-12">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a class="active" href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="register.php">Be A Donor</a></li>
                    <li><a href="change_details.php">Change Details</a></li>
                    <li><a href="find_blood.php">Find Donor</a></li>
                    <?php
                    if (isset($_SESSION['sess_user_id']) && !empty($_SESSION['sess_user_id'])) {
                        echo '<li style="background-color: rgba(255,0,0,0.5);"><a href="logout.php">Logout</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <img class="myFrontPic col-12" src="images/img2.jpg" alt="Blood Donation" style="height: 350px;">

            <div class="about-content">
                <h3><i>Whenever you see a successful business, someone once made a courageous decision.</i> ~Peter F. Drucker</h3>
                <p>I am a student of RR college Kathmandu. This portal had been developed to bring technology to help humanity. We tried to use best contemporary technologies in delivering a promising web portal to bring together all the blood donors in Gwalior, thereby fulfilling every blood request in our city.</p>
                <h4>Why blood donation</h4>
                <p>Blood donation is a major concern to the society as donated blood is lifesaving for individuals who need it. Blood is scarce. There is a shortage to active blood donors to meet the need of increased blood demand. Blood donation as a therapeutic exercise. Globally, approximately 80 million units of blood are donated each year.</p>
                <p>One of the biggest challenges to blood safety particularly is accessing safe and adequate quantities of blood and blood products. Safe supply of blood and blood components is essential, to enable a wide range of critical care procedures to be carried out in hospitals. Good knowledge about blood donation practices is not transforming in donating blood. Interactive awareness on blood donation should be organized to create awareness and opportunities for blood donation. Blood donation could be therefore recommended that voluntary blood donations as often as possible may be therapeutically beneficial to the donors in terms of thrombotic complications and efficient blood flow mechanisms. This is also a plus for blood donation campaigns.</p>
                <hr>
                <h1>LOCATION</h1>
            </div>

            <div id="footer">
                <?php $this->footer->render(); ?>
            </div>

        </body>
        </html>
        <?php
    }
}

// Create an instance of AboutPage and render it
$page = new AboutPage();
$page->render();
?>
