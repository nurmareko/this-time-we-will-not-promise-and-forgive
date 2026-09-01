<?php
require_once 'pdo.php';
require_once 'utils.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: error.php?type=access');
    exit;
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
    $positions = postedPositions();
    $education = postedEducation();
    $position_validation = validatePositions($positions);
    $education_validation = validateEducation($education);

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
    } else if ($education_validation !== true) {
        $_SESSION['error_message'] = $education_validation;
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
            insertPositions($pdo, $profile_id, $positions);
            insertEducation($pdo, $profile_id, $education);

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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <title>4070ffb0</title>
    <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
<div class="page">
    <h1>Adding Profile</h1>
    <div class="notice">
        <?php error_message() ?>
    </div>
    <form method="post">
        <div class="field">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlentities($first_name) ?>">
        </div>
        <div class="field">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlentities($last_name) ?>">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?= htmlentities($email) ?>">
        </div>
        <div class="field">
            <label for="headline">Headline</label>
            <input type="text" id="headline" name="headline" value="<?= htmlentities($headline) ?>">
        </div>
        <div class="field">
            <label for="summary">Summary</label>
            <textarea id="summary" name="summary" rows="8" cols="80"><?= htmlentities($summary) ?></textarea>
        </div>
        <div class="entry-group">
            <div class="entry-row">
                <span class="label">Education</span>
                <input type="button" id="addEducation" value="+">
            </div>
            <div id="educationFields"></div>
        </div>
        <div class="entry-group">
            <div class="entry-row">
                <span class="label">Position</span>
                <input id="addPosition" type="button" value="+">
            </div>
            <div id="positionFields"></div>
        </div>
        <div class="form-actions">
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    var positionCount = 0
    var positionNext = 0
    var educationCount = 0
    var educationNext = 0

    $('#addPosition').click(function () {
        if (positionCount >= 9) {
            alert('Maximum of nine position entries exceeded');
            return;
        }

        positionCount++;
        positionNext++;
        var positionId = 'position' + positionNext;
        var position = $('<div>').attr('id', positionId).addClass('entry-block');
        var row = $('<div>').addClass('entry-row').text('Year: ');
        row.append($('<input>', {
            type: 'text',
            name: 'year' + positionNext
        }));
        row.append(' ');
        row.append($('<input>', {
            type: 'button',
            value: '-'
        }).click(function () {
            $('#' + positionId).remove();
            positionCount--;
        }));
        position.append(row);
        position.append($('<textarea>', {
            name: 'desc' + positionNext,
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
      educationNext++

      const educationId = 'education' + educationNext
      const educationField = $('<div>').attr('id', educationId).addClass('entry-block')

      const row = $('<div>').addClass('entry-row').text('Year: ')
      row.append($('<input>', {
          type: 'text',
          name: 'edu_year' + educationNext
      }))
      row.append(' ')

      row.append($('<input>', {
          type: 'button',
          value: '-'
      }).click(() => {
          $('#' + educationId).remove()
          educationCount--
      }))

      const row2 = $('<div>').addClass('entry-row').text('School: ')
      const schoolInput = $('<input>', {
        type: 'text',
        size: 80,
        name: 'edu_school' + educationNext,
        class: 'school'
      })
      row2.append(schoolInput)

      educationField.append(row)
      educationField.append(row2)

      $('#educationFields').append(educationField)

      schoolInput.autocomplete({
          source: 'school.php'
      })
    })

});
</script>
</body>
</html>
