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
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :profile_id');
    $stmt->execute(['profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load profile');
}

if ($profile === false) {
    die('Bad value for profile_id');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $headline = $_POST['headline'] ?? '';
    $summary = $_POST['summary'] ?? '';

    if (
        $first_name === '' ||
        $last_name === '' ||
        $email === '' ||
        $headline === '' ||
        $summary === ''
    ) {
        $_SESSION['error_message'] = 'All fields are required';
    } else if (strpos($email, '@') === false) {
        $_SESSION['error_message'] = 'Email address must contain @';
    } else {
        $sql = 'UPDATE Profile
                SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    headline = :headline,
                    summary = :summary
                WHERE profile_id = :profile_id';

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'headline' => $headline,
                'summary' => $summary,
                'profile_id' => $profile_id
            ]);
            $_SESSION['success_message'] = 'Profile updated';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to update profile';
        }
    }
} else {
    $first_name = $profile['first_name'];
    $last_name = $profile['last_name'];
    $email = $profile['email'];
    $headline = $profile['headline'];
    $summary = $profile['summary'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4070ffb0</title>
    <script
        src="https://code.jquery.com/jquery-4.0.0.slim.min.js"
        integrity="sha256-8DGpv13HIm+5iDNWw1XqxgFB4mj+yOKFNb+tHBZOowc="
        crossorigin="anonymous"></script>
</head>
<body>
    <h1>Editing Profile</h1>
    <?php error_message() ?>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= htmlentities($profile_id) ?>">
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlentities($first_name) ?>">
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlentities($last_name) ?>">
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?= htmlentities($email) ?>">
        </p>
        <p>
            <label for="headline">Headline:</label>
            <input type="text" id="headline" name="headline" value="<?= htmlentities($headline) ?>">
        </p>
        <p>
            <label for="summary">Summary:</label>
            <textarea id="summary" name="summary" rows="8" cols="80"><?= htmlentities($summary) ?></textarea>
        </p>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
