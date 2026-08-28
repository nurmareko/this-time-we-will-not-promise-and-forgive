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
    $position_validation = validatePositions();

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
    } else if ($position_validation !== true) {
        $_SESSION['error_message'] = $position_validation;
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('UPDATE Profile
                SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    headline = :headline,
                    summary = :summary
                WHERE profile_id = :profile_id');
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'headline' => $headline,
                'summary' => $summary,
                'profile_id' => $profile_id
            ]);

            $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :profile_id');
            $stmt->execute(['profile_id' => $profile_id]);

            $position_stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
                VALUES (:profile_id, :rank, :year, :description)');
            $rank = 1;

            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['year' . $i], $_POST['desc' . $i])) {
                    continue;
                }

                $position_stmt->execute([
                    'profile_id' => $profile_id,
                    'rank' => $rank,
                    'year' => $_POST['year' . $i],
                    'description' => $_POST['desc' . $i]
                ]);
                $rank++;
            }

            $pdo->commit();
            $_SESSION['success_message'] = 'Profile updated';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to update profile';
        }
    }

    header('Location: edit.php?profile_id=' . rawurlencode((string) $profile_id));
    exit;
}

$first_name = $profile['first_name'];
$last_name = $profile['last_name'];
$email = $profile['email'];
$headline = $profile['headline'];
$summary = $profile['summary'];

try {
    $stmt = $pdo->prepare('SELECT year, description
        FROM Position
        WHERE profile_id = :profile_id
        ORDER BY rank');
    $stmt->execute(['profile_id' => $profile_id]);
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to load positions');
}

function validatePositions() {
    for ($i = 1; $i <= 9; $i++) {
        $has_year = isset($_POST['year' . $i]);
        $has_description = isset($_POST['desc' . $i]);

        if (!$has_year && !$has_description) {
            continue;
        }

        if (
            !$has_year ||
            !$has_description ||
            trim($_POST['year' . $i]) === '' ||
            trim($_POST['desc' . $i]) === ''
        ) {
            return 'All fields are required';
        }

        if (!is_numeric($_POST['year' . $i])) {
            return 'Year must be numeric';
        }
    }

    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4070ffb0</title>
    <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
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
        <p>
            Position: <input id="addPosition" type="button" value="+">
        </p>
        <div id="positionFields">
            <?php foreach ($positions as $index => $position): ?>
                <?php $position_number = $index + 1; ?>
                <div id="position<?= $position_number ?>">
                    <p>
                        Year:
                        <input type="text"
                            name="year<?= $position_number ?>"
                            value="<?= htmlentities($position['year']) ?>">
                        <input type="button"
                            class="removePosition"
                            data-position-id="position<?= $position_number ?>"
                            value="-">
                    </p>
                    <textarea
                        name="desc<?= $position_number ?>"
                        rows="8"
                        cols="80"><?= htmlentities($position['description']) ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>

<script>
$(document).ready(function () {
    var positionCount = <?= count($positions) ?>;

    $('#positionFields').on('click', '.removePosition', function () {
        $('#' + $(this).data('position-id')).remove();
    });

    $('#addPosition').click(function () {
        if (positionCount >= 9) {
            alert('Maximum of nine position entries exceeded');
            return;
        }

        positionCount++;
        var positionId = 'position' + positionCount;
        var position = $('<div>').attr('id', positionId);
        var row = $('<p>').text('Year: ');
        row.append($('<input>', {
            type: 'text',
            name: 'year' + positionCount
        }));
        row.append(' ');
        row.append($('<input>', {
            type: 'button',
            class: 'removePosition',
            'data-position-id': positionId,
            value: '-'
        }));
        position.append(row);
        position.append($('<textarea>', {
            name: 'desc' + positionCount,
            rows: 8,
            cols: 80
        }));
        $('#positionFields').append(position);
    });
});
</script>
</body>
</html>
