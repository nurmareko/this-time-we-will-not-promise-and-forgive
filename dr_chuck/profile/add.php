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

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$headline = $_POST['headline'] ?? '';
$summary = $_POST['summary'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        $first_name === '' ||
        $last_name === '' ||
        $email === '' ||
        $headline === '' ||
        $summary === ''
    ) {
        $_SESSION['error_message'] = 'All fields are required';
        header('Location: add.php');
        exit;
    } else if (strpos($email, '@') === false) {
        $_SESSION['error_message'] = 'Email address must contain @';
        header('Location: add.php');
        exit;
    } else {
        $sql = 'INSERT INTO Profile
                    (user_id, first_name, last_name, email, headline, summary)
                VALUES
                    (:user_id, :first_name, :last_name, :email, :headline, :summary)';

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'headline' => $headline,
                'summary' => $summary
            ]);
            $_SESSION['success_message'] = 'Profile added';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to add profile';
            header('Location: add.php');
            exit;
        }
    }
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
    <h1>Adding Profile</h1>
    <?php error_message() ?>
    <form method="post">
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
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
