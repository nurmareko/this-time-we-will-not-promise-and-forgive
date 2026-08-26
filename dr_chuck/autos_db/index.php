<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

function automobiles_table() {
    if (!isset($_SESSION['email'])) {
        return;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SELECT * FROM autos");
        $automobiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Cannot retrieve data: ' . $e->getMessage());
        die('Sorry, cannot retrieve data at the moment');
    }

    if (empty($automobiles)) {
        echo('<p>no data found!</p>');
    } else {
        echo("<h2>Automobiles</h2>");
        echo("<table border='1'>");
        echo("<tr><th>Year</th><th>Make</th><th>Model</th><th>Mileage</th><th>Action</th></tr>");

        foreach ($automobiles as $automobile) {
            $auto_id = htmlentities($automobile['autos_id']);
            $year = htmlentities($automobile['year']);
            $make = htmlentities($automobile['make']);
            $model = htmlentities($automobile['model']);
            $mileage = htmlentities($automobile['mileage']);

            echo('<tr>');
            echo("<td>$year</td>");
            echo("<td>$make</td>");
            echo("<td>$model</td>");
            echo("<td>$mileage</td>");
            echo("<td><a href='edit.php?auto_id=$auto_id'>Edit</a> / "
                . "<a href='delete.php?auto_id=$auto_id'>Delete</a></td>");
            echo('</tr>');
        }

        echo('</table>');
    }
}

function page_content() {
    automobiles_table();
    if (!isset($_SESSION['email'])) {
        echo('<a href="login.php">Please Log In</a>');
    } else {
        echo('<a href="add.php">Add New Entry</a><br>');
        echo('<a href="logout.php">Logout</a>');
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
    <h1>Welcome to Autos Database</h1>
    <?php success_message() ?>
    <?php page_content() ?>
</body>
</html>
