<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

if (!isset($_SESSION['email'])) {
    die('Not logged in');
}

$autos_id = $_GET['auto_id'];

if (isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM autos WHERE autos_id = :autos_id');
        $stmt->execute(['autos_id' => $autos_id]);

        if ($stmt->rowCount() === 0) {
            die('Automobile not found');
        }

        $_SESSION['success_message'] = 'Record deleted';
        die(header('location:index.php'));
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die('Unable to delete automobile');
    }
}

try {
    $stmt = $pdo->prepare('SELECT make FROM autos WHERE autos_id = :autos_id');
    $stmt->execute(['autos_id' => $autos_id]);
    $automobile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load automobile');
}

if ($automobile === false) {
    die('Automobile not found');
}

$make = htmlentities($automobile['make']);

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
    <p>Confirm: deleting <?= $make ?></p>
    <form method="post">
        <input type="hidden" name="autos_id" value="<?= $autos_id ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
