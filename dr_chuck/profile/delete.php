<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit;
}

$profile_id = $_POST['profile_id'] ?? $_GET['profile_id'] ?? null;

if ($profile_id === null) {
    die('Missing profile_id');
}

try {
    $stmt = $pdo->prepare(
        'SELECT profile_id, first_name, last_name
         FROM Profile
         WHERE profile_id = :profile_id'
    );
    $stmt->execute(['profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load profile');
}

if ($profile === false) {
    die('Bad value for profile_id');
}

if (isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :profile_id');
        $stmt->execute(['profile_id' => $profile_id]);
        $_SESSION['success_message'] = 'Profile deleted';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error_message'] = 'Unable to delete profile';
    }
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
    <h1>Deleting Profile</h1>
    <div class="notice">
        <?php error_message() ?>
    </div>
    <div class="detail-row">
        <span class="label">First Name</span>
        <span><?= htmlentities($profile['first_name']) ?></span>
    </div>
    <div class="detail-row">
        <span class="label">Last Name</span>
        <span><?= htmlentities($profile['last_name']) ?></span>
    </div>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= htmlentities($profile_id) ?>">
        <div class="form-actions">
            <input type="submit" name="delete" value="Delete">
            <input type="submit" name="cancel" value="Cancel">
        </div>
    </form>
</div>
</body>
</html>
