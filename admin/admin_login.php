<?php
session_start();
include '../includes/Config.php';
include '../includes/AdminLogin.php';
include '../includes/LoginPage.php';

$db = new Database();
$login = new AdminLogin($db);
$page = new LoginPage($login);

$page->display();
$db->close();
?>
