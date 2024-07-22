<?php
session_start();
session_unset();
session_destroy();
header("Location: ../blood/index.php");
exit();
?>
