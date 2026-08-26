<?php
require_once 'utils.php';
session_start();

if (!isset($_SESSION['email'])) {
    die('Not logged in');
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
    <h1>Editing Automobile</h1>
    <?php error_message() ?>
    <form method="post">
        <label for="make">
            Make <input type="text" name="make">
        </label>
        <br>
        <label for="model">
            Model <input type="text" name="model">
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
