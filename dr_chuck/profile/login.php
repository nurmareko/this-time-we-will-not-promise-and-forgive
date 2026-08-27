<?php
require_once 'utils.php';
require_once 'pdo.php';
session_start();

if (isset($_POST['cancel'])) {
    die(header('location:index.php'));
}

if (isset($_POST['email']) && isset($_POST['pass'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    if ($email == null || $password == null) {
        $_SESSION['error_message'] = "User name and password are required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Email must have an at-sign (@)";
    } else if (!($user_id = check_password($email, $password))) {
        $_SESSION['error_message'] = "Incorrect password";
    } else {
        $_SESSION['user_id'] = $user_id;
        die(header('location:index.php'));
    }
}

function check_password($email, $password) {
    global $pdo;
    $salt = 'XyZzy12*_';

    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die('sorry we cant connect to our database at the moment');
    }

    if ($user === false) {
        return false;
    }

    return hash('md5', $salt . $password) === $user['password'] ?? $user['user_id'];
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
