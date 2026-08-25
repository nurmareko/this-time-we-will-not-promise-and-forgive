<?php
require_once 'pdo.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    die('email parameter missing');
}

if (isset($_POST['logout'])) {
    die(header('location:index.php'));
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
    <form action="#" method="post">
        <label for="">
            Make <input type="text">
        </label>
        <br>
        <label for="">
            Year <input type="text">
        </label>
        <br>
        <label for="">
            Mileage <input type="text">
        </label>
        <br>
        <input type="submit" value="Add">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
