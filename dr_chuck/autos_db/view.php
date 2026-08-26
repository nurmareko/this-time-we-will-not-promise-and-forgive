<?php
session_start();

$email = '';

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

$email = $_SESSION['email'];

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
    <a href="add.php">Add New</a>
    |
    <a href="logout.php">Logout</a>
</body>
</html>
