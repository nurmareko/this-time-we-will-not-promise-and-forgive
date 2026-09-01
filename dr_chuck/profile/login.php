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
        header('Location: error.php?type=database');
        exit;
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
	<link rel="stylesheet" href="style.css">
	<title>4070ffb0</title>
    <script>
        function doValidate() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('pass').value;

            if (email === '' || password === '') {
                alert('Both fields must be filled out');
                return false;
            }

            if (!email.includes('@')) {
                alert('Invalid email address');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
<div class="page">
    <h1>Please Log In</h1>
    <div class="notice">
        <?php error_message() ?>
    </div>
    <form method="post">
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="field">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="pass">
        </div>
        <div class="form-actions">
            <input type="submit" onclick="return doValidate();" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </div>
    </form>
</div>
</body>
</html>
