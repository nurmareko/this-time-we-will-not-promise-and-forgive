<?php
require_once 'pdo.php';

// DEBUG
print_r($_REQUEST);
// DEBUG

// Context
$email = '';
$error_message = '';
$success_message = '';
// Context

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    die('email parameter missing');
}

if (isset($_POST['logout'])) {
    die(header('location:index.php'));
}

if (
    isset($_POST['make']) &&
    isset($_POST['year']) &&
    isset($_POST['mileage'])
) {
    $make = $_POST['make'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];

    if ($make === '') {
        $error_message = 'Make is required';
    }

function display_message($message, $type) {
    if ($type === 'error') {
        echo("<p style=\"color: red;\">$message</p>");
    } else {
        echo("<p style=\"color: green;\">$message</p>");
    }
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

    <?php
    if ($error_message !== '') {
        display_message($error_message, 'error');
    } else if ($success_message !== '') {
        display_message($success_message, 'success');
    }
    ?>

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
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
