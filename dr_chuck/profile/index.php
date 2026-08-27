<?php

$logged_in = isset($_SESSION['user_id']);
$profiles = [];
$name = '';
$headline = '';
$user_id = '';

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

    <?php if (empty($profiles)): ?>
        <p>no data to show</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Headline</th>
                <th>Action</th>
            </tr>
            <?php foreach ($profiles as $profile): ?>
                <tr>
                    <td><?= $name ?></td>
                    <td><?= $headline ?></td>
                    <td>
                        <a href=<?= "edit.php?user_id=?" . $user_id ?>>Edit</a>
                        <a href=<?= "delete.php?user_id=?" . $user_id ?>>Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if ($logged_in): ?>
        <a href="add.php">Add New Entry</a>
    <?php endif; ?>
</html>
