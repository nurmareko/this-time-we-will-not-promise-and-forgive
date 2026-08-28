<?php
require_once 'pdo.php';

$profile_id = $_GET['profile_id'] ?? null;

if ($profile_id === null) {
    die('Missing profile_id');
}

try {
    $stmt = $pdo->prepare('SELECT first_name, last_name, email, headline, summary
        FROM Profile
        WHERE profile_id = :profile_id');
    $stmt->execute(['profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile === false) {
        die('Bad value for profile_id');
    }

    $stmt = $pdo->prepare('SELECT year, description
        FROM Position
        WHERE profile_id = :profile_id
        ORDER BY rank');
    $stmt->execute(['profile_id' => $profile_id]);
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load profile');
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
<div class="container">
    <h1>Profile information</h1>
    <p>
        First Name:
        <?= htmlentities($profile['first_name']) ?>
    </p>
    <p>
        Last Name:
        <?= htmlentities($profile['last_name']) ?>
    </p>
    <p>
        Email:
        <?= htmlentities($profile['email']) ?>
    </p>
    <p>
        Headline:<br>
        <?= htmlentities($profile['headline']) ?>
    </p>
    <p>
        Summary:<br>
        <?= nl2br(htmlentities($profile['summary'])) ?>
    </p>

    <?php if (!empty($positions)): ?>
        <p>Positions:</p>
        <ul>
            <?php foreach ($positions as $position): ?>
                <li>
                    <?= htmlentities($position['year']) ?>:
                    <?= htmlentities($position['description']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="index.php">Done</a></p>
</div>
</body>
</html>
