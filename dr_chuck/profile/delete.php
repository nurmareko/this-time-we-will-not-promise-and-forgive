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
    <title>4070ffb0</title>
</head>
<body>
    <h1>Deleting Profile</h1>
    <?php error_message() ?>
    <p>
        First Name: <?= htmlentities($profile['first_name']) ?>
    </p>
    <p>
        Last Name: <?= htmlentities($profile['last_name']) ?>
    </p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= htmlentities($profile_id) ?>">
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
