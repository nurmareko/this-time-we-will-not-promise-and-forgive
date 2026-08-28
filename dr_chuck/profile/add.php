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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_validation = validatePositions();

    if (
        trim($first_name) === '' ||
        trim($last_name) === '' ||
        trim($email) === '' ||
        trim($headline) === '' ||
        trim($summary) === ''
    ) {
        $_SESSION['error_message'] = 'All fields are required';
    } else if (strpos($email, '@') === false) {
        $_SESSION['error_message'] = 'Email address must contain @';
    } else if ($position_validation !== true) {
        $_SESSION['error_message'] = $position_validation;
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('INSERT INTO Profile
                (user_id, first_name, last_name, email, headline, summary)
                VALUES
                (:user_id, :first_name, :last_name, :email, :headline, :summary)');
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'headline' => $headline,
                'summary' => $summary
            ]);

            $profile_id = $pdo->lastInsertId();
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
            $_SESSION['success_message'] = 'Profile added';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($e->getMessage());
            $_SESSION['error_message'] = 'Unable to add profile';
        }
    }

    header('Location: add.php');
    exit;
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
        <p>
            Education: <input type="button" id="addEducation" value="+">
        </p>
        <div id="educationFields"></div>
        <p>
            Position: <input id="addPosition" type="button" value="+">
        </p>
        <div id="positionFields"></div>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>

<script>
$(document).ready(function () {
    var positionCount = 0
    var educationCount = 0

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
            value: '-'
        }).click(function () {
            $('#' + positionId).remove();
        }));
        position.append(row);
        position.append($('<textarea>', {
            name: 'desc' + positionCount,
            rows: 8,
            cols: 80
        }));
        $('#positionFields').append(position);
    })

    $('#addEducation').click(() => {
      if (educationCount >= 9) {
          alert('Maximum of nine education entries exceeded')
          return;
      }
      educationCount++

      const educationId = 'education' + educationCount
      const educationField = $('<div>').attr('id', educationId)

      const row = $('<p>').text('Year: ')
      row.append($('<input>'), {
          type: 'text',
          name: 'eduYear' + educationCount
      })
      row.append(' ')

      educationField.append(row)

      $('#educationFields').append(educationField);
    })
});
</script>
</body>
</html>
