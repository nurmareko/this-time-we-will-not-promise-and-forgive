<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

if (isset($_POST['cancel'])) {
    die(header('location:index.php'));
}

$auto_id = $_GET['auto_id'];

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
        die(header("location:edit.php?auto_id=$auto_id"));
    } else if ($model === '') {
        $_SESSION['error_message'] = 'Model is required';
        die(header("location:edit.php?auto_id=$auto_id"));
    } else if (!(is_numeric($year) && is_numeric($mileage))) {
        $_SESSION['error_message'] = 'Mileage and year must be numeric';
        die(header("location:edit.php?auto_id=$auto_id"));
    }

    $sql = '
        UPDATE autos
        SET make = :make, model = :model, year = :year, mileage = :mileage
        WHERE autos_id = :auto_id
    ';
    $data = [
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'mileage' => $mileage,
        'auto_id' => $auto_id
    ];

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        $_SESSION['success_message'] = 'Record updated';
        die(header('location:index.php'));
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error_message'] = 'Unable to update record';
        die(header("location:edit.php?auto_id=$auto_id"));
    }
}

try {
    $stmt = $pdo->prepare('SELECT * FROM autos WHERE autos_id = :auto_id');
    $stmt->execute(['auto_id' => $auto_id]);
    $automobile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load automobile');
}

if ($automobile === false) {
    die('Automobile not found');
}

$make = htmlentities($automobile['make']);
$model = htmlentities($automobile['model']);
$year = htmlentities($automobile['year']);
$mileage = htmlentities($automobile['mileage']);
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
        <input type="hidden" name="auto_id" value="<?= $auto_id ?>">
        <label for="make">
            Make <input type="text" id="make" name="make" value="<?= $make ?>">
        </label>
        <br>
        <label for="model">
            Model <input type="text" id="model" name="model" value="<?= $model ?>">
        </label>
        <br>
        <label for="year">
            Year <input type="text" id="year" name="year" value="<?= $year ?>">
        </label>
        <br>
        <label for="mileage">
            Mileage <input type="text" id="mileage" name="mileage" value="<?= $mileage ?>">
        </label>
        <br>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
