<?php
// DEBUG
print_r($_REQUEST);
// DEBUG


$error_message = '';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == null || $password == null) {
        $error_message = "User name and password are required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email must have an at-sign (@)";
    } else if (!check_password($password)) {
        $error_message = "Incorrect password";
    } else {
        die(header('location: autos.php?email=' . urlencode($email)));
    }
}

function check_password($password) {
    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
    return hash('md5', $salt.$password) === $stored_hash;
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
    <p style="color: red;"><?= $error_message ?></p>
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
