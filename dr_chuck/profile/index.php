<?php

$logged_in = isset($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>4070ffb0</title>
</head>
<body>
    <h1>Resume Registry</h1>

    <?php if ($logged_in): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Please log in</a>
    <?php endif; ?>

    <p>table</p>

    <?php if ($logged_in): ?>
        <a href="add.php">Add New Entry</a>
    <?php endif; ?>
</html>
