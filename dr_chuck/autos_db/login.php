<?php
session_start();

// DEBUG
// print_r($_REQUEST);
// print_r($_SESSION);
// DEBUG

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

function check_password($password) {
    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
    return hash('md5', $salt.$password) === $stored_hash;
}

function flash_message() {
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        echo("<p style=\"color: red;\">$error_message</p>");
        unset($_SESSION['error_message']);
    }
}
?>

<!----------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Drestayumna Nurmareko</title>
</head>
<body>
    <h1>Please Log In</h1>
    <?php flash_message() ?>
    <form method="post">
        <label for="email">
            Email <input type="text" name="email">
        </label>
        <br>
        <label for="password">
            Password <input type="password" name="password">
        </label>
        <br>
        <input type="submit" value="Login">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
