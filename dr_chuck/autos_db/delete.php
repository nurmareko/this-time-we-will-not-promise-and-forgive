<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();


?>

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
        <<input type="hidden" name="autos_id" value="<?= $autos_id ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
