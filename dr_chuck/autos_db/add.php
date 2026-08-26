<?php
session_start();

$email = '';

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

if (isset($_POST['cancel'])) {
    die(header('location:view.php'));
}

$email = $_SESSION['email'];

function error_message() {
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        echo "<p style=\"color: red;\">$error_message</p>";
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
    <h1>Tracking Autos for <?= $email ?></h1>
    <?php error_message() ?>
    <form method="post">
        <label for="make">
            Make <input type="text" name="make">
        </label>
        <br>
        <label for="year">
            Year <input type="text" name="year">
        </label>
        <br>
        <label for="mileage">
            Mileage <input type="text" name="mileage">
        </label>
        <br>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
