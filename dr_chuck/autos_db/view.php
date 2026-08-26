<?php
session_start();
require_once 'pdo.php';

$email = '';

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

$email = $_SESSION['email'];

// Retrieving data
try {
    $stmt = $pdo->query('SELECT * FROM autos');
    $automobiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo('Unable to load automobiles');
}

function display_automobiles($automobiles) {
    echo('<h2>Automobiles</h2>');
    echo('<ul>');

    foreach ($automobiles as $automobile) {
        $year = htmlentities($automobile['year']);
        $make = htmlentities($automobile['make']);
        $mileage = htmlentities($automobile['mileage']);

        echo "<li>$year $make / $mileage</li>";
    }

    echo('</ul>');
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
    <a href="add.php">Add New</a>
    |
    <a href="logout.php">Logout</a>
    <?php display_automobiles($automobiles) ?>
</body>
</html>
