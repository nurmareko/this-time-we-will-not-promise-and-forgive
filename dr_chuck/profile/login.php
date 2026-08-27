<?php
require_once 'utils.php';
require_once 'pdo.php';
session_start();

if (isset($_POST['cancel'])) {
    die(header('location:index.php'));
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == null || $password == null) {
        $_SESSION['error_message'] = "User name and password are required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Email must have an at-sign (@)";
    } else if (!($check = check_password($password))) {
        error_log("Login fail ". $email . " $check");
        $_SESSION['error_message'] = "Incorrect password";
    } else {
        error_log("Login success ".$email);
        $_SESSION['email'] = $email;
        die(header('location:index.php'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>4070ffb0</title>
</head>
<body>
    <p>Please Log In</p>
    <?php error_message() ?>
    <form method="post">
        <label for="email">Email <input type="email" name="email"></label>
        <br>
        <label for="pass">Password <input type="password" name="pass"></label>
        <br>
        <input type="submit" value="Login">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
