<?php
require_once 'pdo.php';
session_start();

$logged_in = isset($_SESSION['user_id']);
$profiles = [];

try {
    $stmt = $pdo->query('SELECT * FROM Profile');
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('sorry we cant connect to our database at the moment');
}

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
                    <td><?= htmlentities($profile['first_name'] . ' ' . $profile['last_name']) ?></td>
                    <td><?= htmlentities($profile['headline'])  ?></td>
                    <td>
                        <p>
                        <a href=<?= "edit.php?profile_id=?" . $profile['profile_id'] ?>>Edit</a>
                        /
                        <a href=<?= "delete.php?profile_id=?" . $profile['profile_id'] ?>>Delete</a>
                        </p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if ($logged_in): ?>
        <a href="add.php">Add New Entry</a>
    <?php endif; ?>
</html>
