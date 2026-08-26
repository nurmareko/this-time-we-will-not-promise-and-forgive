<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

$email = '';

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

$email = $_SESSION['email'];

if (isset($_POST['cancel'])) {
    die(header('location:index.php'));
}

// Saving data
if (
    isset($_POST['make']) &&
    isset($_POST['model']) &&
    isset($_POST['year']) &&
    isset($_POST['mileage'])
) {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];

    if ($make === '') {
        $_SESSION['error_message'] = 'Make is required';
        die(header('location:add.php'));
    } else if ($model === '') {
        $_SESSION['error_message'] = 'Model is required';
        die(header('location:add.php'));
    } else if (!(is_numeric($year) && is_numeric($mileage))) {
        $_SESSION['error_message'] = 'Mileage and year must be numeric';
        die(header('location:add.php'));
    } else {
        $data = [
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'mileage' => $mileage
        ];
        $sql = '
            INSERT INTO autos (make, model, year, mileage)
            VALUES (:make, :model, :year, :mileage)
        ';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $_SESSION['success_message'] = 'Record inserted';
            die(header('location:index.php'));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to insert record';
            die(header('locatoin:add.php'));
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
