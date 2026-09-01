<?php
require_once 'pdo.php';
require_once 'utils.php';

$profile_id = $_GET['profile_id'] ?? null;

if ($profile_id === null) {
    header('Location: error.php?type=missing');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT first_name, last_name, email, headline, summary
        FROM Profile
        WHERE profile_id = :profile_id');
    $stmt->execute(['profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile === false) {
        header('Location: error.php?type=not-found');
        exit;
    }

    $positions = loadPositions($pdo, $profile_id);
    $education = loadEducation($pdo, $profile_id);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: error.php?type=database');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>4070ffb0</title>
</head>
<body>
<div class="page">
    <h1>Profile information</h1>

    <div class="detail-row">
        <span class="label">First Name</span>
        <span><?= htmlentities($profile['first_name']) ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Last Name</span>
        <span><?= htmlentities($profile['last_name']) ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Email</span>
        <span><?= htmlentities($profile['email']) ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Headline</span>
        <span><?= htmlentities($profile['headline']) ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Summary</span>
        <span><?= nl2br(htmlentities($profile['summary'])) ?></span>
    </div>

    <?php if (!empty($education)): ?>
        <div>
            <p><strong>Education</strong></p>
            <ul>
                <?php foreach ($education as $entry): ?>
                    <li>
                        <?= htmlentities($entry['year']) ?>:
                        <?= htmlentities($entry['name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($positions)): ?>
        <div>
            <p><strong>Positions</strong></p>
            <ul>
                <?php foreach ($positions as $position): ?>
                    <li>
                        <?= htmlentities($position['year']) ?>:
                        <?= htmlentities($position['description']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="footer-actions">
        <a href="index.php">Done</a>
    </div>
</div>
</body>
</html>
