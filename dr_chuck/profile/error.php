<?php
$errors = [
    'access' => [
        'status' => 403,
        'title' => 'Access denied',
        'message' => 'You need to log in before you can use this page.'
    ],
    'missing' => [
        'status' => 400,
        'title' => 'Missing information',
        'message' => 'The page did not receive all the information it needs.'
    ],
    'not-found' => [
        'status' => 404,
        'title' => 'Profile not found',
        'message' => 'That profile does not exist or may have been removed.'
    ],
    'database' => [
        'status' => 500,
        'title' => 'Something went wrong',
        'message' => 'We could not load the requested information. Please try again later.'
    ]
];

$type = $_GET['type'] ?? 'not-found';
$error = $errors[$type] ?? $errors['not-found'];
http_response_code($error['status']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?= $error['status'] ?> - <?= htmlentities($error['title']) ?></title>
</head>
<body>
<div class="page">
    <h1><?= $error['status'] ?> - <?= htmlentities($error['title']) ?></h1>
    <p><?= htmlentities($error['message']) ?></p>

    <div class="footer-actions">
        <a href="index.php">Back to home</a>
        <?php if ($type === 'access'): ?>
            <a href="login.php">Log in</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
