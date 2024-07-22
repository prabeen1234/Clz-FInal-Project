<?php
session_start();
require '../includes/Config.php';


// Check if ID is provided
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $userId = mysqli_real_escape_string($con, $_POST['id']);

    // Prepare and execute deletion query
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        if (mysqli_stmt_execute($stmt)) {
            // Successfully deleted
            header('Location: manage_user.php');
            exit();
        } else {
            die("Deletion failed: " . mysqli_stmt_error($stmt));
        }
    } else {
        die("Statement preparation failed: " . mysqli_error($con));
    }
} else {
    // ID not provided
    header('Location: manage_user.php');
    exit();
}
?>
