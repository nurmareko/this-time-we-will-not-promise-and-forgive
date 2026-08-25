<?php
require_once 'pdo.php';

// DEBUG
print_r($_REQUEST);
// DEBUG

// Context
$email = '';
$error_message = '';
$success_message = '';
$automobiles = [];
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
    } else if (!(is_numeric($year) && is_numeric($mileage))) {
        $error_message = 'Mileage and year must be numeric';
    } else {
        $data = [
            'make' => $make,
            'year' => $year,
            'mileage' => $mileage
        ];
        $sql = '
            INSERT INTO autos (make, year, mileage)
            VALUES (:make, :year, :mileage)
        ';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success_message = 'Record inserted';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error_message = 'Unable to insert record';
        }
    }
}

try {
    $stmt = $pdo->query('SELECT make, year, mileage FROM autos');
    $automobiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error_message = 'Unable to load automobiles';
}

function display_message($message, $type) {
    if ($type === 'error') {
        echo("<p style=\"color: red;\">$message</p>");
    } else {
        echo("<p style=\"color: green;\">$message</p>");
    }
}

function display_automobiles($automobiles) {
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

    <h2>Automobiles</h2>
    <?php display_automobiles($automobiles) ?>
</body>
</html>
